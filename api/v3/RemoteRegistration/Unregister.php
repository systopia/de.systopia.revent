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
 * Unregister a contact from the given event via participant_id
 */
function civicrm_api3_remote_registration_unregister($params) {
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteRegistration.unregister');
  CRM_Revent_APIProcessor::stripEmptyParameters($params);

  // find the participant
  $participant_search = civicrm_api3('Participant', 'get', array(
    'id'           => $params['participant_id'],
    'return'       => 'id,register_date,event_id,contact_id',
    'option.limit' => 0));
  if (empty($participant_search['id'])) {
    return civicrm_api3_create_error("Couldn't find registration [{$params['participant_id']}]");
  }

  // TODO: check for status?

  // set status to 'Cancelled'
  civicrm_api3('Participant', 'create', [
      'status_id'     => 4, // 'Cancelled'
      'id'            => $params['participant_id'],
      'contact_id'    => $params['contact_id'],
      'event_id'      => $params['event_id'],
      'register_date' => $params['register_date'],
  ]);

  return civicrm_api3('Participant', 'getsingle', array('id' => $params['participant_id']));
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_registration_unregister_spec(&$params) {
  $params['participant_id'] = array(
    'name'         => 'participant_id',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'Registration ID',
    'description'  => 'The registration ID (aka participant_id)',
    );
}
