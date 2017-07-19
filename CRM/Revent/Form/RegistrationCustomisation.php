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

    // // add form elements
    // $this->add(
    //   'select', // field type
    //   'favorite_color', // field name
    //   'Favorite Color', // field label
    //   $this->getColorOptions(), // list of options
    //   TRUE // is required
    // );
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
