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
    $this->ctx->log->info('Applying update 0.9.6 - changing size of the registration_fields column');
    CRM_Core_DAO::executeQuery('ALTER TABLE civicrm_value_remote_event_registration MODIFY registration_fields VARCHAR(2048);');
    return TRUE;
  }

  /**
   * Make sure custom field changes are applied
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_0908() {
    $this->ctx->log->info('Applying update 0.9.8 - changes in custom fields');
    $customData = new CRM_Revent_CustomData('de.systopia.revent');
    $customData->syncCustomGroup(__DIR__ . '/../../resources/custom_group_registration_address.json');
    return TRUE;
  }

  /**
   * Extend length of civicrm_value_remote_event_registration.registration_fields to varchar(2048)
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1000() {
    $this->ctx->log->info('Applying update 1.0.beta2 - changing size of the registration_fields column');
    CRM_Core_DAO::executeQuery('ALTER TABLE civicrm_value_remote_event_registration MODIFY registration_fields VARCHAR(2048);');
    try {
      CRM_Core_DAO::executeQuery('ALTER TABLE log_civicrm_value_remote_event_registration MODIFY registration_fields VARCHAR(2048);');
    } catch (Exception $e) {
      // most likely cause: logging not enabled - that's fine then
    }
    return TRUE;
  }

  /**
   * Make sure custom field changes are applied
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_1300() {
    $this->ctx->log->info('Applying update 1.3 - changes in custom fields');
    $customData = new CRM_Revent_CustomData('de.systopia.revent');
    $customData->syncCustomGroup(__DIR__ . '/../../resources/custom_group_remote_event_registration.json');
    return TRUE;
  }

}
