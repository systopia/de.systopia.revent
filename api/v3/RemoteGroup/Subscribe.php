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
function civicrm_api3_remote_group_subscribe($params) {
  CRM_Revent_APIProcessor::preProcess($params, 'RemoteGroup.subscribe');

  // IF BUSINESS ADDRESS is given, ther should be an organisation
  $location_type_id = CRM_Utils_Array::value('location_type_id', $params);
  if ($location_type_id == CRM_Revent_Config::getBusinessLocationType()) {
    $organisation_id = CRM_Revent_RegistrationProcessor::createOrganisation($params);
    if ($organisation_id) {
      // add this organisation for address sharing
      $params['address_master_contact_id'] = $organisation_id;
    }
  }

  // process 'special' prefix 'ka' (HBS-5606)
  if (!empty($params['prefix_id']) && $params['prefix_id'] == 'ka') {
    unset($params['prefix_id']);
    if (empty($params['gender_id'])) {
      $params['gender_id'] = 3;
    }
  }

  // resolve/create contact
  CRM_Revent_CustomData::resolveCustomFields($params);
  $contact = civicrm_api3('Contact', 'getorcreate', $params);

  // register for each group
  $group_ids = explode(',', $params['group_ids']);
  foreach ($group_ids as $group_id) {
    civicrm_api3('GroupContact', 'create', array(
      'contact_id' => $contact['id'],
      'group_id'   => $group_id));
  }

  // remove opt-out flag
  civicrm_api3('Contact', 'create', array(
    'contact_id' => $contact['id'],
    'is_opt_out' => 0));

  return civicrm_api3_create_success($contact);
}

/**
 * Schedule a Contract modification
 */
function _civicrm_api3_remote_group_subscribe_spec(&$params) {
  $params['group_ids'] = array(
    'name'         => 'group_ids',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'List of group IDs to subscribe to (comma-separated)',
    );
  $params['email'] = array(
    'name'         => 'email',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Participant Email',
    );
}
