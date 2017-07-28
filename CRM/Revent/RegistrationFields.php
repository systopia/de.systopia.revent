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

define('REVENT_SCHEMA_VERSION',  '0.2.dev');
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
    CRM_Revent_CustomData::resolveCustomFields($event_identification);
    //$event_identification['return'] = implode(',', array_keys($event_fields_to_load));
    $this->event = civicrm_api3('Event', 'getsingle', $event_identification);
    CRM_Revent_CustomData::labelCustomFields($this->event, 3);
  }

  /**
   * Create a JSON representation of the
   *  event registration form
   *  based on Drupal https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7.x
   */
  public function renderEventRegistrationForm($customised=TRUE) {
    $rendered_fields = array();
    $rendered_groups = array();

    if (empty($this->event['remote_event_registration.registration_fields'])) {
      error_log("no registration fields available, aborting");
      return array();
    }

    // step 1: render all groups
    $groups = $this->event['remote_event_registration.registration_fields'];
    foreach ($groups as $group_id) {
      $rendered_groups[] = $this->renderGroupMetadata($group_id);
      $rendered_fields  += $this->renderGroup($group_id);
    }


    // step 2: apply customisation
    if ($customised) {
      $this->applyCustomisation($rendered_groups, $rendered_fields);
    }

    // step 3: compile result
    return array(
      'schema_version'    => REVENT_SCHEMA_VERSION,
      'extension_version' => self::getExtensionVersion(),
      'fields'            => $rendered_fields,
      'groups'            => $rendered_groups,
      'values'            => NULL);
  }

  /**
   * render one group
   */
  public function renderGroup($group_id) {
    if (preg_match("#^(?P<type>\w+)-(?P<id>\d+)$#", $group_id, $match)) {
      switch ($match['type']) {
        case 'BuiltInProfile':
          return $this->renderCustomBuiltinProfile($group_id);

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
   * Update the customisation data
   */
  public function updateCustomisation($groups, $fields) {
    // first: render the default data without customisation
    $default_metadata = $this->renderEventRegistrationForm(FALSE);

    // diff groups
    $groups_default = $this->indexArray($default_metadata['groups']);
    $groups_submit  = $this->indexArray($groups);
    $groups_diff    = array();
    foreach ($groups_submit as $key => $group) {
      $groups_diff[$key] = array();
      $this->pushDiff($groups_default[$key], $groups_submit[$key], $groups_diff[$key]);
    }

    // diff fields
    $fields_default = $default_metadata['fields'];
    $fields_submit  = $fields;
    $fields_diff    = array();
    foreach ($fields_submit as $key => $field) {
      $fields_diff[$key] = array();
      $this->pushDiff($fields_default[$key], $fields_submit[$key], $fields_diff[$key]);
    }

    // store new serialised data
    $new_custom_serialised = json_encode(array('groups' => $groups_diff, 'fields' => $fields_diff));

    $update = array(
      'entity_id' => $this->event['id'],
      'remote_event_registration.registration_customisations' => $new_custom_serialised
      );

    CRM_Revent_CustomData::resolveCustomFields($update);
    civicrm_api3('CustomValue', 'create', $update);
  }

  /**
   * Update the customisation data
   */
  protected function applyCustomisation(&$groups, &$fields) {
    if (isset($this->event['remote_event_registration.registration_customisations'])) {
      $customisation_raw = $this->event['remote_event_registration.registration_customisations'];
    } else {
      // nothing to do here, we don't have customizations
      return;
    }
    $customisation = json_decode($customisation_raw, TRUE);

    // apply group data
    foreach ($customisation['groups'] as $group_name => $group_data) {
      foreach ($group_data as $key => $value) {
        $groups[$group_name][$key] = $value;
        // error_log("OVERWRITE groups[$group_name][$key] with '$value'");
      }
    }

    // apply field data
    foreach ($customisation['fields'] as $field_name => $field_data) {
      foreach ($field_data as $key => $value) {
        $fields[$field_name][$key] = $value;
        // error_log("OVERWRITE fields[$field_name][$key] with '$value'");
      }
    }
  }

  /**
   * render a custom group
   */
  public function renderCustomBuiltinProfile($custom_group_id) {
    $folder = dirname(__FILE__) . '/../../resources/profiles';
    $group_data = json_decode(file_get_contents("{$folder}/{$custom_group_id}.json"), TRUE);

    // process field data
    $fields = $group_data['fields'];
    foreach ($fields as &$metadata) {
      // resolve custom group
      if (isset($metadata['options']) && !is_array($metadata['options'])) {
        $metadata['option_group_id'] = $metadata['options'];
        $metadata['options'] = $this->getOptions($metadata);
      }

      // add localisation
      $this->renderLocalisation($metadata);
    }

    return $fields;
  }

  /**
   * render the metadata of the group itself
   */
  protected function renderGroupMetadata($custom_group_id) {
    if (preg_match("#^(?P<type>\w+)-(?P<id>\d+)$#", $custom_group_id, $match)) {
      switch ($match['type']) {
        case 'BuiltInProfile':
          $folder = dirname(__FILE__) . '/../../resources/profiles';
          $group_data = json_decode(file_get_contents("{$folder}/{$custom_group_id}.json"), TRUE);

          return array(
            'name'   => $group_data['name'],
            'title'  => $group_data['title'],
            'weight' => $group_data['weight'],
            );

        case 'CustomGroup':
        case 'OptionGroup': // legacy
          $custom_group = civicrm_api3('CustomGroup', 'getsingle', array('id' => $match['id']));
          return array(
            'name'   => $custom_group['name'],
            'title'  => $custom_group['title'],
            'weight' => $custom_group['weight'],
            );

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
  protected function renderCustomGroup($custom_group_id) {
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
      // copy the generic fields
      $metadata = array(
        'title'        => $custom_field['label'],
        'group'        => CRM_Revent_CustomData::getGroupName($custom_group_id),
        'weight'       => $custom_field['weight'],
        'required'     => $custom_field['is_required'],
        'maxlength'    => CRM_Utils_Array::value('text_length', $custom_field, ''),
        'description'  => CRM_Utils_Array::value('help_pre', $custom_field, ''),
      );

      // add the type-specfic fields
      $this->renderType($custom_field, $metadata);

      // localisation
      $this->renderLocalisation($metadata);

      // store in result
      $result[$key] = $metadata;
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
   * Render and add the type specific fields,
   *  in particular 'type', 'validation', 'options'
   *
   */
  protected function renderType($custom_field, &$metadata) {
    switch ($custom_field['html_type']) {
      case 'Text':
        $metadata['type'] = 'textfield';
        $this->renderValidation($custom_field, $metadata);
        break;

      case 'Radio':
      case 'Multi-Select':
        $metadata['type'] = 'checkboxes';
        $metadata['options'] = $this->getOptions($custom_field);
        break;

      case 'Select Date':
        $metadata['type'] = 'date';
        break;

      case 'Autocomplete-Select':
      case 'Select':
        $metadata['type'] = 'select';
        $metadata['options'] = $this->getOptions($custom_field);
        break;

      case 'Link':
        $metadata['type'] = 'textfield';
        $metadata['validation'] = 'url';
        break;

      case 'TextArea':
      case 'RichTextEditor':
        $metadata['type'] = 'textarea';
        $metadata['validation'] = 'string';

      default:
      case 'File':
        $metadata['type'] = 'error';
    }
  }

  /**
   * Render the validation type
   */
  protected function renderValidation($custom_field, &$metadata) {
    switch ($custom_field['data_type']) {
      case 'String':
        $metadata['validation'] = 'string';
        break;

      case 'Boolean':
        $metadata['validation'] = 'bool';
        break;

      case 'Memo':
        $metadata['validation'] = 'string';
        break;

      case 'Int':
        $metadata['validation'] = 'int';
        break;

      case 'Money':
        $metadata['validation'] = 'float';
        break;

      default:
      case 'Date':
      case 'ContactReference':
      case 'File':
        $metadata['type'] = 'error';
        break;
    }
  }

  /**
   * Get options as a key/name map
   */
  protected function getOptions($custom_field) {
    if (empty($custom_field['option_group_id'])) {
      // return a generic map
      return array( '0' => 'No', '1' => 'Yes');
    }

    $options = array();
    $option_query = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => $custom_field['option_group_id'],
      'options.limit'   => 0,
      'is_active'       => 1,
      'return'          => 'value,label'));
    foreach ($option_query['values'] as $option_value) {
      $options[$option_value['value']] = $option_value['label'];
    }
    return $options;
  }

  /**
   * Add a localisation extra data
   */
  protected function renderLocalisation(&$metadata) {
    // FIXM: this is probably not enough...
    $languages = array('de', 'en');
    foreach ($languages as $language) {
      // do some simple fields
      $fields = array('title', 'description', 'options');
      foreach ($fields as $field) {
        if (isset($metadata[$field])) {
          $metadata["{$field}_{$language}"] = $metadata[$field];
        }
      }
    }
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
    $profiles = array();
    $folder = dirname(__FILE__) . '/../../resources/profiles';
    $files  = scandir($folder);
    foreach ($files as $file) {
      if (preg_match('#^(?P<name>BuiltInProfile-\d+).json$#', $file, $match)) {
        $data = json_decode(file_get_contents($folder . '/' . $file), TRUE);
        if ($data) {
          $key = $match['name'];
          $profiles[$key] = array(
            'value'  => $key,
            'label'  => $data['title'],
            'name'   => $match['name'],
            'weight' => $data['weight'],
          );
        } else {
          error_log("Couldn't parse '{$file}'.");
        }
      }
    }
    return $profiles;
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

  /**
   * turns a list array into an indexed one, using the $attribute as index
   */
  protected function indexArray($list, $attribute = 'name') {
    $result = array();
    foreach ($list as $entry) {
      $result[$entry[$attribute]] = $entry;
    }
    return $result;
  }

  /**
   * compares the two array and pushes the diff
   */
  protected function pushDiff($default_data, $submitted_data, &$diff_target_data) {
    if (empty($submitted_data) || empty($default_data)) return;
    $fields = array_keys($submitted_data + $default_data);
    foreach ($fields as $key) {
      $default_value   = CRM_Utils_Array::value($key, $default_data);
      $submitted_value = CRM_Utils_Array::value($key, $submitted_data);

      if ($submitted_value !== NULL) {
        if (is_array($default_value)) {
          $diff_target_data[$key] = array();
          $this->pushDiff($default_value, $submitted_value, $diff_target_data[$key]);

        } else {
          if ($default_value != $submitted_value) {
            $diff_target_data[$key] = $submitted_value;
          }
        }
      }
    }
  }
}
