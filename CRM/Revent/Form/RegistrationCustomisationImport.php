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

/**
 * Form to customise Event registrations
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Revent_Form_RegistrationCustomisationImport extends CRM_Core_Form{

  protected $event_id               = NULL;
  protected $data                   = NULL;
  protected $eventLabel2id               = NULL;

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Import Customisation from other Event', array('domain' => 'de.systopia.revent')));

    // get event ID
    $event_id = CRM_Utils_Request::retrieve('eid', 'Positive', $this);
    if (!$event_id) {
      CRM_Core_Error::fatal(ts('No event ID (eid) given.', array('domain' => 'de.systopia.revent')));
    } else {
      $this->add('hidden', 'eid', $event_id);
      $this->event_id = $event_id;
    }

    // get current Events from api
    $event_query = civicrm_api3('Event', 'get', array(
      'sequential' => 1,
      'return' => array('id', 'event_title'),
      'options' => array('limit' => 0),
    ))['values'];

    $this->eventLabel2id = array();
    foreach ($event_query as $event) {
      $this->eventLabel2id[$event['id']] = $event['event_title'];
    }

    $this->assign("introduction", ts("introduction text", array('domain' => 'de.systopia.revent')));

    $this->add('select',
      "events",
      'events',
      $this->eventLabel2id,
      True,
      array('class' => 'crm-select2')
    );

    // submit button
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit', array('domain' => 'de.systopia.revent')),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  public function postProcess() {

    // get form values
    $values = $this->exportValues();

    // get custom fields
    $custom_group_customisations = CRM_Revent_CustomData::getCustomFieldKey("remote_event_registration", "registration_customisations");
    $custom_group_fields = CRM_Revent_CustomData::getCustomFieldKey("remote_event_registration", "registration_fields");

    $event = civicrm_api3('Event', 'getsingle', array(
      'sequential' => 1,
      'return' => array($custom_group_customisations, $custom_group_fields),
      'id' => $values['events'],
    ));

    // store it
    $update_result = civicrm_api3('Event', 'create', array(
      'sequential' => 1,
      'id' => $this->event_id,
      $custom_group_customisations => $event[$custom_group_customisations],
      $custom_group_fields         => $event[$custom_group_fields],
    ));

    parent::postProcess();
  }
}
