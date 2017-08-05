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
 * Generate a JSON representation of the registration form for
 *  the given event
 */
function civicrm_api3_remote_registration_get_form($params) {
  CRM_Revent_APIProcessor::preProcess($params);

  // first: load the  event
  $event_search = array();
  if (!empty($params['event_id'])) {
    $event_search['id'] = $params['event_id'];
  }

  if (!empty($params['event_external_identifier'])) {
    $event_search['remote_event_connection.external_identifier'] = $params['event_external_identifier'];
  }

  if (empty($event_search)) {
    return civicrm_api3_create_error("You have to provide either 'event_id' or 'event_external_identifier'");
  }

  // step two: pass it to
  $renderer = new CRM_Revent_RegistrationFields($event_search);
  $field_descriptions = $renderer->renderEventRegistrationForm();

  $null = NULL; // needed as a variable below
  return civicrm_api3_create_success(array(), $params, 'RemoteRegistration', 'get_form', $null, $field_descriptions);
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_registration_get_form_spec(&$params) {
  $params['event_external_identifier'] = array(
    'name'         => 'event_external_identifier',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Event External Identifier',
    );
  $params['event_id'] = array(
    'name'         => 'event_id',
    'api.required' => 0,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'CiviCRM Event ID',
    );
}
