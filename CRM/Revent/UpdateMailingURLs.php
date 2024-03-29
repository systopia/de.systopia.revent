<?php
/*-------------------------------------------------------+
| SYSTOPIA REMOTE EVENT REGISTRATION                     |
| Copyright (C) 2020 SYSTOPIA                            |
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

use CRM_Revent_ExtensionUtil as E;

/**
 * Class CRM_Revent_UpdateMailingURLs
 */
class CRM_Revent_UpdateMailingURLs {

  // number of hours to check for new mailings.
  // if NULL, ALL mailings are checked and updated
  private $hours_since_last_check;
  // counter for debug output
  private $url_counter;

  //
  private $regional_mailinggroups = [
    1 => [138, 739], // Lateinamerika Newsletter
    2 => [141, 737], // Nahost- & Nordafrika-Update
    3 => [148, 740], // Ost- und Südosteuropa-Newsletter
    4 => [110, 712], // 30_NEWS | Newsletter Asien (Web, IZ), 20_ASIEN (WEB | IZ)
//    5 => [3, 4], // local test groups
  ];

  /**
   * CRM_Revent_UpdateMailingURLs constructor.
   * @param null $since
   *  hours for the last scheduled mailing
   */
  public function __construct($since = NULL) {
    $this->hours_since_last_check = $since;
    $this->url_counter = 0;
  }


  /**
   * Main runner function
   */
  public function run() {
    if (empty($this->hours_since_last_check)) {
      // cleanup previous mailing_urls
      // this prevents old mailing URLs for deleted mailings that never disappear
      // this should only happen if no lsat date is configured!
      $this->cleanup_old_mailing_urls();
    }
    
    // get all mailings since
    $mailing_ids = $this->get_all_mailing();
    $this->filter_mailing($mailing_ids);

    // put them in array (group -> URL)
    $mapped_mailing_urls = $this->map_mailing_urls($mailing_ids);

    // add url to respective group
    $this->set_mailing_urls($mapped_mailing_urls);
  }

  
  /**
   * @return void
   */
  private function cleanup_old_mailing_urls() {
    try {
      $query_string = "
        UPDATE civicrm_value_group_fields 
        SET mailing_url = '';";
      $query = CRM_Core_DAO::executeQuery($query_string);
    } catch (Exception $e) {
      Civi::log()->log("DEBUG", "Failed to remove previous mailing_urls. Error: " . $e->getMessage());
    }
  }


  /**
   * Debug output for number of changed mailing URLs
   *
   * @return int
   */
  public function get_changed_url_counter() {
    return $this->url_counter;
  }


  /**
   * set mailing urls for groups involved in that mailing
   *
   * @param $mapped_mailing_urls
   * @throws CiviCRM_API3_Exception
   */
  private function set_mailing_urls($mapped_mailing_urls) {

    $custom_field = $this->get_mailing_url_custom_field();
    
    foreach ($mapped_mailing_urls as $mailing_id => $mailing_url) {
      $result = civicrm_api3('MailingGroup', 'get', [
        'mailing_id' => $mailing_id,
      ]);
      foreach ($result['values'] as $value) {
        $group_id = $value['entity_id'];
        $update_group_result = civicrm_api3('Group', 'create', [
          'id' => $group_id,
          $custom_field => $mailing_url,
        ]);
        // this number is highly misleading, since it is possible and quitepossible that the same group gets updated multiple times
        // So the real amount of mailing URLs in the system is very likely to be lower
        $this->url_counter++;
      }
    }
  }


