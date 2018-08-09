<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Admin extends Age_Gate_Common {

	public function __construct() {

		parent::__construct();

		$this->_update_check();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Age_Gate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Age_Gate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, AGE_GATE_URL . 'admin/css/age-gate-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Age_Gate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Age_Gate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script('wplink');
		wp_enqueue_script( $this->plugin_name, AGE_GATE_URL . 'admin/js/age-gate-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ag', $this->_localize() );

	}

	/**
	 * Register global plugin menu item
	 *
	 * @since 1.0.0 Displays the menu
	 */
	public function add_menu_section()
	{
		$this->plugin_page_hook_suffix = add_menu_page(
			__('Age Gate', $this->plugin_name),
			__('Age Gate', $this->plugin_name),
			AGE_GATE_CAP_RESTRICTIONS,
			$this->plugin_name,
			'__return_false',//array($this, 'display_options_page'),
			'dashicons-lock',
			60
		);
	}


	public function age_gate_admin_notice() {


		if ($messages = get_transient('age_gate_admin_notice')) {

			foreach ($messages as $key => $message) {
				echo '<div id="message" class="notice notice-' . $message['status'] . ' is-dismissible"><p>'. $message['message'] .'</p></div>';
			}

		}

		if(current_user_can(AGE_GATE_CAP_ACCESS)){
			$this->_dev_notices();
		}


		delete_transient('age_gate_admin_notice');
		delete_transient('age_gate_admin_notice_css');
	}

	private function _dev_notices()
	{
		$data = get_plugin_data( AGE_GATE_PATH . 'age-gate.php' );
		$sub = explode('-', ($data['Version']));

		if(isset($sub[1]) && !empty($sub[1]) && !$this->settings['advanced']['dev_hide_warning']){

			if($this->settings['advanced']['dev_notify'] && $new = $this->_checkLatest($sub[1])){
				$messageText = sprintf(__('A new development version of Age Gate has been released. <b>%s</b> is the latest build, you have <b>%s</b>.', 'age-gate'), $new, $sub[1]);
				echo '<div id="message" class="notice notice-info is-dismissible"><p>' . $messageText . ' <a href="https://agegate.io/downloads" target="_blank">'. __('View downloads', 'age-gate') .'</a></p></div>';
			}

			$messageText = sprintf(__('You are using the <b>%s</b> version of Age Gate. This may not be suitable for production websites.', 'age-gate'), $sub[1]);
			echo '<div id="message" class="notice notice-error"><p>' . $messageText . '</p></div>';

		}
	}

	public function editor_scripts(){
		global $pagenow;
		if(isset($_REQUEST['page']) && strpos($_REQUEST['page'], 'age-gate') !== false){

			if ( ! class_exists('_WP_Editors') and ( ! defined('DOING_AJAX') or ! DOING_AJAX)) {
					require_once ABSPATH.WPINC.'/class-wp-editor.php';
					wp_print_styles('editor-buttons');
					_WP_Editors::wp_link_dialog();
			}
		}
	}

	/**
	 * Create a settings link in the plugins screen
	 *
	 * @param  mixed $links The standard links
	 * @return mixed $links	The links updated with our settings
	 * @since 1.0.0
	 */
	public function plugin_action_links( $links ) {
    $settings_link = '<a href="admin.php?page='. $this->plugin_name .'">' . __( 'Settings', 'age-gate' ) . '</a>';
    $donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40wordpressagegate%2ecom&lc=GB&item_name=Age%20Gate&item_number=Age%20Gate%20Donation&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank">' . __( 'Donate', 'age-gate' ) . '</a>';
    array_unshift( $links, $settings_link );
    array_push( $links, $donate_link );



  	return $links;
	}

	/**
	 * Create a link to website
	 *
	 * @param  mixed $plugin_meta The standard links
	 * @return mixed $plugin_file	The links updated with our settings
	 * @since 2.0.0
	 */
	public function website_link( $plugin_meta, $plugin_file ) {
		$basename = plugin_basename(AGE_GATE_PATH) . '/age-gate.php';

		if ( $basename === $plugin_file ) {

			$plugin_meta[] = sprintf(
				'<a href="%s">%s</a>',
				'https://agegate.io/docs',
				esc_html__( 'Documentation', 'age-gate' )
			);

			$addons = [];
			$addons = apply_filters('age_gate_addons', $addons);
	    $addons = array_unique($addons, SORT_REGULAR);

			if($addons){
				$message = '<style>input[value="age-gate/age-gate.php"] {pointer-events: none;cursor: default; opacity:0.3;}</style><br><br>Age Gate cannot be disabled as the following addons are activated:';
				foreach ($addons as $key => $value) {
					$message .= ' <b>' . $value['name'] . "<b>,";
				}
				$plugin_meta[] = substr($message, 0, -1);
			}
		}

		return $plugin_meta;
	}

	private function _localize()
	{
		return array(
			'uploader' => array(
				'title' => _x('Select an image to upload', 'Image uploader text', 'age-gate'),
				'button' => _x('Use this image', 'Image uploader button', 'age-gate'),
				'remove_image' => __('Remove image', 'age-gate'),

			),
			'link' => array(
				'remove_link' => __('Remove link', 'age-gate'),
				'custom' => __('Custom', 'age-gate')
			),
			'css' => array(
				'warning' => __('There are warnings in your CSS. You can save your changes, but there may be display issues.', 'age-gate'),
				'error' => __('There are errors in your CSS. Please fix these before saving.', 'age-gate'),
			),
			'update_confirm' => array(
				'warning' => _x('I have read the warning and understand updating could cause unexpected results.', 'Warning message presented when trying to update between major versions', 'age-gate'),
				'confirm' => _x('You can now proceed with the update', 'Confirm message presented on enabling update', 'age-gate')
			)
		);
	}

	/**
	 * Checks the plugin version against the stored version
	 * and updates the settings if mismatched
	 *
	 * @since 1.1.0
	 *
	 */
	private function _update_check()
	{
		if (AGE_GATE_VERSION !== get_option('wp_age_gate_version')){
			require_once AGE_GATE_PATH . 'includes/class-age-gate-activator.php';
			Age_Gate_Activator::activate();
			// update_option('wp_age_gate_version', AGE_GATE_VERSION);
		}

	}

	/**
	 * Forces use of JS age gate in some cases
	 * @return [type] [description]
	 */
	public static function force_js()
	{
		if(!function_exists('is_plugin_active')){
			// wp_die(ABSPATH);
			require_once( ABSPATH . "wp-admin/includes/plugin.php" );
		}
		return is_plugin_active('wp-e-commerce/wp-shopping-cart.php');

	}

	private function _checkLatest($current)
	{
		$x = @file_get_contents('https://agegate.io/api/downloads/v1/latest');
		if($x){
			$x = str_replace('"', '', $x);

		}

		if(!version_compare($current, $x, ">=")){
			return $x;
		}
	}




}
