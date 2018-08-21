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
class CRM_Revent_Form_RegistrationCustomisation extends CRM_Core_Form {

  protected $registrationRenderer   = NULL;
  protected $default_data           = NULL;
  protected $data                   = NULL;
  protected $event_id               = NULL;
  protected $replacement_index      = array();

  public function buildQuickForm() {

    // check php configuration
    $this->checkPHPConfig();

    CRM_Utils_System::setTitle(ts('Registration Customisation', array('domain' => 'de.systopia.revent')));

    // get event ID
    $event_id = CRM_Utils_Request::retrieve('eid', 'Positive', $this);
    if (!$event_id) {
      CRM_Core_Error::fatal(ts('No event ID (eid) given.', array('domain' => 'de.systopia.revent')));
    } else {
      $this->add('hidden', 'eid', $event_id);
      $this->event_id = $event_id;
    }

    // load currently customised data
    $this->registrationRenderer = new CRM_Revent_RegistrationFields(array('id' => $event_id));
    $data = $this->registrationRenderer->renderEventRegistrationForm();

    if (empty($data['fields']) || empty($data['groups'])) {
      // nothing to do here for us, call parent render and quit
      parent::buildQuickForm();
      return;
    }
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
      if (!empty($group['fields'])) {
        usort($group['fields'], array('CRM_Revent_Form_RegistrationCustomisation', 'compareHelper'));
      }
      // FixMe: Still static language supprt here (as above). Needs generic solution in future releases
    }

    foreach ($data['groups'] as &$display_grp) {
      $display_grp['languages'] = array('de', 'en');
    }

    // add arrays for option counts
    foreach ($data['groups'] as &$grp) {
      if (empty($grp['fields'])) {
        continue;
      }
      foreach ($grp['fields'] as &$fld) {
        if (isset($fld['options'])) {
          // create a counter array for the template
          $fld['option_count'] = range(1, count($fld['options']));
          // check if options are available for languages, otherwise copy the default value to the groups
          foreach ($fld['languages'] as $lang) {
            if (empty($fld["options_{$lang}"])) {
              $fld["options_{$lang}"] = $fld['options'];
            }
          }
        }
      }
    }

    // some group names contain a '.', which messes with the quickform api or something else.
    // replace them with _, and store them locally, to be mapped afterwards
    foreach ($data['groups'] as &$g) {
      if (empty($g['fields'])) {
        continue;
      }
      foreach ($g['fields'] as &$f) {
        if (strpos($f['name'], '.')) {
          // we need to replace this, and map it locally
          $replacement_name = str_replace('.', '_', $f['name']);
          $this->replacement_index[$replacement_name] = $f['name'];
          $f['name'] = $replacement_name;
        }
      }
    }

    // data is now sorted. create forms now for all groups
    foreach($data['groups'] as $indexed_group) {
      foreach ($indexed_group['languages'] as $display_lang) {
        $def_value = $indexed_group['title'];
        if (isset($indexed_group["title_{$display_lang}"])) {
          $def_value = $indexed_group["title_{$display_lang}"];
        }
        $this->createGroupElements($indexed_group['name'], $display_lang, $def_value);
      }
      if (empty($indexed_group['fields'])) {
        continue;
      }
      foreach ($indexed_group['fields'] as $indexed_field) {
        foreach ($indexed_field['languages'] as $indexed_language) {
          if (!isset($indexed_field['maxlength'])) {
            $indexed_field['maxlength'] = 128;
          }
          if (isset($indexed_field["options_{$indexed_language}"])) {
            // If we have option types, then this is just a name, thus no maxLength is needed. Setting default to 128 for now
            $this->createFormElements($indexed_group['name'], $indexed_field['name'], 128, $indexed_language);
          } else {
            $this->createFormElements($indexed_group['name'], $indexed_field['name'], $indexed_field['maxlength'], $indexed_language);
          }
          $this->createDefaultFormValues($indexed_field, $indexed_group['name'], $indexed_field['name'], $indexed_language);
          if (isset($indexed_field["options_{$indexed_language}"])) {
            // iterate over options field, but create form elements for options_{language}
            foreach ($indexed_field["options"] as $key => $option) {
              if (isset($indexed_field["options_{$indexed_language}"][$key])) {
                $opt = $indexed_field["options_{$indexed_language}"][$key];
              } else {
                $opt = "";
              }
              $this->createFormElementOptions($indexed_group['name'], $indexed_field['name'], $key, $indexed_language, $opt);
            }
          }
        } // for loop over field languages
      } // for loop fields
    } // for loop groups

