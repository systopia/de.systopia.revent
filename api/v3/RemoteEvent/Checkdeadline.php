<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: P.Batroff (batroff@systopia.de)                |
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
 * Check deadline for given Event and set online registration to false if deadline reached
 */
function civicrm_api3_remote_event_checkdeadline($params) {
  $now  = date("Y-m-d H:i:s");
  $counter = 0;
  $result = civicrm_api3('RemoteEvent', 'get', [
    'sequential' => 1,
    'start_date' => ['>' => $now],
    'options' => ['limit' => 0],
  ]);
  
  foreach ($result['values'] as $event_id => $event) {
    if (isset($event['remote_event_registration.registration_deadline']) && $event['remote_event_registration.registration_deadline'] < $now) {
      // disable online registration
      $result = civicrm_api3('RemoteEvent', 'create', [
        'id' => $event_id,
        'remote_event_registration.online_registration_enabled' => FALSE,
      ]);
      $counter++;
    }
  }
  return civicrm_api3_create_success("Disabled online registration for {$counter} events with expired registration deadlines");
}
