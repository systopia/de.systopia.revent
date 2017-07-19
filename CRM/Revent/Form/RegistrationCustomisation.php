<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
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
 * Form to customise Event registrations
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Revent_Form_RegistrationCustomisation extends CRM_Core_Form {

  protected $registrationRenderer = NULL;

  public function buildQuickForm() {
    // get event ID
    $event_id = CRM_Utils_Request::retrieve('eid', 'Positive', $this);
    if (!$event_id) {
      CRM_Core_Error::fatal("No event ID (eid) given.");
    } else {
      $this->add('hidden', 'eid', $event_id);
    }

    // load currently customised data
    $this->registrationRenderer = new CRM_Revent_RegistrationFields(array('id' => $event_id));
    $data = $this->registrationRenderer->renderEventRegistrationForm();

    // sort after group weight
    usort($data['groups'], array('CRM_Revent_Form_RegistrationCustomisation', 'compareHelper'));
    // put fields to corrosponding sorted groups
    foreach($data['fields'] as $value) {
      foreach($data['groups'] as &$group) {
        if ($group['name'] == $value['group']) {
          $group['fields'][$value['name']] = $value;
          // FixMe: currently static field for language support.
          //        Needed lateron in the form creation
          // TODO: Add additional lanuages here in the future
          //       Also: language dependent fields need to be named __NAME_LANG (e.g. title_de)
          $group['fields'][$value['name']]['languages'] = array('de', 'en');
        }
      }
    }
    // sort associated groups according to weight
    foreach ($data['groups'] as &$group) {
      usort($group['fields'], array('CRM_Revent_Form_RegistrationCustomisation', 'compareHelper'));
    }

    // data is now sorted. create forms now for all groups
    // TODO: do we need $i/$j here as actual counter?
    $groupIndex =1;
    foreach($data['groups'] as $indexed_group) {
      $fieldIndex = 1;
      foreach ($indexed_group['fields'] as $indexed_field) {
        $languageIndex = 1;
        $this->createFormElements($groupIndex, $fieldIndex, $languageIndex);
        $languageIndex++;
        foreach ($indexed_field['languages'] as $indexed_language) {
          $this->createFormElements($groupIndex, $fieldIndex, $languageIndex);
          if ($languageIndex != count($indexed_field['languages'])) {
            $languageIndex++;
          }
        } // for loop over field languages
        // assign number of languages for field $j and group $i
        $this->assign("line_numbers_{$groupIndex}_{$fieldIndex}", range(1, $languageIndex));
        if ($fieldIndex != count($indexed_group['fields'])) {
          $fieldIndex++;
        }
      } // for loop fields
      // assign number of fields for group $i
      $this->assign("line_numbers_{$groupIndex}", range(1, $fieldIndex));
      if ($groupIndex != count($data['groups'])) {
        $groupIndex++;
      }
    } // for loop groups

    // assign line numbers for groups
    $this->assign("line_numbers", range(1, $groupIndex));

    // submit button
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  /**
   * compares to arrays elements weight and returns the difference
   * according to callback function of usort
   * @param $a
   * @param $b
   *
   * @return integer
   */
  private static function compareHelper($a, $b) {
    return $a['weight'] - $b['weight'];
  }

  /**
   * creates form elements indexed by group, field and language
   *
   * @param $groupIndex
   * @param $fieldIndex
   * @param $languageIndex (1 = default language, always available)
   */
  private function createFormElements($groupIndex, $fieldIndex, $languageIndex) {
    $this->add(
      'text',
      "title_{$groupIndex}_{$fieldIndex}_{$languageIndex}",
      'title'
    );
    $this->add(
      'text',
      "description_{$groupIndex}_{$fieldIndex}_{$languageIndex}",
      'description'
    );
    $this->add(
      'advcheckbox',
      "required_{$groupIndex}_{$fieldIndex}_{$languageIndex}",
      'required'
    );
    $this->add(
      'text',
      "weight_{$groupIndex}_{$fieldIndex}_{$languageIndex}",
      'weight'
    );
  }

  public function postProcess() {
    $values = $this->exportValues();

    // TODO: extract from values
    $groups = array();
    $fields = array();

    // pass on to the
    $data = $this->registrationRenderer->updateCustomisation($groups, $fields);
    parent::postProcess();
  }
}
