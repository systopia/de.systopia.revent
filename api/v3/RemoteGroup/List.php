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


/**
 * List all eligible groups
 */
function civicrm_api3_remote_group_list($params) {
  $result = array();

  // TODO: restrict to eligible groups
  $result = civicrm_api3('Group', 'get', array(
    'options.limit' => 0));

  // replace custom fields with labels
  CRM_Revent_CustomData::labelCustomFields($result['values'], 2);

  return civicrm_api3_create_success($result['values']);
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_group_list_spec(&$params) {
}
