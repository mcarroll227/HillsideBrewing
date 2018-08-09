<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public
 */


/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public
 * @author     Phil Baker
 */
class Age_Gate_Submission extends Age_Gate_Public {

  public function __construct()
  {
    parent::__construct();

    Age_Gate_Validation::add_validator("nonce", function($field, $input, $param = NULL) {
      return wp_verify_nonce($input[$field], 'age_gate_form');
    });

    do_action('age_gate_add_validators');

    $error_messages = array();
    $error_messages = apply_filters('age_gate_validation_messages', $error_messages);
    if(!is_array($error_messages)){
      $error_messages = [];
    }
    $error_messages = array_merge($error_messages, [
      'nonce' => $this->settings['messages']['generic_error_msg']
    ]);

    Age_Gate_Validation::set_error_messages($error_messages);

    $field_names = array();
    $field_names = apply_filters('age_gate_field_names', $field_names);
    if(!is_array($field_names)){
      $field_names = [];
    }
    $field_names = array_merge($field_names, [
      'age_gate_d' => __('day', 'age-gate'),
      'age_gate_m' => __('month', 'age-gate'),
      'age_gate_y' => __('year', 'age-gate')
    ]);

    foreach($field_names as $field => $name){
      Age_Gate_Validation::set_field_name($field, $name);
    }

  }

  function flatten($array, $prefix = '') {
    $result = array();

    foreach ($array as $key => $value)
    {
        $new_key = $prefix . (empty($prefix) ? '' : '_') . $key;

        if (is_array($value))
        {
          $result = array_merge($result, $this->flatten($value, $new_key));
        }
        else
        {
          $result[$new_key] = $value;
        }
    }

    return $result;
  }

  /**
   * Handle the user input
   * @return 	bool True/False if it meets requirements
   * @since 	2.0.0
   */
  public function handle_form_submission()
  {
    $post = $this->validation->sanitize($_POST);
    $post['age_gate']['age'] = $this->_decode_age($post['age_gate']['age']) / $this->ag_serial ;
    $redirect = $post['_wp_http_referer'];
    $status = 'success';

    // handle if it's just buttons
    if(isset($post['age_gate']['confirm'])){
      $this->_handle_button_submission($post);

    } else {
      $this->_handle_input_submission($post);
      // else it's inputs of some kind
    }
  }

  private function _handle_button_submission($data)
  {
    $redirect = $data['_wp_http_referer'];

    $form_data = $this->flatten($data);

    if(!$form_data['age_gate_confirm']){

      // echo 'THEY CLICKED NO';
      $status = 'failed';

      $this->_set_error_message(['buttons' => $this->settings['messages']['under_age_msg']], $form_data);

      $this->age_gate_failed();

      if($this->settings['restrictions']['fail_link']){
        $redirect = $this->settings['restrictions']['fail_link'];
      }

      wp_redirect($redirect);
      exit;

    }  else {
      $is_valid = $this->_validate($form_data);

      if ($is_valid !== true) {
        $errors = $this->_filter_errors($is_valid);
        $status = 'failed';
        do_action("age_gate_form_{$status}", $this->_hook_data($form_data));

        $this->_set_error_message($errors, $form_data);
        wp_redirect($data['_wp_http_referer']);
        exit;

      } else {
        $this->_set_cookie($data['age_gate']['age'], isset($data['age_gate']['remember']));
        $status = 'success';
      }
    }

    do_action("age_gate_form_{$status}", $this->_hook_data($form_data));

    wp_redirect($redirect);
    exit;
  }
  private function _handle_input_submission($data)
  {
    $redirect = $data['_wp_http_referer'];
    $form_data = $this->flatten($data);

    $is_valid = $this->_validate($form_data);

    if ($is_valid !== true) {
      $errors = $this->_filter_errors($is_valid);
      $status = 'failed';
      do_action("age_gate_form_{$status}", $this->_hook_data($form_data));

      $this->_set_error_message($errors, $form_data);
      wp_redirect($data['_wp_http_referer']);
      exit;
    }

    // inputs are valid - check their age
    $user_age = $this->_calc_age($data['age_gate']);

    if($this->_test_user_age($user_age, $data['age_gate']['age'])){
      $this->_set_cookie($user_age, isset($data['age_gate']['remember']));
      $status = 'success';
    } else {
      $this->_set_error_message(['age_gate_failed' => $this->settings['messages']['under_age_msg']], $form_data);
      $status = 'failed';

      $this->age_gate_failed();

      if($this->settings['restrictions']['fail_link']){
        $redirect = $this->settings['restrictions']['fail_link'];
      }

    }

    do_action("age_gate_form_{$status}", $this->_hook_data($form_data));

    wp_redirect($redirect);
    exit;
  }

