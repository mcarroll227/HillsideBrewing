<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Fired during plugin activation
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 * @author     Phil Baker
 */
class Age_Gate_Activator {

	private static $installed_version;
	private static $migrate = false;

	/**
	 * Add / update default settings
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// // return;
		self::$installed_version = get_option('wp_age_gate_version');

		if (version_compare(self::$installed_version, '2.0.0', '<')) {
			self::$migrate = get_option('wp_age_gate_general');
	  	// wp_die('I am under Age Gate version 2.0.0 - ' . self::$installed_version );
		}

		self::restrictions();
		self::messages();
		self::appearance();
		self::advanced();
		self::post_types();
		if(!self::$installed_version || self::$migrate){
			self::caps();
		}


		// Age_Gate_Public::generate_unique_id();

		update_option('wp_age_gate_version', AGE_GATE_VERSION);
		update_option('wp_age_gate_api',
			[
				'base' => 'https://agegate.io',
				'api' => '/api/license-manager/v1'
			]
		);

		// Don't remove the old options when first upgrading,
		// remove on the next release
		if(version_compare(AGE_GATE_VERSION, '2.5.0', '>=')){
			delete_option('wp_age_gate_general');
		}
	}

	private static function caps()
	{
		$caps = [
			'manage_options' => array(
				'ag_manage_restrictions',
				'ag_manage_appearance',
				'ag_manage_advanced',
				'ag_manage_messaging',
				'ag_manage_settings'
			),
			'edit_posts' => array(
				'ag_manage_set_content_restriction',
				'ag_manage_set_content_bypass',
				'ag_manage_set_custom_age'
			)
		];



		$roles = wp_roles();

		foreach($roles->roles as $key => $role){
			$r = get_role( $key );
			if(array_key_exists('manage_options', $role['capabilities'])){
				foreach ($caps['manage_options'] as $cap) {
					$r->add_cap( $cap );
				}
			}

			if(array_key_exists('edit_posts', $role['capabilities'])){
				foreach ($caps['edit_posts'] as $cap) {
					$r->add_cap( $cap );
				}
			}


		}

	}


	private static function restrictions()
	{
		$locale = get_locale();
		$defaults = array(
      'min_age' => ($locale == 'en_GB') ? 18 : 21,
      'restriction_type' => 'all',
      'multi_age' => 0,
      'restrict_register' => 1,
      'input_type' => 'inputs',
      'remember' => 0,
      'remember_days' => 365,
			'remember_timescale' => 'days',
      'remember_auto_check' => 0,
      'date_format' => ($locale == 'en_GB') ? 'ddmmyyyy' : 'mmddyyyy',
      'ignore_logged' => 0,
      'rechallenge' => 1,
      'fail_link_title' => null,
      'fail_link' => null,
    );

		// handle update from v1 to v2
		if(self::$migrate){
			$defaults['min_age'] = (!isset(self::$migrate['wp_age_gate_min_age']) ? $defaults['min_age'] : self::$migrate['wp_age_gate_min_age']);
			$defaults['restriction_type'] = (!isset(self::$migrate['wp_age_gate_restriction_type']) ? $defaults['restriction_type'] : self::$migrate['wp_age_gate_restriction_type']);
			$defaults['restrict_register'] = (!isset(self::$migrate['wp_age_gate_restrict_register']) ? $defaults['restrict_register'] : self::$migrate['wp_age_gate_restrict_register']);
			$defaults['input_type'] = (!isset(self::$migrate['wp_age_gate_input_type']) ? $defaults['input_type'] : self::$migrate['wp_age_gate_input_type']);
			$defaults['remember'] = (!isset(self::$migrate['wp_age_gate_remember']) ? $defaults['remember'] : self::$migrate['wp_age_gate_remember']);
			$defaults['remember_days'] = (!isset(self::$migrate['wp_age_gate_remember_days']) ? $defaults['remember_days'] : self::$migrate['wp_age_gate_remember_days']);
			$defaults['remember_auto_check'] = (!isset(self::$migrate['wp_age_gate_remember_auto_check']) ? $defaults['remember_auto_check'] : self::$migrate['wp_age_gate_remember_auto_check']);
			$defaults['date_format'] = (!isset(self::$migrate['wp_age_gate_date_format']) ? $defaults['date_format'] : self::$migrate['wp_age_gate_date_format']);
			$defaults['ignore_logged'] = (!isset(self::$migrate['wp_age_gate_ignore_logged']) ? $defaults['ignore_logged'] : self::$migrate['wp_age_gate_ignore_logged']);
			$defaults['rechallenge'] = (isset(self::$migrate['wp_age_gate_no_second_chance']) ? 0 : 1);
			$defaults['fail_link_title'] = (!isset(self::$migrate['wp_age_gate_fail_link_title']) ? $defaults['fail_link_title'] : self::$migrate['wp_age_gate_fail_link_title']);
			$defaults['fail_link'] = (!isset(self::$migrate['wp_age_gate_fail_link']) ? $defaults['fail_link'] : self::$migrate['wp_age_gate_fail_link']);
		}

		$user_settings = get_option('wp_age_gate_restrictions', array());

		update_option('wp_age_gate_restrictions', array_merge($defaults, $user_settings));
	}

	private static function messages()
	{
		$defaults = array(
			'instruction' => '',
	    'messaging' => '',
	    'invalid_input_msg' => __('Your input was invalid', 'age-gate'),
	    'under_age_msg' => __('You are not old enough to view this content', 'age-gate'),
	    'generic_error_msg' => __('An error occurred, please try again', 'age-gate'),
			'remember_me_text' => __('Remember me', 'age-gate'),
	    'yes_no_message' => __('Are you over %s years of age?', 'age-gate'),
	    'yes_text' => __('Yes', 'age-gate'),
	    'no_text' => __('No', 'age-gate'),
	    'additional' => '',
	    'button_text' => __('Submit', 'age-gate'),
			'cookie_message' => __('Your browser does not support cookies, you may experience problems entering this site', 'age-gate')
		);

		if(self::$migrate){
			$defaults['instruction'] = (!isset(self::$migrate['wp_age_gate_instruction'])) ? $defaults['instruction'] : self::$migrate['wp_age_gate_instruction'];
			$defaults['messaging'] = (!isset(self::$migrate['wp_age_gate_messaging'])) ? $defaults['messaging'] : self::$migrate['wp_age_gate_messaging'];
			$defaults['invalid_input_msg'] = (!isset(self::$migrate['wp_age_gate_invalid_input_msg'])) ? $defaults['invalid_input_msg'] : self::$migrate['wp_age_gate_invalid_input_msg'];
			$defaults['under_age_msg'] = (!isset(self::$migrate['wp_age_gate_under_age_msg'])) ? $defaults['under_age_msg'] : self::$migrate['wp_age_gate_under_age_msg'];
			$defaults['generic_error_msg'] = (!isset(self::$migrate['wp_age_gate_generic_error_msg'])) ? $defaults['generic_error_msg'] : self::$migrate['wp_age_gate_generic_error_msg'];
			$defaults['remember_me_text'] = (!isset(self::$migrate['wp_age_gate_remember_me_text'])) ? $defaults['remember_me_text'] : self::$migrate['wp_age_gate_remember_me_text'];
			$defaults['yes_no_message'] = (!isset(self::$migrate['wp_age_gate_yes_no_message'])) ? $defaults['yes_no_message'] : self::$migrate['wp_age_gate_yes_no_message'];
			$defaults['additional'] = (!isset(self::$migrate['wp_age_gate_additional'])) ? $defaults['additional'] : self::$migrate['wp_age_gate_additional'];
			$defaults['button_text'] = (!isset(self::$migrate['wp_age_gate_button_text'])) ? $defaults['button_text'] : self::$migrate['wp_age_gate_button_text'];
		}

		$user_settings = get_option('wp_age_gate_messages', array());
		update_option('wp_age_gate_messages', array_merge($defaults, $user_settings));


		$messages = array(
				'validate_required'                 => 'The {field} field is required',
				'validate_valid_email'              => 'The {field} field must be a valid email address',
				'validate_max_len'                  => 'The {field} field needs to be {param} characters or less',
				'validate_min_len'                  => 'The {field} field needs to be at least {param} characters',
				'validate_exact_len'                => 'The {field} field needs to be exactly {param} characters',
				'validate_alpha'                    => 'The {field} field may only contain letters',
				'validate_alpha_numeric'            => 'The {field} field may only contain letters and numbers',
				'validate_alpha_numeric_space'      => 'The {field} field may only contain letters, numbers and spaces',
				'validate_alpha_dash'               => 'The {field} field may only contain letters and dashes',
				'validate_alpha_space'              => 'The {field} field may only contain letters and spaces',
				'validate_numeric'                  => 'The {field} field must be a number',
				'validate_integer'                  => 'The {field} field must be a number without a decimal',
				'validate_boolean'                  => 'The {field} field has to be either true or false',
				'validate_float'                    => 'The {field} field must be a number with a decimal point (float)',
				'validate_valid_url'                => 'The {field} field has to be a URL',
				'validate_url_exists'               => 'The {field} URL does not exist',
				'validate_valid_ip'                 => 'The {field} field needs to be a valid IP address',
				'validate_valid_ipv4'               => 'The {field} field needs to contain a valid IPv4 address',
				'validate_valid_ipv6'               => 'The {field} field needs to contain a valid IPv6 address',
				'validate_guidv4'                   => 'The {field} field needs to contain a valid GUID',
				'validate_valid_cc'                 => 'The {field} is not a valid credit card number',
				'validate_valid_name'               => 'The {field} should be a full name',
				'validate_contains'                 => 'The {field} can only be one of the following: {param}',
				'validate_contains_list'            => 'The {field} is not a valid option',
				'validate_doesnt_contain_list'      => 'The {field} field contains a value that is not accepted',
				'validate_street_address'           => 'The {field} field needs to be a valid street address',
				'validate_date'                     => 'The {field} must be a valid date',
				'validate_min_numeric'              => 'The {field} field needs to be a numeric value, equal to, or higher than {param}',
				'validate_max_numeric'              => 'The {field} field needs to be a numeric value, equal to, or lower than {param}',
				'validate_min_age'                  => 'The {field} field needs to have an age greater than or equal to {param}',
				'validate_invalid'                  => 'The {field} field is invalid',
				'validate_starts'                   => 'The {field} field needs to start with {param}',
				'validate_extension'                => 'The {field} field can only have one of the following extensions: {param}',
				'validate_required_file'            => 'The {field} field is required',
				'validate_equalsfield'              => 'The {field} field does not equal {param} field',
				'validate_iban'                     => 'The {field} field needs to contain a valid IBAN',
				'validate_phone_number'             => 'The {field} field needs to be a valid Phone Number',
				'validate_regex'                    => 'The {field} field needs to contain a value with valid format',
				'validate_valid_json_string'        => 'The {field} field needs to contain a valid JSON format string',
				'validate_valid_array_size_greater' => 'The {field} fields needs to be an array with a size, equal to, or higher than {param}',
				'validate_valid_array_size_lesser'  => 'The {field} fields needs to be an array with a size, equal to, or lower than {param}',
				'validate_valid_array_size_equal'   => 'The {field} fields needs to be an array with a size equal to {param}',
				'validate_valid_persian_name'       => 'The {field} should be a valid Persian/Dari or Arabic name',
			'validate_valid_eng_per_pas_name'   => 'The {field} should be a valid English, Persian/Dari/Pashtu or Arabic name',
			'validate_valid_persian_digit'      => 'The {field} should be a valid digit in Persian/Dari or Arabic format',
			'validate_valid_persian_text'       => 'The {field} should be a valid text in Persian/Dari or Arabic format',
			'validate_valid_pashtu_text'        => 'The {field} should be a valid text in Pashtu format',
		);

		$user_settings = get_option('wp_age_gate_validation_messages', array());
		update_option('wp_age_gate_validation_messages', array_merge($messages, $user_settings));
	}

	private static function appearance()
	{

		// create serial
		$serial = abs( 2147483648 + mt_rand( -2147482448, 2147483647 ) * mt_rand( -2147482448, 2147483647 ) );
		update_option('age_gate_serial', $serial);

		$defaults = array(
			'logo' => null,
	    'background_colour' => null,
			'background_opacity' => 1,
	    'background_image' => null,
			'background_image_opacity' => 1,
	    'foreground_colour' => null,
			'foreground_opacity' => 1,
	    'text_colour' => null,
	    'styling' => 1,
	    'device_width' => 1,
	    'switch_title' => 0,
			'custom_title' => 'Age Verification',
			'auto_tab' => 0
		);

		if(self::$migrate){
			$defaults['logo'] = (!isset(self::$migrate['wp_age_gate_logo'])) ? $defaults['logo'] : self::$migrate['wp_age_gate_logo'];
	    $defaults['background_colour'] = (!isset(self::$migrate['wp_age_gate_background_colour'])) ? $defaults['background_colour'] : self::$migrate['wp_age_gate_background_colour'];
	    $defaults['background_image'] = (!isset(self::$migrate['wp_age_gate_background_image'])) ? $defaults['background_image'] : self::$migrate['wp_age_gate_background_image'];
	    $defaults['foreground_colour'] = (!isset(self::$migrate['wp_age_gate_foreground_colour'])) ? $defaults['foreground_colour'] : self::$migrate['wp_age_gate_foreground_colour'];
	    $defaults['text_colour'] = (!isset(self::$migrate['wp_age_gate_text_colour'])) ? $defaults['text_colour'] : self::$migrate['wp_age_gate_text_colour'];
	    $defaults['styling'] = (!isset(self::$migrate['wp_age_gate_styling'])) ? $defaults['styling'] : self::$migrate['wp_age_gate_styling'];
	    $defaults['device_width'] = (!isset(self::$migrate['wp_age_gate_device_width'])) ? $defaults['device_width'] : self::$migrate['wp_age_gate_device_width'];
	    $defaults['switch_title'] = (!isset(self::$migrate['wp_age_gate_switch_title'])) ? $defaults['switch_title'] : self::$migrate['wp_age_gate_switch_title'];
		}

		$user_settings = get_option('wp_age_gate_appearance', array());

		update_option('wp_age_gate_appearance', array_merge($defaults, $user_settings));
	}

	private static function advanced()
	{
		$defaults = array(
			'use_js' => 0,
			'save_to_file' => 0,
			'custom_css' => '',
			'restrict_archives' => 0,
			'dev_notify' => 1,
			'dev_hide_warning' => 0,
			'anonymous_age_gate' => 0
		);

		if(self::$migrate){
			$defaults['use_js'] = (isset(self::$migrate['wp_age_gate_use_js']) && self::$migrate['wp_age_gate_use_js'] === 'js') ? 1 : $defaults['use_js'];
		}

		$user_settings = get_option('wp_age_gate_advanced', array());

		if(Age_Gate_Admin::force_js()){
			$user_settings['use_js'] = 1;
		}

		$update = array_merge($defaults, $user_settings);
		self::rewriteCSS($update);


		update_option('wp_age_gate_advanced', $update);
	}

	private static function access()
	{

    $empties = array_fill_keys([
      'permissions',
      'post_types'
    ], array());

    return array_merge($empties, $data);
	}

	private static function post_types()
	{
		$types = [];
		foreach (get_post_types('', 'objects') as $key => $post_type){
			switch($key){
				case 'acf-field-group':
				case 'acf-field':
					$type[$key] = 1;
				break;
				default:
					$types[$key] = 0;
			}
		}

		$post_types = array_merge($types, get_option('wp_age_gate_access', array()));
		update_option('wp_age_gate_access', $post_types);
	}

	private static function rewriteCSS($data)
	{
		if($data['save_to_file'] && $data['custom_css'] && is_writable(AGE_GATE_PATH . 'public/css/age-gate-custom.css')){

      file_put_contents(AGE_GATE_PATH . 'public/css/age-gate-custom.css', stripslashes(htmlspecialchars_decode( html_entity_decode($data['custom_css']), ENT_QUOTES)) );
    }
	}


}
