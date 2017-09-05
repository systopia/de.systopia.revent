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
function civicrm_api3_remote_group_unsubscribe($params) {
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteGroup.unsubscribe');

  // copied from civicrm_api3_mailing_event_unsubscribe_create
  $job   = $params['job_id'];
  $queue = $params['event_queue_id'];
  $hash  = $params['hash'];

  if (empty($params['org_unsubscribe'])) {
    // UNSUBSCRIBE FROM GROUPS
    $groups = CRM_Mailing_Event_BAO_Unsubscribe::unsub_from_mailing($job, $queue, $hash);
    return civicrm_api3_create_success($params);

  } else {
    // UNSUBSCRIBE FROM DOMAIN (OPT-OUT)
    $unsubs = CRM_Mailing_Event_BAO_Unsubscribe::unsub_from_domain($job, $queue, $hash);
    if (!$unsubs) {
      return civicrm_api3_create_error('Domain Queue event could not be found');
    }
    return civicrm_api3_create_success($params);
  }
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_group_unsubscribe_spec(&$params) {
  $params['job_id'] = array(
    'api.required' => 1,
    'title' => 'Mailing Job ID',
    'type' => CRM_Utils_Type::T_INT,
  );
  $params['hash'] = array(
    'api.required' => 1,
    'title' => 'Mailing Hash',
    'type' => CRM_Utils_Type::T_STRING,
  );
  $params['event_queue_id'] = array(
    'api.required' => 1,
    'title' => 'Mailing Queue ID',
    'type' => CRM_Utils_Type::T_INT,
  );
  $params['org_unsubscribe'] = array(
    'api.default' => 0,
    'title'       => 'Opt-Out?',
    'type'        => CRM_Utils_Type::T_INT,
  );
}
