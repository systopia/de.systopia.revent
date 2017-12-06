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
 * Changes the custom data form elements, hides hidden option Groups
 */

class CRM_Revent_CustomDataForm_Mods {

  /**
   * adds gui mods for build form hook
   * @param $formName
   * @param $form
   */
  public static function buildFormHook($formName, &$form) {

    $event_id = $form->getVar('_eID');
    $registrationFields = new CRM_Revent_RegistrationFields(array('id' => $event_id));
    $active_group_ids = $registrationFields->getActiveGroups();

    $form->assign("active_group_ids", json_encode($active_group_ids));

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/EventCustomDataForm.tpl"
    ));
  }

  public static function buildFormHookNoEventId($formName, &$form) {

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/EventCustomDataFormDynamicEventId.tpl"
    ));
  }
}

