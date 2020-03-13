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
   *
   * @param $params array participant parameters
   * @throws CiviCRM_API3_Exception
   */
  public static function filter_custom_fields(&$params) {
    // get event ID
    $event_id = CRM_Utils_Array::value('event_id', $params);
    if (empty($event_id) && !empty($params['id'])) {
      $event_id = civicrm_api3('Participant', 'getvalue', ['id' => $params['id'], 'return' => 'event_id']);
    }
    if (empty($event_id)) { // if event_id still not known: bail
      CRM_Core_Error::debug_log_message("Revent: filter_custom_fields() failed, couldn't determine event_id");
      return;
    }

    // get custom fields
    $custom_fields = self::get_event_custom_fields($event_id);

    //
    $pattern = "/custom_(?P<custom_field_id>[0-9]+).+/";
    foreach($params as $key => $value) {
      preg_match($pattern, $key, $matches);
      if(isset($matches['custom_field_id'])) {
        // check if parsed custom_field_id is in cache, if not unset from params
        if (!isset($custom_fields[$matches['custom_field_id']])) {
          unset($params[$key]);
        }
      }
    }
    // unset invalid custom_field in $params['custom']
    foreach ($params['custom'] as $key => $value) {
      if (!isset($custom_fields[$key])) {
        unset($params['custom'][$key]);
      }
    }
  }

  /**
   * Get Custom Field IDs for given Event
   *
   * @param $event_id int event ID
   * @return array list of field IDs
   * @throws CiviCRM_API3_Exception
   */
  private static function get_event_custom_fields($event_id) {
    if (!isset(self::$event_to_custom_field_ids[$event_id])) {
      // result not yet cached, fill:
      self::$event_to_custom_field_ids[$event_id] = [];

      // load all active groups
      $result = civicrm_api3('RemoteRegistration', 'get_active_groups', array(
          'sequential'   => 1,
          'option.limit' => 0,
          'event_id'     => $event_id,
      ));
      $custom_group_ids = $result['values'];

      // load all custom fields
      if (!empty($custom_group_ids)) {
        $custom_fields = civicrm_api3('CustomField', 'get', array(
            'sequential'      => 1,
            'option.limit'    => 0,
            'custom_group_id' => ['IN' => $custom_group_ids],
        ));

        // add all custom field IDs to the list
        foreach ($custom_fields['values'] as $custom_field) {
          self::$event_to_custom_field_ids[$event_id][$custom_field['id']] = 0;
        }
      }
    }

    return self::$event_to_custom_field_ids[$event_id];
  }
}