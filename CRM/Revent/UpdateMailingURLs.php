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
    // get all mailings since
    $mailing_ids = $this->get_all_mailing();
    $this->filter_mailing($mailing_ids);

    // put them in array (group -> URL)
    $mapped_mailing_urls = $this->map_mailing_urls($mailing_ids);

    // add url to respective group
    $this->set_mailing_urls($mapped_mailing_urls);
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
      $group_id = $result['values'][$result['id']]['entity_id'];
      $update_group_result = civicrm_api3('Group', 'create', [
        'id' => $group_id,
        $custom_field => $mailing_url,
      ]);
      // this number is highly misleading, since it is possible and quitepossible that the same group gets updated multiple times
      // So the real amount of mailing URLs in the system is very likely to be lower
      $this->url_counter++;
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
      if ($result['count'] != 1) {
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
