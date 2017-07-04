<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

define('REVENT_SCHEMA_VERSION',  '0.1.dev');
define('FIELDSET_WEIGHT_OFFSET', 100);

/**
 * Provides functions for the registration field selection
 */
class CRM_Revent_RegistrationFields {

  protected static $extension_version = NULL;

  protected $event = NULL;


  public function __construct($event_identification) {
    // LOAD event
    $event_fields_to_load = array(
      'remote_event_registration.registration_fields' => 1,
      'title'                                         => 1
      );
    CRM_Revent_CustomData::resolveCustomFields($event_fields_to_load);
    $event_identification['return'] = implode(',', array_keys($event_fields_to_load));
    $this->event = civicrm_api3('Event', 'getsingle', $event_identification);
    CRM_Revent_CustomData::labelCustomFields($this->event, 3);
  }

  /**
   * Create a JSON representation of the
   *  event registration form
   */
  public function renderEventRegistrationForm() {
    $rendered_fields = array();

    // step 1: render all groups
    $groups = $this->event['remote_event_registration.registration_fields'];
    foreach ($groups as $group_id) {
      $rendered_fields += $this->renderGroup($group_id);
    }


    // step 2: apply customisation
    // TODO

    return array(
      'schema_version'    => REVENT_SCHEMA_VERSION,
      'extension_version' => self::getExtensionVersion(),
      'fields'            => $rendered_fields,
      'values'            => NULL);
  }

  /**
   * render one group
   */
  public function renderGroup($group_id) {
    if (preg_match("#^(?P<type>\w+)-(?P<id>\d+)$#", $group_id, $match)) {
      switch ($match['type']) {
        case 'BuiltInProfile':
          return $this->renderCustomBuiltinProfile($match['id']);

        case 'CustomGroup':
        case 'OptionGroup': // legacy
          return $this->renderCustomGroup($match['id']);

        default:
          throw new Exception("Unknown field set type '{$match['type']}'!");
      }
    } else {
      throw new Exception("Unknown field set id '{$group_id}'!");
    }
  }

  /**
   * render a custom group
   */
  public function renderCustomGroup($custom_group_id) {
    $result = array();
    $fields = civicrm_api3('CustomField', 'get', array(
      'check_permissions' => 0,
      'custom_group_id'   => $custom_group_id,
      'option.limit'      => 0,
      'sequential'        => 0,
      ));
    // render fields
    foreach ($fields['values'] as $custom_field) {
      $key = "custom_{$custom_field['id']}";
      $result[$key] = array(
        'type'         => strtolower($custom_field['data_type']),
        'label'        => $custom_field['label'],
        'group'        => CRM_Revent_CustomData::getGroupName($custom_group_id),
        'group_title'  => CRM_Revent_CustomData::getGroupName($custom_group_id),
        'verification' => '',
        'weight'       => $custom_field['weight'],
        'mandatory'    => $custom_field['is_required'],
        'length'       => $custom_field['text_length'],
      );
    }

    // resolve fields
    CRM_Revent_CustomData::cacheCustomFields(array_keys($fields['values']));
    CRM_Revent_CustomData::labelCustomFields($result, 4);

    // set (resolved) name
    foreach ($result as $key => &$value) {
      $value['name'] = $key;
    }

    return $result;
  }

  /**
   * render a custom group
   */
  public function renderCustomBuiltinProfile($built_in_id) {
    // TODO: implement
    return array();
  }


  /****************************************************
   *          Field Synchronisation (static)          *
   ***************************************************/

  /**
   * Update the remote_registration_fields option group to reflect
   *  the current custom groups, profiles, etc.
   */
  public static function synchroniseFields() {
    // load current custom groups
    $desired_list = self::getActiveFieldSets() + self::getProfiles();

    // now, get all current values
    $query = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'remote_registration_fields',
      'option.limit'    => 0,
      'sequential'      => 1,
      'return'          => 'id,weight,value,label,is_active',
      ));
    $current_list = array();
    foreach ($query['values'] as $optionValue) {
      $current_list[$optionValue['value']] = $optionValue;
    }

    // first: iterate through wanted entries
    foreach ($desired_list as $key => $value) {
      if (isset($current_list[$key])) {
        // enable the entry if not enabled
        $current_value = $current_list[$key];
        if (!$current_value['is_active']) {
          civicrm_api3('OptionValue', 'create', array(
            'id'        => $current_value['id'],
            'is_active' => 1));
        }
        unset($current_list[$key]);
      } else {
        $value['option_group_id'] = 'remote_registration_fields';
        civicrm_api3('OptionValue', 'create', $value);
      }
    }

    // iterate through the (remaining) entries that we don't want any more
    foreach ($current_list as $key => $value) {
      // these should be disabled:
      if ($value['is_active']) {
        civicrm_api3('OptionValue', 'create', array(
          'id'        => $value['id'],
          'is_active' => 0));
      }
    }
  }

  /**
   * Get all eligible Participant CustomGroups
   */
  public static function getActiveFieldSets() {
    $query = civicrm_api3('CustomGroup', 'get', array(
      'extends'         => 'Participant',
      'is_active'       => 1,
      'return'          => 'title,weight,id,name'
    ));

    $result = array();
    foreach ($query['values'] as $customGroup) {
      $key = "CustomGroup-{$customGroup['id']}";
      $result[$key] = array(
        'value'  => $key,
        'weight' => $customGroup['weight'] + FIELDSET_WEIGHT_OFFSET,
        'label'  => $customGroup['title'],
        'name'   => $customGroup['name'],
      );
    }

    return $result;
  }


  /**
   * Get all eligible Profiles
   */
  public static function getProfiles() {
    return array(
      'BuiltInProfile-1' => array(
        'value'  => 'BuiltInProfile-1',
        'weight' => 1,
        'label'  => ts('Email und Sprache', array('domain' => 'de.systopia.revent')),
        'name'   => 'profile_1',
      ),
      'BuiltInProfile-2' => array(
        'value'  => 'BuiltInProfile-2',
        'weight' => 2,
        'label'  => ts('Name und Email Pflicht', array('domain' => 'de.systopia.revent')),
        'name'   => 'profile_2',
      )
    );
  }



  /***************************************************
   *                         HELPER                  *
   ***************************************************/

  /**
   * Get the extension version
   */
  public static function getExtensionVersion() {
    if (self::$extension_version === NULL) {
      $mapper = CRM_Extension_System::singleton()->getMapper();
      $info   = $mapper->keyToInfo('de.systopia.revent');
      self::$extension_version = $info->version;
    }
    return self::$extension_version;
  }
}
