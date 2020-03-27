<?php
/*-------------------------------------------------------+
| SYSTOPIA CUSTOM DATA HELPER                            |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| Source: https://github.com/systopia/Custom-Data-Helper |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Revent_ExtensionUtil as E;

class CRM_Revent_Config {

  private static $orgname_group_id            = NULL;
  private static $orgname_fields              = NULL;

  protected static $jobs = NULL;

  /**
   * get the business (geschäftlich) location type ID
   */
  public static function getBusinessLocationType() {
    return 8;
  }

  /**
   * return the field name of the first field as
   * API parameter, i.e. "custom_xx"
   *
   * @param $number  only supposed to be 1 or 2
   */
  public static function getOrgnameField($number) {
    $fields = self::getOrgnameFields();
    foreach ($fields as $field) {
      if ($field['name'] == "organisation_name_{$number}") {
        return "custom_{$field['id']}";
      }
    }
    return NULL;
  }

  /**
   * get all the fields from the orgname field group
   */
  public static function getOrgnameFields() {
    if (self::$orgname_fields == NULL) {
      $query = civicrm_api3('CustomField', 'get', array(
        'custom_group_id' => self::getOrgnameGroupID(),
        'options'         => array('limit' => 0),
      ));
      self::$orgname_fields = $query['values'];
    }
    return self::$orgname_fields;
  }

  /**
   * Get the list of custom group names that
   * should _not_ be delivered to a remote event registration form
   */
  public static function getLocalCustomGroups() {
    // TODO: config option?
    return array('registration_address');
  }

  /**
   * get CustomGroup ID of the organisation_names
   */
  public static function getOrgnameGroupID() {
    if (self::$orgname_group_id == NULL) {
      $group = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'organisation_name'));
      self::$orgname_group_id = $group['id'];
    }
    return self::$orgname_group_id;
  }

  /**
   * Install a scheduled job if there isn't one already
   */
  public static function installScheduledJob() {
    $jobs = self::getScheduledJobs();
    if (empty($jobs)) {
      // none found? create a new one
      civicrm_api3('Job', 'create', array(
        'api_entity'    => 'RemoteEvent',
        'api_action'    => 'checkdeadline',
        'run_frequency' => 'Daily',
        'scheduled_run_date' => "00:00",
        'name'          => E::ts('Check Registration Deadlines'),
        'description'   => E::ts('Checks registration Deadlines and disables online registration if expired'),
        'is_active'     => '0'));
    }
  }

  /**
   * get all scheduled jobs that trigger the dispatcher
   */
  public static function getScheduledJobs() {
    if (self::$jobs === NULL) {
      // find all scheduled jobs calling Sqltask.execute
      $query = civicrm_api3('Job', 'get', array(
        'api_entity'   => 'RemoteEvent',
        'api_action'   => 'checkdeadline'));
      self::$jobs = $query['values'];
    }
    return self::$jobs;
  }
}
