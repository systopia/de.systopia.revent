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
  unset($params['check_permissions']);
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteGroup.list');
  $result = array();

  // TODO: restrict to eligible groups
  // 21588 only get mailing groups
  $result = civicrm_api3('Group', 'get', [
    'group_type' => "Mailing List",
    'options' => ['limit' => 0],
  ]);
  $groups = $result['values'];

  // replace custom fields with labels
  CRM_Revent_CustomData::labelCustomFields($groups, 2);

  // do some filtering / processing
  foreach ($groups as &$group) {
    // set profile
    $profile_type = CRM_Utils_Array::value('group_fields.profile_type', $group, '');
    switch ($profile_type) {
      default:
      case '1':
        $group['profile'] = 'email';
        break;

      case '2':
        $group['profile'] = 'email_name';
        break;

      case '3':
        $group['profile'] = 'email_name_postal';
        break;
    }

    // remove clutter
    if (isset($group['where_clause']))  unset($group['where_clause']);
    if (isset($group['where_tables']))  unset($group['where_tables']);
    if (isset($group['select_tables'])) unset($group['select_tables']);
    if (isset($group['visibility']))    unset($group['visibility']);
    if (isset($group['created_id']))    unset($group['created_id']);
    if (isset($group['modified_id']))   unset($group['modified_id']);
    if (isset($group['is_reserved']))   unset($group['is_reserved']);

    // #21588 - adding additional filters
    if (isset($group['is_active']))   unset($group['is_active']);
    if (isset($group['group_type']))   unset($group['group_type']);
    if (isset($group['is_hidden']))   unset($group['is_hidden']);

    // #11024 check if show_last_mailing is set. if not, remove mailing_ulr here
    //        then remove show_last_mailing parameter, we don't want/need to expose this
    if (isset($group['group_fields.show_last_mailing']) && !$group['group_fields.show_last_mailing']) {
      unset($group['group_fields.mailing_url']);
    }
    if (isset($group['group_fields.show_last_mailing']))   unset($group['group_fields.show_last_mailing']);

    if (!empty($group['group_fields.display_section'])) {
      try {
        $group['group_fields.display_section_label'] = civicrm_api3('OptionValue', 'getvalue', array(
          'option_group_id' => 'display_section',
          'value'           => $group['group_fields.display_section'],
          'return'          => 'label'));
      } catch (Exception $e) {
        // not found, no problem
        $group['group_fields.display_section_label'] = 'Error';
      }
    }
  }

  return civicrm_api3_create_success($groups);
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_group_list_spec(&$params) {
}
