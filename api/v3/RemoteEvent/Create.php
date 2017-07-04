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
 * Create a Remote Event with the provided data
 */
function civicrm_api3_remote_event_create($params) {

  $params['remote_event_connection.external_identifier'] = $params['external_identifier'];
  unset($params['external_identifier']);
  CRM_Revent_CustomData::resolveCustomFields($params);
  return civicrm_api3('Event', 'create', $params);
}

/**
 * Create Remote Event
 */
function _civicrm_api3_remote_event_create_spec(&$params) {
  $params['external_identifier'] = array(
    'name'         => 'external_identifier',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Event External Identifier',
    );
  $params['start_date'] = array(
    'name'         => 'start_date',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_DATE,
    'title'        => 'Remote Event start date',
  );
  $params['title'] = array(
    'name'         => 'title',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Remote Event title',
  );
  $params['event_type_id'] = array(
    'name'         => 'event_type_id',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'Remote Event event_type_id',
  );
}