    // save the data structure
    $this->data= $data;

    // assign the whole groups array to the template form
    $this->assign('groups', $data['groups']);

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

  /**
   * set the default (=current) values in the form
   */
  public function setDefaultValues() {
    return $this->default_data;
  }


  /**
   * post processing function
   */
  public function postProcess() {

    $values = $this->exportValues();
    $groups = array();
    $fields = array();

    $pattern = "/(?<type>[A-Za-z]+)__(?<group>[A-Za-z0-9-._]+)__(?<field>[0-9A-Za-z_.]+)__(?<language>[a-z0]{1,2})$/";
    $option_pattern = "/option__(?<group>[A-Za-z0-9-._]+)__(?<field>[0-9A-Za-z_.]+)__(?<index>[0-9]*?)__(?<language>[a-z0]{1,2})$/";
    $group_name_patter = "/group__display__(?<group>[A-Za-z0-9-._]+)__(?<language>[a-z0]{1,2})$/";
    $matches = array();
    // iterate values, then build group and fields array
    foreach ($values as $name => $value) {

      if (preg_match($group_name_patter, $name, $matches)) {
        $group = $matches['group'];
        $language = $matches['language'];
        foreach ($this->data['groups'] as &$display_group) {
          if ($display_group['name'] === $group) {
            $display_group["title_{$language}"] = $value;
          }
        }
      } elseif (preg_match($option_pattern, $name, $matches)) {
        $field    = $matches['field'];
        $language = $matches['language'];
        $index    = $matches['index'];

        //error_log("DEBUG OPTION GROUP: {$name} -> {$value}; Field: {$field}, language: {$language}, index: {$index}");

        // Check if we did replacements for the field name. If so, overwrite them
        if (isset($this->replacement_index[$field])) {
          $field = $this->replacement_index[$field];
        }

        if (!isset($fields[$field])) {
          $fields[$field] = array();
        }
        if (!isset($fields[$field]["options_{$language}"])) {
          $fields[$field]["options_{$language}"] = array();
        }
        $fields[$field]["options_{$language}"][$index] = $value;

      } elseif (preg_match($pattern, $name, $matches)) {

        $type     = $matches['type'];
        $group    = $matches['group'];
        $field    = $matches['field'];
        $language = $matches['language'];

        // Check if we did replacements for the field name. If so, overwrite them
        if (isset($this->replacement_index[$field])) {
          $field = $this->replacement_index[$field];
        }

        // if language is 0, then this is the default value for the type,
        // otherwise only set the language specific field
        if ($language === "0") {
          // we don't need to do anything here, but might for future releases
          $fields[$field][$type] = $value;
        } else {
          $fields[$field]['group'] = $group;
          $fields[$field]['name'] = $field;
          // handle 'description' specially, because this is not a required field,
          // but can be added nonetheless
          if ($type == 'description') {
            $fields[$field][$type . "_" . $language] = $value;
          } elseif (isset($this->data['fields'][$field][$type . "_" . $language])) {
            $fields[$field][$type . "_" . $language] = $value;
          } elseif($type === "required" || $type === "weight") {
            // if type == required, then the value will be empty. We need to set 0 for this
            if ($type === "required" && empty($value) && !isset($fields[$field][$type])) {
              $fields[$field][$type] = "0";
            } elseif (!isset($fields[$field][$type])) {
              $fields[$field][$type] = $value;
            }
          }
        }
      } else {
        continue;
      }
    }

    foreach ($this->data['groups'] as $group) {
      unset($group['fields']);
      // cleanup additional language field in group array
      unset($group['languages']);
      $groups[] = $group;
    }

    // store it
    $data = $this->registrationRenderer->updateCustomisation($groups, $fields);
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/event/info',
      "id={$this->event_id}&reset=1"));
    parent::postProcess();
  }

  // Helper functions

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
   * Checks the local php config
   *
   * creates a warning if 'max_input_vars' is set to less than 10000
   */
    private function checkPHPConfig() {
      $php_var_count = ini_get('max_input_vars');
      if ($php_var_count < 10000) {
        CRM_Core_Session::setStatus(
          ts("The php configuration for 'max_input_vars' is currently set to {$php_var_count}. It should be increased to 10000 for large forms. "),
          ts("PHP Configuration Warning"),
          "info",
          array('unique' => true, 'expires' => 0));
      }
    }

  /**
   * @param $group_title
   * @param $language
   * @param $default_value
   */
    private function createGroupElements($group_title, $language, $default_value) {
      $this->add(
        'text',
        "group__display__{$group_title}__{$language}",
        ts('Group Display Name', array('domain' => 'de.systopia.revent')),
        array(
          'class'     => 'huge',
        )
      );
      $this->default_data["group__display__{$group_title}__{$language}"] = $default_value;
    }

  /**
   * creates form elements indexed by group, field and language
   *
   * @param $group_title
   * @param $field_title
   * @param $maxlength_title
   * @param int $language = 0
   * @param int $maxlength_description = 1000
   */
    private function createFormElements($group_title, $field_title, $maxlength_title, $language = 0, $maxlength_description = 1000) {
      $this->add(
        'text',
        "title__{$group_title}__{$field_title}__{$language}",
        ts('title', array('domain' => 'de.systopia.revent')),
        array(
          'class'     => 'huge',
          'maxlength' => $maxlength_title
        )
      );
      $this->add(
        'text',
        "description__{$group_title}__{$field_title}__{$language}",
        ts('description', array('domain' => 'de.systopia.revent')),
        array(
          'class'     => 'huge',
          'maxlength' => $maxlength_description
        )
      );
      $this->add(
        'advcheckbox',
        "required__{$group_title}__{$field_title}__{$language}",
        ts('Required', array('domain' => 'de.systopia.revent'))
      );
      $this->add(
        'text',
        "weight__{$group_title}__{$field_title}__{$language}",
        ts('Position', array('domain' => 'de.systopia.revent')),
        array(
          'size' => '2'
        )
      );
    }

  /**
   * creates the option fields for the form elements
   * @param $group_title
   * @param $field_title
   * @param $option
   * @param $language
   * @param $default_value
   */
    private function createFormElementOptions($group_title, $field_title, $option, $language, $default_value) {
      $this->add(
        'text',
        "option__{$group_title}__{$field_title}__{$option}__{$language}",
        ts('option', array('domain' => 'de.systopia.revent')),
        array(
          'class' => 'huge'
        )
      );
      $this->default_data["option__{$group_title}__{$field_title}__{$option}__{$language}"] = $default_value;
    }

  /**
   * creates default values for the form
   *
   * @param $value
   * @param $group_title
   * @param $field_title
   * @param int $language
   */
    private function createDefaultFormValues($value, $group_title, $field_title, $language = 0) {

      if ($language == "0") {
        $this->default_data["title__{$value['group']}__{$value['name']}__0"] = $value['title'];
        if (isset($value['description'])) {
          $this->default_data["description__{$value['group']}__{$value['name']}__0"] = $value['description'];
        }
        $this->default_data["required__{$value['group']}__{$value['name']}__0"] = $value['required'];
        $this->default_data["weight__{$value['group']}__{$value['name']}__0"] = $value['weight'];
      } else {
        $this->default_data["title__{$value['group']}__{$value['name']}__{$language}"] = $value["title_{$language}"];
        if (isset($value["description_{$language}"])) {
          $this->default_data["description__{$value['group']}__{$value['name']}__{$language}"] = $value["description_{$language}"];
        } else {
          $this->default_data["description__{$value['group']}__{$value['name']}__{$language}"] = "";
        }
        $this->default_data["required__{$value['group']}__{$value['name']}__{$language}"] = $value['required'];
        $this->default_data["weight__{$value['group']}__{$value['name']}__{$language}"] = $value['weight'];
      }

    }

  }
