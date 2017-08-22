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
  CRM_Revent_APIProcessor::preProcess($params);
  $result = array();

  // TODO: restrict to eligible groups
  $result = civicrm_api3('Group', 'get', array('option.limit' => 0));
  $groups = $result['values'];

  // replace custom fields with labels
  CRM_Revent_CustomData::labelCustomFields($groups, 2);

  // TODO: add profiles (random for now)
  $profiles = array('email', 'email_name', 'email_name_postal');
  foreach ($groups as &$group) {
    $random_index = array_rand($profiles);
    $group['profile'] = $profiles[$random_index];
  }

  return civicrm_api3_create_success($groups);
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_group_list_spec(&$params) {
}
