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

require_once 'revent.civix.php';

/**
 * trigger the synchronisation of the CustomGroup <-> selector option group
 */
function revent_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'CustomGroup') {
    if ($op == 'create' || $op == 'edit' || $op == 'delete') {
      CRM_Revent_RegistrationFields::synchroniseFields();
    }
  }
}


/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function revent_civicrm_config(&$config) {
  _revent_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function revent_civicrm_xmlMenu(&$files) {
  _revent_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function revent_civicrm_install() {
  _revent_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function revent_civicrm_postInstall() {
  _revent_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function revent_civicrm_uninstall() {
  _revent_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function revent_civicrm_enable() {
  _revent_civix_civicrm_enable();

  require_once 'CRM/Revent/CustomData.php';
  $customData = new CRM_Revent_CustomData('de.systopia.revent');
  $customData->syncOptionGroup(__DIR__ . '/resources/option_group_remote_registration_fields.json');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_remote_event_connection.json');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_remote_event_registration.json');
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function revent_civicrm_disable() {
  _revent_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function revent_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _revent_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function revent_civicrm_managed(&$entities) {
  _revent_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function revent_civicrm_caseTypes(&$caseTypes) {
  _revent_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function revent_civicrm_angularModules(&$angularModules) {
  _revent_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function revent_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _revent_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
