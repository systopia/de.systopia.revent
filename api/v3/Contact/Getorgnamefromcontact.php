<?php
/*-------------------------------------------------------+
| HBS UI Modififications                                 |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres  (endres@systopia.de)                |
| Author: P. Batroff (batroff@systopia.de)               |
| http://www.systopia.de/                                |
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
 * Derive the contact shared address from
 */
function civicrm_api3_contact_getorgnamefromcontact($params) {

  $business_location_typ_id = CRM_Uimods_Config::getBusinessLocationType();
  $contact_result = civicrm_api3('Address', 'get', array(
    'sequential'        => 1,
    'contact_id'        => $params['contact_id'],
    'location_type_id'  => $business_location_typ_id
  ));

  if ($contact_result['is_error'] === '1') {
    throw new API_Exception('Could not find the Contact, or the given contact does not have a shared business address');
  }

  $result = reset($contact_result['values']);

  if (!isset($result['master_id'])){
    throw new API_Exception('The given contact does not have a shared business address');
  }

  $base_address = civicrm_api3('Address', 'getsingle', array(
    'sequential' => 1,
    'id' => $result['master_id'],
  ));

  if ($base_address['is_error'] === '1') {
    throw new API_Exception('Couldn\'t get the Address, this shouldn\'t happen.');
  }

  $orgname_line_1 = CRM_Revent_Config::getOrgnameField(1);
  $orgname_line_2 = CRM_Revent_Config::getOrgnameField(2);

  $master_contact_org = civicrm_api3('Contact', 'getsingle', array(
    'id'         => $base_address['contact_id'],
    'sequential' => 0,
    'return'     => "display_name,{$orgname_line_1},{$orgname_line_2}",
  ));

  $linked_contact_org_address['master']   = CRM_Utils_Array::value('display_name',  $master_contact_org, '');
  $linked_contact_org_address['master_1'] = CRM_Utils_Array::value($orgname_line_1, $master_contact_org, '');
  $linked_contact_org_address['master_2'] = CRM_Utils_Array::value($orgname_line_2, $master_contact_org, '');

  return civicrm_api3_create_success($linked_contact_org_address);
}

/**
 * API3 action specs
 */
function _civicrm_api3_contact_getorgnamefromcontact_spec(&$params) {
  $params['contact_id']['api.required'] = 1;
}
