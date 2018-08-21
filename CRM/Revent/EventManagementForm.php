<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres  (endres@systopia.de)                |
| Author: P. Batroff (batroff@systopia.de)               |
| Source: http://www.systopia.de/                        |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

/**
 * Changes the Event Management form
 */
class CRM_Revent_EventManagementForm {

  public static function buildFormHook() {

    $script = file_get_contents(__DIR__ . '/../../js/event_management_form.js');
    $script = str_replace('__MESSAGE__',
      ts(
      'Please use alphanumeric Characters, Space or Dash for Event Title. Event Title configured or edited here wont show up on the website.',
        array('domain' => 'de.systopia.revent')),
      $script);
    CRM_Core_Region::instance('page-footer')->add(array(
      'script' => $script,
    ));
  }

  public static function handleEventPageHook() {

    $script = file_get_contents(__DIR__ . '/../../js/event_management_page.js');

    $base_url = CRM_Utils_System::url('civicrm/revent/customisation');
    $script = str_replace('__URL__', $base_url, $script);
    $script = str_replace('__Registration-Customisation__', ts('Registration Customisation', array('domain' => 'de.systopia.revent')), $script);

    $base_url_import = CRM_Utils_System::url('civicrm/revent/customisation_import');
    $script = str_replace('__URL-import__', $base_url_import, $script);
    $script = str_replace('__Registration-Customisation-import__', ts('Import Customisation from other Event', array('domain' => 'de.systopia.revent')), $script);

    // #6330 Event Report Link
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
    //$report_instance = 26;   // LOKALE TESTUMGEBUNG
    foreach ($reports as $report_instance => $title) {
      $report_url     = CRM_Utils_System::url("civicrm/report/instance/{$report_instance}");
      $script = str_replace("__REPORT-URL-{$report_instance}__", $report_url, $script);
      $script = str_replace("__REPORT-URL-LABEL-{$report_instance}__", $title, $script);
    }

    CRM_Core_Region::instance('page-footer')->add(array(
      'script' => $script,
    ));
  }

  public static function handleFormHookRedirect($formName, $form) {

    $eventId = $form->getVar("_id");
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/manage/settings',
      "&reset=1&action=update&id={$eventId}"));
  }
}