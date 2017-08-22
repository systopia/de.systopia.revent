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
 * Provides functions for the registration field selection
 */
class CRM_Revent_RegistrationProcessor {

  /** the associated event */
  protected $event_id = NULL;

  // cached field
  protected $_event = NULL;
  protected $_attending_status_ids = NULL;
  protected $_waiting_status_ids = NULL;


  public function __construct($event_id) {
    $this->event_id = $event_id;
  }

  /**
   * Main registration processor
   */
  public function registerContact($data) {
    // resolve custom fields
    CRM_Revent_CustomData::resolveCustomFields($data);

    if ($this->doesEventNeedRegistrationApproval()) {
      // error_log("NEEDS APPROVAL");
      $data['participant_status_id'] = $this->getApprovalPendingStatusID();

    } elseif ($this->hasWaitlist()) {
      // WAIT LIST
      // error_log("HAS WAIT LIST");
      if ($this->eventFull() || $this->hasPeopleOnWaitlist()) {
        // there is already a waitlist or the event has just become full
        // error_log("MUST WAIT");
        $data['participant_status_id'] = $this->getWaitingListStatusID();
        $data['must_wait'] = 1;

      } else {
        // all seems to be good
        // error_log("ALL GOOD");
        $data['participant_status_id'] = $this->getRegisteredStatusID();
      }

    } else {
      // NOT WAIT LIST
      // error_log("HAS NO WAIT LIST");
      if ($this->eventFull()) {
        // event full and no waitlist, sorry
        // error_log("REJECTED");
        $data['participant_status_id'] = $this->getRejectedStatusID();
      } else {
        // all seems to be good
        // error_log("ALL GOOD");
        $data['participant_status_id'] = $this->getRegisteredStatusID();
      }
    }

    // create participant
    $participant = civicrm_api3('Participant', 'create', $data);

    // get all participant data
    return civicrm_api3('Participant', 'getsingle', array('id' => $participant['id']));
  }

  /**
   * are there currently people on the waiting list
   */
  protected function hasPeopleOnWaitlist() {
    // there is a limit -> count the current participants
    $count = civicrm_api3('Participant', 'getcount', array(
      'event_id'              => $this->event_id,
      'participant_status_id' => array('IN' => $this->getWatitingStatusIDs())
      ));
    // error_log("PEOPLE ON WAIT LIST: $count");
    return $count > 0;
  }

  /**
   * test if the given event requires manual approval for registration
   */
  protected function doesEventNeedRegistrationApproval() {
    $event = $this->getEvent();
    return !empty($event['remote_event_registration.requires_approval']);
  }

  /**
   * Does the event has a waiting list?
   */
  protected function hasWaitlist() {
    $event = $this->getEvent();
    return !empty($event['has_waitlist']);
  }

  /**
   * Is the event full, i.e. all regular participant slots have been filled?
   */
  protected function eventFull() {
    $event = $this->getEvent();
    if (empty($event['max_participants'])) {
      // there's no limit
      return FALSE;

    } else {

      // there is a limit -> count the current participants
      $count = civicrm_api3('Participant', 'getcount', array(
        'event_id'              => $this->event_id,
        'participant_status_id' => array('IN' => $this->getAttendingStatusIDs())
        ));
      // error_log("PEOPLE ATTENDING: $count");

      return $count >= (int) $event['max_participants'];
    }
  }

  /**
   * get the event object
   */
  public function getEvent() {
    if ($this->_event === NULL) {
      $this->_event = civicrm_api3('Event', 'getsingle', array('id' => $this->event_id));
      CRM_Revent_CustomData::labelCustomFields($this->_event, 2);
    }
    return $this->_event;
  }

  /**
   * Participant status for "Waiting List"
   */
  protected function getWaitingListStatusID() {
    return 7;
  }

  /**
   * Participant status for "Registered"
   */
  protected function getRegisteredStatusID() {
    return 1;
  }

  /**
   * Participant status for "Waiting List"
   */
  protected function getApprovalPendingStatusID() {
    return 8;
  }

  /**
   * Participant status for "Waiting List"
   */
  protected function getRejectedStatusID() {
    return 11;
  }

  /**
   * get a list of postitive status IDs
   */
  protected function getAttendingStatusIDs() {
    if ($this->_attending_status_ids === NULL) {
      $this->_attending_status_ids = array($this->getRegisteredStatusID());
      $query = civicrm_api3('ParticipantStatusType', 'get', array(
        'class'        => 'Positive',
        'option.limit' => 0,
        'return'       => 'id'));
      foreach ($query['values'] as $status) {
        $this->_attending_status_ids[] = $status['id'];
      }
    }
    return $this->_attending_status_ids;
  }

  /**
   * get a list of postitive status IDs
   */
  protected function getWatitingStatusIDs() {
    if ($this->_waiting_status_ids === NULL) {
      $this->_waiting_status_ids = array($this->getWaitingListStatusID());
      $query = civicrm_api3('ParticipantStatusType', 'get', array(
        'class'        => 'Waiting',
        'option.limit' => 0,
        'return'       => 'id'));
      foreach ($query['values'] as $status) {
        $this->_waiting_status_ids[] = $status['id'];
      }
    }
    return $this->_waiting_status_ids;
  }
}
