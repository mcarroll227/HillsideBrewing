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
 * The messaging settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Messaging extends Age_Gate_Common {

  public function __construct() {

		parent::__construct();

	}

  /**
   * Add the sub menu for messaging Settings
   * @since 2.0.0
   */
  public function add_settings_page()
  {
    add_submenu_page(
			$this->plugin_name,
			'Age Gate Messaging Settings',
			'Messaging',
			AGE_GATE_CAP_MESSAGING,
			$this->plugin_name . '-messaging',
			array($this, 'display_options_page')
		);

	}

  /**
   * Display messaging settings options
   * @since 2.0.0
   */
  public function display_options_page()
  {
    $values = $this->_filter_values( $this->settings['messages'], null);
    $validation = $this->_filter_values( $this->settings['validation'], null);
    include AGE_GATE_PATH . 'admin/partials/age-gate-admin-messaging.php';
  }

  /**
   * Handle settings post from form
   * @return mixed
   * @since 2.0.0
   */
  public function handle_form_submission()
  {
    /**
     * Remove additional from post before sanitizing
     * to maintain it's html
     * @var string
     */
    $additional = $_POST['ag_settings']['additional'];

    // Sanitize the post data
    $post = $this->validation->sanitize($this->stripslashes_deep($_POST));

    $post['ag_settings']['additional'] = esc_html($additional);

    if ( ! isset( $post['nonce'] ) || ! wp_verify_nonce( $post['nonce'], 'age_gate_update_messages' ) ) {

      $this->_set_admin_notice( array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error') );

      // set_transient( 'age_gate_admin_notice',  );
      wp_redirect($post['_wp_http_referer']);
      exit;
    }

    // set empty values so everything is stored
    // this will fix the issue of some settings getting
    // overwritten on update
    $values = $this->_filter_values( $post['ag_settings'], 0);
    update_option('wp_age_gate_messages', $values);

    $validation_messages = array_merge(get_option('wp_age_gate_validation_messages', array()), $post['ag_validation']);
    update_option('wp_age_gate_validation_messages', $validation_messages);

    $this->_set_admin_notice( array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success') );

    if($this->settings['advanced']['use_js']){
      $this->_set_admin_notice( array('message' => __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate'), 'status' => 'info') );
    }

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
      'instruction',
      'messaging',
      'invalid_input_msg',
      'under_age_msg',
      'generic_error_msg',
      'remember_me_text',
      'yes_no_message',
      'yes_text',
      'no_text',
      'additional',
      'button_text',
      'cookie_message'
    ], $fill);

    return array_merge($empties, $data);
  }

  /**
   * Filter to ensure all fields get sent to the DB
   * @param  [type] $data [description]
   * @param  [type] $fill [description]
   * @return [type]       [description]
   * @since   2.0.0
   */
  private function _filter_validation($data, $fill)
  {
    $empties = array_fill_keys([
      'validate_required',
      'validate_max_len',
      'validate_min_len',
      'validate_max_numeric',
      'validate_numeric',
      'validate_min_numeric'
    ], $fill);

    return array_merge($empties, $data);
  }

  /**
	 * Callback to display remove fields from tinymce
	 * @param  mixed $args
	 * @since 1.0.0
	 */
  public function customise_tinymce($buttons)
  {
    global $hook_suffix;
    // return the orginal array if not on Age Gate
    if('age-gate_page_age-gate-messaging' !== $hook_suffix) return $buttons;

    $removeButtons = array('formatselect','blockquote','alignleft','aligncenter','alignright','wp_more','fullscreen','wp_adv');

		foreach ($buttons as $button_key => $button_value) {
			if( in_array($button_value, $removeButtons )){
				unset($buttons[$button_key]);
			}
		}

		return $buttons;
  }

}