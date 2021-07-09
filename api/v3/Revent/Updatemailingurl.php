<?php
use CRM_Revent_ExtensionUtil as E;

/**
 * Revent.UpdateMailingUrl API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_revent_updatemailingurl_spec(&$spec) {
  $spec['last_check']['api.required'] = 0;
}

/**
 * Revent.UpdateMailingUrl API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_revent_updatemailingurl($params) {

  try {
    $last_check = NULL;
    if (isset($params['last_check'])) {
      $last_check = $params['last_check'];
    }
    $UrlUpdater = new CRM_Revent_UpdateMailingURLs($last_check);
    $UrlUpdater->run();

    return civicrm_api3_create_success("Update Successful. Updated " . $UrlUpdater->get_changed_url_counter() . " Mailing URLs.");
  } catch (API_Exception $e) {
    return civicrm_api3_create_error("Failed to update Mailing URLs. Error message: " . $e->getMessage());
  }
}