  private function _hook_data($data)
  {
    unset($data['age_gate_nonce']);
    unset($data['action']);
    unset($data['_wp_http_referer']);
    ksort($data);
    return $data;
  }

  /**
   * Test the age against the requirement
   * @param  int $age    Supplied Age
   * @param  int $target Required Age
   * @return bool
   */
  private function _test_user_age($age, $target)
  {
    return $age >= $target;
  }

  private function _validate($post)
  {

    $custom_rules = array();
    $custom_rules = apply_filters('age_gate_validation', $custom_rules);

    $ag_rules = [
      'age_gate_age' => 'required|numeric',
      'age_gate_nonce' => 'required|nonce'
    ];



    if($this->settings['restrictions']['input_type'] !== 'buttons'){

      $min_year = 1900;
      $min_year = apply_filters('age_gate_select_years', $min_year);



      $ag_rules = array_merge(
        [
          'age_gate_d' => 'required|numeric|min_len,2|max_len,2|max_numeric,31',
          'age_gate_m' => 'required|numeric|min_len,2|max_len,2|max_numeric,12',
          'age_gate_y' => 'required|numeric|min_len,4|max_len,4|min_numeric,'. $min_year .'|max_numeric,' . date('Y'),
        ],
        $ag_rules
      );
    }

    $validation_rules = array_merge($custom_rules, $ag_rules);


    return $this->validation->is_valid($post, $validation_rules);

  }

  /**
   * [_decode_age description]
   * @param  string $string Double encoded age
   * @return int            The decoded age
   */
  private function _decode_age($age)
  {
    return base64_decode(base64_decode($age));
  }

  /**
	 * Get the age of the user
	 * @param  mixed $dob Post array
	 * @return int   The int value of the age
	 * @since 		2.0.0
	 */
	private function _calc_age($age)
	{
    if(intval($age['y']) >= date('Y')){
      return 0;
    }

    // wp_die(date_default_timezone_get());

		$dob = intval($age['y']). '-' . str_pad(intval($age['m']), 2, 0, STR_PAD_LEFT) . '-' . str_pad(intval($age['d']), 2, 0, STR_PAD_LEFT);

    $tz = get_option('timezone_string');

    if(empty($tz)){
      $tz = date_default_timezone_get();
    }

    $timezone = new DateTimeZone($tz);

		$from = new DateTime($dob, $timezone);
		$to   = new DateTime('today', $timezone);
		return $from->diff($to)->y;
	}

  private function _set_cookie($age, $remember)
  {
    $set_cookie = true;
    $set_cookie = apply_filters('age_gate_set_cookie', $set_cookie);
    $length = ($remember) ? strtotime('+' . $this->settings['restrictions']['remember_days'] . ' ' . $this->settings['restrictions']['remember_timescale']) : 0;
    $age = (!$this->settings['advanced']['anonymous_age_gate']) ? $age : 1;

    if($set_cookie){
      setcookie( 'age_gate', abs(ceil($age)), $length, COOKIEPATH, COOKIE_DOMAIN);
    }
    // die();
  }


  private function _set_error_message($message, $data = array())
  {
    $id = time() . abs( 2147483648 + mt_rand( -2147482448, 2147483647 ) * mt_rand( -2147482448, 2147483647 ) );
    set_transient($id . '_age_gate_error', $message, "+5 mins");
    set_transient($id . '_age_gate_submitted', $data, "+5 mins");
    setcookie( 'age_gate_error', $id, 0, COOKIEPATH, COOKIE_DOMAIN);
  }
}