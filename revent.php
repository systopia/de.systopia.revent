<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| Author: P. Batroff (batroff@systopia.de)               |
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
      CRM_Revent_RegistrationFieldSynchronisation::synchroniseFields();
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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function revent_civicrm_install() {
  _revent_civix_civicrm_install();
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
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_registration_address.json');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_registration_billing_address.json');
  $customData->syncCustomGroup(__DIR__ . '/resources/custom_group_participant_extra_information.json');


  require_once 'CRM/Revent/RegistrationFields.php';
  CRM_Revent_RegistrationFieldSynchronisation::synchroniseFields();

  CRM_Revent_Config::installScheduledJob();
}

function revent_civicrm_pre($op, $objectName, $id, &$params) {
  if (($op == 'create' || $op == 'edit' ) && $objectName == 'Participant') {
    try {
      CRM_Revent_CustomFieldFilter::filter_custom_fields($params);
    } catch (Exception $ex) {
      CRM_Core_Error::debug_log_message("Revent: filter_custom_fields() on {$op} failed: " . $ex->getMessage());
    }
  }
}

/**
 * Implements hook_civicrm_buildForm()
 * @param $formName
 * @param $form
 */
function revent_civicrm_buildForm($formName, &$form) {
  switch ($formName) {
    case 'CRM_Custom_Form_CustomDataByType':
      require_once 'CRM/Revent/EventRegistrationIntegration.php';
      $regIntegration = new CRM_Revent_EventRegistrationIntegration($formName, $form);
      $regIntegration ->buildFormHook();
      break;
    case 'CRM_Event_Form_ManageEvent_EventInfo':
      require_once 'CRM/Revent/EventManagementForm.php';
      CRM_Revent_EventManagementForm::buildFormHook($formName, $form);
      break;
    case 'CRM_Event_Form_Search':
      require_once 'CRM/Revent/EventDashboardForm.php';
      CRM_Revent_EventDashboardForm::buildFormHook();
      break;
    case 'CRM_Event_Form_Participant':
      require_once 'CRM/Revent/CustomDataForm_Mods.php';
      CRM_Revent_CustomDataForm_Mods::buildFormHook($formName, $form);
      break;
    case 'CRM_Event_Form_ParticipantView':
      CRM_Revent_ParticipantViewMods::buildFormHook($form);
      break;
    case 'CRM_Event_Form_ManageEvent_Location':
      CRM_Revent_EventManagementForm::handleFormHookRedirect($formName, $form);
      break;
    case 'CRM_Report_Form_Event_ParticipantListing':
      /* @var \CRM_Report_Form_Event_ParticipantListing $form */
      // Remove actions drop-down menu for users without permissions to change
      // report criteria.
      // @see https://github.com/systopia/de.systopia.revent/issues/1
      if (!CRM_Core_Permission::check('access Report Criteria')) {
        $form->removeElement('task');
      }
      break;
    default:
      break;
  }
}

/**
 * implements hook_civicrm_pageRun( &$page )
 * @param $page
 */
function revent_civicrm_pageRun( &$page ) {
  $name = $page->getVar('_name');
  $eid = $page->getVar('_id');
  switch ($name) {
    case "CRM_Event_Page_EventInfo":
      if (empty($name) || empty($eid)) {
        return;
      }
      // FixME? seems dirty
      $tmpform = "";
      $regIntegration = new CRM_Revent_EventRegistrationIntegration(NULL, $tmpform, $eid, $page);
      $regIntegration->pageRunHook();
      break;
    case "CRM_Event_Page_ManageEvent":
      require_once 'CRM/Revent/EventManagementForm.php';
      CRM_Revent_EventManagementForm::handleEventPageHook();
      break;
    default:
      break;
  }
}

/**
 * Define custom (Drupal) permissions
 */
function revent_civicrm_permission(&$permissions) {
  $prefix = 'Revent' . ': ';
  $permissions['access RemoteEvent']    = [
    'label' => $prefix . 'access Remote Event API',
    'description' => 'Permission to access Remote Event API.',
  ];
}

/**
 * Set permissions for runner/engine API call
 */
function revent_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['remote_event']['create']                            = array('access RemoteEvent');
  $permissions['revent']['get']                               = array('access RemoteEvent');
  $permissions['remote_group']['list']                              = array('access RemoteEvent');
  $permissions['remote_group']['subscribe']                         = array('access RemoteEvent');
  $permissions['remote_group']['unsubscribe']                       = array('access RemoteEvent');
  $permissions['remote_registration']['get_form']                   = array('access RemoteEvent');
  $permissions['remote_registration']['register']                   = array('access RemoteEvent');
  $permissions['remote_registration']['unregister']                 = array('access RemoteEvent');
  $permissions['remote_registration']['get_active_groups']          = array('access RemoteEvent');
  $permissions['remote_registration']['get_custom_group_meta_data'] = array('access RemoteEvent');
}

/**
 * Implementation of hook_civicrm_copy
 */
function remoteevent_civicrm_copy($objectName, &$object)
{
  // do not "activate for now, see #21816 for details
  return;
  if ($objectName == 'Event') {
    // we have the new event ID...
    $new_event_id = (int)$object->id;

    // ...unfortunately, we have to dig up the original event ID
    // shamelessly stolen from remoteevents
    $callstack = debug_backtrace();
    foreach ($callstack as $call) {
      if (isset($call['class']) && isset($call['function'])) {
        if ($call['class'] == 'CRM_Event_BAO_Event' && $call['function'] == 'copy') {
          // this should be it:
          $original_event_id = (int) $call['args'][0];
          break;
        }
      }
    }

    if ($original_event_id && $new_event_id) {
      // copy custom fields to new event

      // get custom fields
      $custom_group_customisations = CRM_Revent_CustomData::getCustomFieldKey("remote_event_registration", "registration_customisations");
      $custom_group_fields = CRM_Revent_CustomData::getCustomFieldKey("remote_event_registration", "registration_fields");

      $original_event = civicrm_api3('Event', 'getsingle', array(
        'sequential' => 1,
        'return' => array($custom_group_customisations, $custom_group_fields),
        'id' => $original_event_id,
      ));

      // store it
      $update_result = civicrm_api3('Event', 'create', array(
        'sequential' => 1,
        'id' => $new_event_id,
        $custom_group_customisations => $original_event[$custom_group_customisations],
        $custom_group_fields         => $original_event[$custom_group_fields],
      ));
    }
  }
}
