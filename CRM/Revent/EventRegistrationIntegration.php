<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres  (endres@systopia.de)                |
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


class CRM_Revent_EventRegistrationIntegration {

  private $formName       = NULL;
  private $form           = NULL;
  private $eid            = NULL;
  private $page           = NULL;

  /**
   * CRM_Uimods_UserClearance constructor.
   */
  public function __construct($formName = NULL, &$form = NULL, $eid = NULL, &$page = NULL) {
    $this->form = $form;
    $this->formName = $formName;
    if (empty($eid)) {
      $this->eid = $form->_entityId;
    } else {
      $this->eid = $eid;
    }
    $this->page = $page;
  }

  public function buildFormHook() {

    if (empty($this->eid)) {
      // nothing to do here
      return;
    }
    // get custom fields
    $registration_customisation_key = CRM_Revent_CustomData::getCustomFieldKey('remote_event_registration', 'registration_customisations');
    $registration_fields_key        = CRM_Revent_CustomData::getCustomFieldKey('remote_event_registration', 'registration_fields');
    if (!$registration_customisation_key || !$registration_fields_key) {
      error_log("Couldn't find custom fields for registration. This shouldn't happen. Check if all configuration files are installed.");
      return;
    }
    $this->form->assign("registration_customisation_field", "{$registration_customisation_key}_");
    $this->form->assign("registration_fields", "{$registration_fields_key}_");

    $args = array(
      'eid'     => $this->eid,
      'reset'   =>  '1',
    );
    $registration_customisation_link = CRM_Utils_System::url("civicrm/revent/customisation", $args, TRUE);
    $this->form->assign("form_link", $registration_customisation_link);

    // add template path for these fields
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/EventRegistrationIntegration.tpl"
    ));
  }

  public function pageRunHook() {

    $args = array(
      'eid'     => $this->eid,
      'reset'   =>  '1',
    );
    $registration_customisation_link = CRM_Utils_System::url("civicrm/revent/customisation", $args, TRUE);
    $this->page->assign("form_link", $registration_customisation_link);

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/RegistrationCustomizationPageRun.tpl"
    ));
  }


}