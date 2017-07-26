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
    if ($eid != NULL) {
      $this->eid = $eid;
    }
  }

  public function buildFormHook() {
    // TODO:
    // - get custom group name for Registration Customisation (e.g. custom_14_4)
    //      --> assign this to form then
    // - create link to registration customisation page and assign it in form


    // add template path for these fields
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/EventRegistrationIntegration.tpl"
    ));
  }


}