<?php
use CRM_Revent_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Revent_Upgrader extends CRM_Revent_Upgrader_Base {

  /**
   * Extend length of civicrm_value_remote_event_registration.registration_fields to varchar(2048)
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_0906() {
    $this->ctx->log->info('Applying update 0906 - changing size of the registration_fields column');
    CRM_Core_DAO::executeQuery('ALTER TABLE civicrm_value_remote_event_registration MODIFY registration_fields VARCHAR(2048);');
    return TRUE;
  }

}
