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
  unset($params['check_permissions']);
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteEvent.get');
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

  } elseif(!empty($params['start_date'])) {
    $query = array(
      'check_permissions'                           => 0,
      'start_date' => $params['start_date']);
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
    // get the number of registered participants
    $event['registered_participants'] = civicrm_api3('Participant', 'getcount', [
      'event_id' => $event_id,
      'status_id' => ['IN' => ["Registered", "Attended", "Pending from pay later", "Pending from incomplete transaction", "Partially paid"]],
    ]);
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
  $params['start_date'] = array(
    'name'         => 'start_date',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_DATE,
    'title'        => 'Event Start Date',
  );
}
