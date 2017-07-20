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
 * Get an event based on the give data
 */
function civicrm_api3_remote_event_get($params) {
  $result = array();

  if (!empty($params['id'])) {
    // first priority is BY ID
    $result = civicrm_api3('Event', 'get', array(
      'check_permissions' => 0,
      'id'                => $params['id']));

  } elseif (!empty($params['external_identifier'])) {
    // first priority is BY external_identifier
    $query = array(
      'check_permissions'                           => 0,
      'remote_event_connection.external_identifier' => $params['external_identifier']);
    CRM_Revent_CustomData::resolveCustomFields($query);
    $result = civicrm_api3('Event', 'get', $query);

  } else {
    // you have to provide something
    return civicrm_api3_create_error("You have to provide either 'id' or 'external_identifier'");
  }

  // replace custom fields with labels
  CRM_Revent_CustomData::labelCustomFields($result, 3);

  // add CiviCRM URL
  foreach ($result['values'] as $event_id => &$event) {
    $event['civicrm_link'] = CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$event_id}", true);
  }

  return $result;
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_event_get_spec(&$params) {
  $params['external_identifier'] = array(
    'name'         => 'external_identifier',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Event External Identifier',
    );
  $params['id'] = array(
    'name'         => 'id',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'CiviCRM Event ID',
    );
}
