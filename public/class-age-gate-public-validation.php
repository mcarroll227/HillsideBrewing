<?php

class Age_Gate_Validation extends GUMP {

  /**
   * Process the validation errors and return human readable error messages.
   *
   * @param bool   $convert_to_string = false
   * @param string $field_class
   * @param string $error_class
   *
   * @return array
   * @return string
   */
  public function get_readable_errors($convert_to_string = false, $field_class = 'age-gate-field', $error_class = 'gump-error-message')
  {

      if (empty($this->errors)) {
          return ($convert_to_string) ? null : array();
      }

      $resp = array();

      // Error messages
      $messages = $this->get_messages();

      foreach ($this->errors as $e) {
          $field = ucwords(str_replace($this->fieldCharsToRemove, chr(32), $e['field']));
          $param = $e['param'];

          // Let's fetch explicitly if the field names exist
          if (array_key_exists($e['field'], self::$fields)) {
              $field = self::$fields[$e['field']];

              // If param is a field (i.e. equalsfield validator)
              if (array_key_exists($param, self::$fields)) {
                  $param = self::$fields[$e['param']];
              }
          }

          // Messages
          if (isset($messages[$e['rule']])) {
              if (is_array($param)) {
                  $param = implode(', ', $param);
              }
              $message = str_replace('{param}', $param, str_replace('{field}', $field, $messages[$e['rule']]));

              if(!array_key_exists($e['field'], $resp)){
                $resp[$e['field']] = [
                  'rule' => str_replace('validate_', '', $e['rule']),
                  'message' => $message
                ];
              }
          } else {
              throw new \Exception ('Rule "'.$e['rule'].'" does not have an error message');
          }
      }

      if (!$convert_to_string) {
          return $resp;
      } else {
          $buffer = '';
          foreach ($resp as $s) {
              $buffer .= "<span class=\"$error_class\">$s</span>";
          }
          return $buffer;
      }
  }

  /**
   * Get error messages.
   *
   * @return array
   */
  protected function get_messages()
  {
      // $lang_file = __DIR__.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$this->lang.'.php';
      $messages = get_option('wp_age_gate_validation_messages', array());

      if ($validation_methods_errors = self::$validation_methods_errors) {
          $messages = array_merge($messages, $validation_methods_errors);
      }
      return $messages;
  }

}