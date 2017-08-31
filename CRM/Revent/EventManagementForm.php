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
    CRM_Core_Region::instance('page-footer')->add(array(
      'script' => $script,
    ));
  }

  public static function handleEventPageHook() {

    $script = file_get_contents(__DIR__ . '/../../js/event_management_page.js');
    $base_url = CRM_Utils_System::url('civicrm/revent/customisation');
    $script = str_replace('__URL__', $base_url, $script);
    $script = str_replace('__Registration-Customisation__', ts('Registration Customisation', array('domain' => 'de.systopia.revent')), $script);

    CRM_Core_Region::instance('page-footer')->add(array(
      'script' => $script,
    ));
  }
}