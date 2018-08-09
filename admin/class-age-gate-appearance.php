<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      2.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * The appearance settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Appearance extends Age_Gate_Common {

  public function __construct() {

		parent::__construct();

	}

  /**
   * Add the sub menu for appearance Settings
   * @since 2.0.0
   */
  public function add_settings_page()
  {
    add_submenu_page(
			$this->plugin_name,
			'Age Gate Appearance Settings',
			'Appearance',
			AGE_GATE_CAP_APPEARANCE,
			$this->plugin_name . '-appearance',
			array($this, 'display_options_page')
		);
  }

  /**
   * Display appearance settings options
   * @since 2.0.0
   */
  public function display_options_page()
  {
    $values = $this->_filter_values( $this->settings['appearance'], null);

    // need to enque media for image pickers
    wp_enqueue_media();

    include AGE_GATE_PATH . 'admin/partials/age-gate-admin-appearance.php';
  }

  /**
   * Handle settings post from form
   * @return mixed
   * @since 2.0.0
   */
  public function handle_form_submission()
  {
    // Sanitize the post data
    $post = $this->validation->sanitize($this->stripslashes_deep($_POST));

    if ( ! isset( $post['nonce'] ) || ! wp_verify_nonce( $post['nonce'], 'age_gate_update_appearance' ) ) {

      $this->_set_admin_notice( array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error') );

      // set_transient( 'age_gate_admin_notice',  );
      wp_redirect($post['_wp_http_referer']);
      exit;
    }

    // set empty values so everything is stored
    // this will fix the issue of some settings getting
    // overwritten on update
    $values = $this->_filter_values( $post['ag_settings'], 0);

    update_option('wp_age_gate_appearance', $values);

    $this->_set_admin_notice( array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success') );

    if($this->settings['advanced']['use_js']){
      $this->_set_admin_notice( array('message' => __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate'), 'status' => 'info') );
    }

    // set_transient( 'age_gate_admin_notice',  );
    wp_redirect($post['_wp_http_referer']);
  }


  /**
   * Filter to ensure all fields get sent to the DB
   * @param  [type] $data [description]
   * @param  [type] $fill [description]
   * @return [type]       [description]
   * @since   2.0.0
   */
  private function _filter_values($data, $fill)
  {
    $empties = array_fill_keys([
      'logo',
      'background_colour',
      'background_opacity',
      'background_image',
      'background_image_opacity',
      'foreground_colour',
      'foreground_opacity',
      'text_colour',
      'styling',
      'device_width',
      'switch_title',
      'custom_title',
      'auto_tab'
    ], $fill);

    return array_merge($empties, $data);
  }

}