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

class CRM_Revent_Config {

  /**
   * get the business (geschäftlich) location type ID
   */
  public static function getBusinessLocationType() {
    return 8;
  }

  /**
   * get the activity type id for the
   */
  public static function getCheckBusinessActivityType() {
    $query = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'activity_type',
      'name'            => 'Geschaeftliche Adresse pruefen',
      'return'          => 'id,value'));
    if ($query['count'] != 1) {
      throw new Exception("Activity type 'Geschaeftliche Adresse pruefen' not found!", 1);
    } else {
      $value = reset($query['values']);
      return $value['value'];
    }
  }
}
