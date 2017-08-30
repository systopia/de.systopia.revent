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

define('FIELDSET_WEIGHT_OFFSET', 100);

/**
 * Synchronises the remote_registration_fields to the
 * existing custom groups and built-in profiles
 */
class CRM_Revent_RegistrationFieldSynchronisation {

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
      $value['is_active'] = 1; // all fields found are active

      if (isset($current_list[$key])) {
        $current_value = $current_list[$key];
        unset($current_list[$key]); // already exists
        // see if there is a difference
        if (   $current_value['label']     != $value['label']
            || $current_value['weight']    != $value['weight']
            || $current_value['is_active'] != $value['is_active']) {

          // an update is required:
          civicrm_api3('OptionValue', 'create', array(
            'id'        => $current_value['id'],
            'label'     => $value['label'],
            'weight'    => $value['weight'],
            'is_active' => $value['is_active']));
        }
      } else {
        // create a new one
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
      'option.limit'    => 0,
      'is_active'       => 1,
      'is_reserved'     => 0,  // don't include reserved field sets
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
}
