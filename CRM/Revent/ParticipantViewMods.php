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
 * hides not activated option Groups
 */

class CRM_Revent_ParticipantViewMods {

  /**
   * adds gui mods for build form hook
   * @param $form
   */
  public static function buildFormHook(&$form) {

    $participant_id = CRM_Utils_Request::retrieve('id', 'Positive', $form);
    if (empty($participant_id)) {
      // we shall do nothing
      return;
    }
    $participant = civicrm_api3('Participant', 'getsingle', array(
      'sequential' => 1,
      'id' => $participant_id,
    ));
    $registrationFields = new CRM_Revent_RegistrationFields(array('id' => $participant['event_id']));
    $active_group_ids = $registrationFields->getActiveGroups();

    $custom_group_names = CRM_Revent_CustomData::getGroup2Name();
    foreach ($active_group_ids as $active_group_id) {
      $active_group_names[] = $custom_group_names[$active_group_id];
    }
    $form->assign("active_group_ids", json_encode($active_group_names));

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "CRM/Revent/ParticipantViewForm.tpl"
    ));
  }
}

