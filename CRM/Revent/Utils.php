<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: P. Batroff (batroff@systopia.de)               |
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

/**
 * Collection of upgrade steps.
 */
class CRM_Revent_Utils {

  public static $API_ERROR_REFERENCE = [
    "PARTICIPANT_NOT_FOUND" => '404'
  ];


  /**
   * Count the number ob participant for given $event_id
   * Necessary because of massive performance Problems with
   * Participant.getcount(event_id).
   *
   * @param $event_id
   * @return int
   * @throws CiviCRM_API3_Exception
   */
  public static function get_participant_count($event_id) {
    try {
      $query_string = "
        SELECT
          event_id                                    AS event_id,
          COUNT(event_id)                             AS count
        FROM civicrm_participant
        WHERE event_id = {$event_id}
        AND status_id IN ('1', '2','5','6','14')";

      $query = CRM_Core_DAO::executeQuery($query_string);

      while ($query->fetch()) {
        return $query->count;
      }

    } catch (Exception $e) {
      CRM_Core_Error::debug_log_message("[CRM_Revent_Utils::get_participant_count] Error while getting the number of participants for Event {$event_id}");
      throw new CiviCRM_API3_Exception("[CRM_Revent_Utils::get_participant_count] Error while getting the number of participants for Event {$event_id}");
    }
  }

  /**
   * Generate a RemoteEvent conform API3 error
   *
   * @param $error_message
   *
   *
   */
  public static function createApi3Error($error_message, $error_reference = NULL)
  {
    return civicrm_api3_create_error($error_message, [
      'status_messages' => [
        [
          'severity' => 'error',
          'reference' => empty($error_reference) ? "" : $error_reference,
        ]
      ]
    ]);
  }
}