  /**
   * Helper function. Get custom field id for mailing_url
   *
   * @return string
   * @throws CiviCRM_API3_Exception
   */
  private function get_mailing_url_custom_field() {
    $result = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => "group_fields",
      'name' => "mailing_url",
    ]);
    return "custom_{$result['id']}";
  }


  /**
   * Maps Mailing IDs -> Mailing URLs
   *
   * @param $mailing_ids
   * @return array
   */
  private function map_mailing_urls($mailing_ids) {
    $config      = CRM_Core_Config::singleton();
    $system_base = $config->userFrameworkBaseURL;
    $proxy_base = NULL;
    $enabled = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_enabled');
    if ($enabled) {
      $proxy_base = CRM_Core_BAO_Setting::getItem('CiviProxy Settings', 'proxy_url');
    }

    $results = [];
    foreach ($mailing_ids as $key => $m_id) {
      $url = $this->create_mailing_url($m_id['id'], $system_base, $proxy_base);
      $results[$m_id['id']] = $url;
    }
    return $results;
  }


  /** Created a public URL for the given mailing ID.
   *  If CiviProxy is installed, the public mailing URL is created for the proxy
   *
   * @param $mailing_id
   * @param $civi_base_url
   * @param $proxy_base
   * @return string
   */
  private function create_mailing_url($mailing_id, $civi_base_url, $proxy_base) {
    // check if CiviProxy is enabled
    if (!empty($proxy_base)) {
      // create proxy URL
      return "{$proxy_base}/mailing/mail.php?id={$mailing_id}";
    }
    // create normal mailing URL
    return "{$civi_base_url}civicrm/mailing/view?id={$mailing_id}";
  }


  /**
   * Gets all Mailings for the hours since the last check.
   * If  $this->hours_since_last_check isn't configured, all mailings are fetched.
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  private function get_all_mailing() {
    $params['options'] = ['limit' => 0];
    $params['return'] = ["id"];
    // only use completed mailings, see https://projekte.systopia.de/issues/19238#note-6
    $params['is_completed'] = 1;
    $this->get_date_param($params);
    $result = civicrm_api3('Mailing', 'get', $params);
    return array_values($result['values']);
  }


  /**
   * Filter out all mailings that have more than one recipient group
   *
   * @param $mailing_ids
   * @throws CiviCRM_API3_Exception
   */
  private function filter_mailing(&$mailing_ids) {
    foreach ($mailing_ids as $key => $m_id) {
      $result = civicrm_api3('MailingGroup', 'get', [
        'mailing_id' => $m_id['id'],
      ]);
      // special handling, see https://projekte.systopia.de/issues/19238
      // check if the respective mailing goes to exacly 2 mailing_groups specified in $regional_mailinggroups
      if ($result['count'] == 2) {
        if ($this->filter_regional_mapping($result['values'])) {
          unset($mailing_ids[$key]);
        }

        $mailing_groups = [];
        foreach ($result['values'] as $m_key => $m_values) {
          array_push($mailing_groups, $m_values['entity_id']);
        }
        $b = False;
        foreach ($this->regional_mailinggroups as $key => $group_ids) {
          if (empty(array_diff($group_ids, $mailing_groups))) {
            $b = True;
          }
        }
        if (!$b) {
          unset($mailing_ids[$key]);
        }
      }
      if ($result['count'] > 2 || $result['count'] == 0) {
        unset($mailing_ids[$key]);
      }
      // we need to check if the respective group exists, otherwise the insert operation will fail
      // see https://projekte.systopia.de/issues/11024?issue_count=61&issue_position=44&next_issue_id=10334&prev_issue_id=11184#note-60
      // if group doesn't exist (anymore), we need to unset the mailing ID as well!
      foreach ($result['values'] as $m_key => $m_values) {
        // this really can only have one entry because of the above query
        $mailing_group_result = civicrm_api3('Group', 'get', [
          'id' => $m_values['entity_id'],
        ]);
        if ($mailing_group_result['count'] == 0) {
          // unset as well, since the mailing group doesn't exist anymore!
          unset($mailing_ids[$key]);
        }
      }
    }
  }


  /**
   * Return False if mailing groups are a regional tuple
   *
   * @return bool
   */
  private function filter_regional_mapping($mailing_group_results) {
    $mailing_groups = [];
    foreach ($mailing_group_results as $m_key => $m_values) {
      array_push($mailing_groups, $m_values['entity_id']);
    }
    foreach ($this->regional_mailinggroups as $key => $group_ids) {
      if (empty(array_diff($group_ids, $mailing_groups))) {
        return False;
      }
    }
    return True;
  }


  /**
   * Helper function. Creates a timestamp for now - configured hours since last check
   * Is directly added to the parameter array for the filter API
   *
   * @param $params
   */
  private function get_date_param(&$params) {
    if (empty($this->hours_since_last_check)) {
      return;
    }
    // we have hours here, create a timestamp and substract seconds
    $timestamp = strtotime('now') - ($this->hours_since_last_check * 3600);
    $params['scheduled_date'] = ['>' => date('Y-m-d H:i:s', $timestamp)];
  }


}
