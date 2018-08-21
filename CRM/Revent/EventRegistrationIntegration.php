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
    $this->form->assign("registration_customisation_field", "{$registration_customisation_key}");

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

    // Registration Customisation Import links
    $registration_customisation_import_link = CRM_Utils_System::url("civicrm/revent/customisation_import", $args, TRUE);
    $this->page->assign("import_form_link", $registration_customisation_import_link);

    $registration_customisation = CRM_Revent_CustomData::getCustomField('remote_event_registration', 'registration_customisations');
    $registration_fields        = CRM_Revent_CustomData::getCustomField('remote_event_registration', 'registration_fields');

    $this->page->assign("reg_customization_label", $registration_customisation['label']);
    $this->page->assign("reg_customisation_fields_label", $registration_fields['label']);

    $this->page->assign("registration_form_link_label", ts('registration customisation edit', array('domain' => 'de.systopia.revent')));
    $this->page->assign("registration_customisation", ts('Registration Customisation', array('domain' => 'de.systopia.revent')));

    // registration import menu entry
    $this->page->assign("registration_customisation_import", ts('Import Customisation from other Event', array('domain' => 'de.systopia.revent')));

    // #6330 - Link to event report
    $reports = array(
      56 => "Veranstaltungsbericht Teilnehmende Standard",
      61 => "Veranstaltungsbericht Teilnehmende Extras",
      62 => "Veranstaltungsbericht Teilnehmende Fachveranstaltung",
      63 => "Veranstaltungsbericht Teilnehmende GC Workshops",
      74 => "Veranstaltungsbericht Teilnehmende GC Workshops sortiert nach Zahlung",
      64 => "Veranstaltungsbericht Teilnehmende 1 Panelblock nach Panels sortiert ",
      66 => "Veranstaltungsbericht Teilnehmende 1 Panelblock nach Tagen sortiert",
      67 => "Veranstaltungsbericht Teilnehmende 2 Panelblöcke nach Panel 1 sortiert",
      68 => "Veranstaltungsbericht Teilnehmende 2 Panelblöcke nach Panel 2 sortiert",
      69 => "Veranstaltungsbericht Teilnehmende 2 Panelblöcke nach Tagen sortiert",
      70 => "Veranstaltungsbericht Teilnehmende 3 Panelblöcke nach Panel 1 sortiert",
      71 => "Veranstaltungsbericht Teilnehmende 3 Panelblöcke nach Panel 2 sortiert",
      72 => "Veranstaltungsbericht Teilnehmende 3 Panelblöcke nach Panel 3 sortiert",
      73 => "Veranstaltungsbericht Teilnehmende 3 Panelblöcke nach Tagen sortiert",
    );
//    $report_instance = 26;   // LOKALE TESTUMGEBUNG
    $report_args = array(
      'reset'   =>  '1',
      'force'   =>  '1',
      'event_id_op'   => 'in',
      'event_id_value' => $this->eid,
    );
    foreach ($reports as $report_instance => $title) {
      $event_report_link = CRM_Utils_System::url("civicrm/report/instance/{$report_instance}", $report_args, TRUE);
      $this->page->assign("event_report_link_{$report_instance}", $event_report_link);
      $this->page->assign("event_report_link_label_{$report_instance}", $title);
    }

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/RegistrationCustomizationPageRun.tpl"
    ));
  }


}