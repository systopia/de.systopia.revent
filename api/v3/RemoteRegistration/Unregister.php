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
 * Unegister a contact from the given event via participant_id
 */
function civicrm_api3_remote_registration_unregister($params) {
  unset($params['check_permissions']);
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteRegistration.unregister');
  CRM_Revent_APIProcessor::stripEmptyParameters($params);

  // find the participant
  $participant_search = civicrm_api3('Participant', 'get', array(
    'id'           => $params['participant_id'],
    'return'       => 'id,participant_status_id',
    'option.limit' => 0));
  if (empty($participant_search['id'])) {
    return CRM_Revent_Utils::createApi3Error("Couldn't find registration [{$params['participant_id']}]", CRM_Revent_Utils::$API_ERROR_REFERENCE['PARTICIPANT_NOT_FOUND']);
  }
  if ($participant_search['values'][$participant_search['id']]['participant_status_id'] == '4') {
    // nothing to do here, notify API with Error Code that
    return CRM_Revent_Utils::createApi3Error("Participant [{$params['participant_id']}] already cancelled", CRM_Revent_Utils::$API_ERROR_REFERENCE['PARTICIPANT_ALREADY_CANCELLED']);
  }

  // set status to 'Cancelled'
  civicrm_api3('Participant', 'create', array(
    'id'                    => $params['participant_id'],
    'participant_status_id' => 4, // 'Cancelled'
  ));

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
