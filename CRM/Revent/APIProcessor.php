<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres  (endres@systopia.de)                |
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

define('LOG_ALL_CALLS', '1');


class CRM_Revent_APIProcessor {

  /**
   * common preprocessing of all API calls
   */
  public static function preProcess(&$params, $log_id = NULL) {

    if ($log_id && LOG_ALL_CALLS) {
      CRM_Core_Error::debug_log_message("{$log_id}: " . json_encode($params));
    }

    // resolve REST fields
    CRM_Revent_CustomData::unREST($params);

    // make sure it returns JSON
    $params['json'] = 1;
  }

}