<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');


class Age_Gate_Common {

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  protected $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  protected $version;

  /**
   * The settings of this plugin.
   *
   * @since    2.0.0
   * @access   private
   * @var      string    $settings    The current settings of this plugin.
   */
  protected $settings;

  /**
   * The config of this plugin.
   *
   * @since    2.0.0
   * @access   private
   * @var      string    $config    The config of this plugin.
   */
  protected $config;

  /**
   * The custom form validation/sanitizer of this plugin.
   *
   * @since    2.0.0
   * @access   private
   * @var      string    $custom form validation/sanitizer    The custom form validation/sanitizer of this plugin.
   */
  protected $validation;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct() {

    $this->plugin_name = AGE_GATE_NAME;;
    $this->version = AGE_GATE_VERSION;

    $this->config = $this->_get_config();
    $this->settings = $this->_get_settings();

    $this->validation = new Age_Gate_Validation;
  }

  private function _get_config()
  {
    return (!is_admin() ? (object) array() : include AGE_GATE_PATH . 'admin/config/config.php');

  }

  private function _get_settings($type = null)
  {
    $settings = apply_filters('ag_settings', array());
    return array_merge($settings, array(
      'restrictions' => get_option('wp_age_gate_restrictions', array()),
      'messages' => get_option('wp_age_gate_messages', array()),
      'validation' => get_option('wp_age_gate_validation_messages', array()),
      'appearance' => get_option('wp_age_gate_appearance', array()),
      'access' => get_option('wp_age_gate_access', array()),
      'advanced' => get_option('wp_age_gate_advanced', array()),
    ));
  }

  protected function _set_admin_notice($notice = array())
  {
    if(!$notices = get_transient('age_gate_admin_notice')){
      $notices = array();
    }

    $notices[] = $notice;

    set_transient('age_gate_admin_notice', $notices);

  }

  protected function is_dev()
  {
    $data = get_plugin_data( AGE_GATE_PATH . 'age-gate.php' );
		$sub = explode('-', ($data['Version']));

		if(isset($sub[1]) && !empty($sub[1])){
      return $sub[1];
    }

    return false;
  }


  /**
   * Strip slashes "usefully" added by WP
   * @param  [type] $value [description]
   * @return [type]        [description]
   */
  protected function stripslashes_deep($value)
  {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
  }
}