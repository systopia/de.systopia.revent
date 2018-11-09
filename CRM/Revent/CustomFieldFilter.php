<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2018 SYSTOPIA                            |
| Author: P. Batroff (batroff@systopia.de)               |
| Source: http://www.systopia.de/                        |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

/**
 * Class CRM_Revent_CustomFieldFilter
 */
class CRM_Revent_CustomFieldFilter {

  /**
   * lookup cache
   * array (event_id => array(
   *    custom_field_id => 1)
   * )
   * @var
   */
  private static $event_to_custom_field_ids;

  /**
   * filters custom fields for the params. This will be called before Participant::create
   * @param &$params
   */
  public static function filter_custom_fields(&$params) {
    if (!isset(self::$event_to_custom_field_ids[$params['event_id']])) {
      self::get_event_custom_fields($params['event_id']);
    }
    $event_id = $params['event_id'];
    $pattern = "/custom_(?P<custom_field_id>[0-9]+).+/";
    foreach($params as $key => $value) {
      preg_match($pattern, $key, $matches);
      if(isset($matches['custom_field_id'])) {
        // check if parsed custom_field_id is in cache, if not unset from params
        if (!isset(self::$event_to_custom_field_ids[$event_id][$matches['custom_field_id']])) {
          unset($params[$key]);
        }
      }
    }
    // unset invalid custom_field in $params['custom']
    foreach ($params['custom'] as $key => $value) {
      if (!isset(self::$event_to_custom_field_ids[$event_id][$key])) {
        unset($params['custom'][$key]);
      }
    }
  }

  /**
   * Get Custom Field IDs for given Event
   * @param $event_id
   *
   * @throws \CiviCRM_API3_Exception
   */
  private static function get_event_custom_fields($event_id) {
    $result = civicrm_api3('RemoteRegistration', 'get_active_groups', array(
      'sequential' => 1,
      'event_id' => $event_id,
    ));
    if ($result['is_error'] == '1') {
      return;
    }
    foreach ($result['values'] as $custom_group_id) {
      $custom_group_result = civicrm_api3('CustomField', 'get', array(
        'sequential' => 1,
        'custom_group_id' => $custom_group_id,
      ));
      foreach ($custom_group_result['values'] as $custom_field) {
        self::$event_to_custom_field_ids[$event_id][$custom_field['id']] = 0;
      }
    }
  }

}