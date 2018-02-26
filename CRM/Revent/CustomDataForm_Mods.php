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

    $cId = $form->getVar('_contactId');

    if (!empty($cId)) {
      $form->assign("form_contact_id", $cId);
    } else {
      // FixME: if this isn't initialized, js throws errors
      $form->assign("form_contact_id", "0");
    }

    $custom_group_names = CRM_Revent_CustomData::getGroup2Name();
    $custom_group_name = array_search("registration_address", $custom_group_names);
    $form->assign("registration_address_custom_id", "\"custom_group_{$custom_group_name}\"");

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/EventCustomDataForm.tpl"
    ));
  }

}

