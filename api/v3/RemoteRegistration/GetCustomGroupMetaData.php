<?php
use CRM_Revent_ExtensionUtil as E;

/**
 * RemoteRegistration.GetCustomGroupMetaData API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_remote_registration_get_custom_group_meta_data_spec(&$spec) {
}

/**
 * RemoteRegistration.GetCustomGroupMetaData API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 * @throws  \CiviCRM_API3_Exception
 */
function civicrm_api3_remote_registration_get_custom_group_meta_data($params) {
  $result = civicrm_api3('CustomField', 'get', array(
    'sequential' => 1,
    'custom_group_id' => "registration_address",
  ));

  if ($result['is_error'] === 1) {
    throw new API_Exception('Registration Address Custom Fields not found.');
  }
  return civicrm_api3_create_success($result['values']);
}
