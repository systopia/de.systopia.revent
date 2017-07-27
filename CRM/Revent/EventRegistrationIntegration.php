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

  /**
   * CRM_Uimods_UserClearance constructor.
   */
  public function __construct($formName = NULL, &$form = NULL, $eid = NULL) {
    $this->form = $form;
    $this->formName = $formName;
    $this->eid = $form->_entityId;
  }

  public function buildFormHook() {
    // TODO:
    // - get custom group name for Registration Customisation (e.g. custom_14_4)
    //      --> assign this to form then
    // - create link to registration customisation page and assign it in form

    // get customisation id
    $customisation_field = civicrm_api3('CustomField', 'getsingle', array(
      'sequential' => 1,
      'label' => "Registration Customisations",
    ));
    if ($customisation_field['is_error']) {
      error_log("Couldn't find custom ID for label 'Registration Customisation'. This shouldn't happen. Check if all configuration files are installed.");
      return;
    }
    $this->form->assign("registration_customisation_field", "custom_{$customisation_field['id']}_");

    // get field choice id
    $registration_field = civicrm_api3('CustomField', 'getsingle', array(
      'sequential' => 1,
      'label' => "Registration Fields",
    ));
    if ($registration_field['is_error']) {
      error_log("Couldn't find custom ID for label 'Registration Customisation'. This shouldn't happen. Check if all configuration files are installed.");
      return;
    }
    $this->form->assign("registration_fields", "custom_{$registration_field['id']}_");


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


}