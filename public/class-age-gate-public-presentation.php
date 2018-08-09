<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      2.0.0
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
class Age_Gate_Presentation extends Age_Gate_Public {
  protected $restricted;
  public function __construct()
  {
    parent::__construct();
  }

  /**
	 * Register the stylesheets for the public-facing side of the site.
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

    // add a filter for the css file
    // this will enable easier theming later
    $age_gate_css = apply_filters('age_gate_default_css', plugin_dir_url( __FILE__ ) . 'css/age-gate-public.css');
		wp_register_style( $this->plugin_name, $age_gate_css, array(), $this->version, 'all' );

    if(is_admin_bar_showing()){
      wp_register_style( $this->plugin_name . '-toolbar', AGE_GATE_URL . 'admin/css/age-gate-toolbar.css', array(), $this->version, 'all' );
      wp_enqueue_style( $this->plugin_name . '-toolbar' );
    }

		wp_register_style( $this->plugin_name . '-user-options', false );



		// enqueue default css

		if($this->settings['appearance']['styling']){
			wp_enqueue_style( $this->plugin_name );
		}

		// enqueue settings based
		if($custom = $this->_compile_css()){
			wp_add_inline_style( $this->plugin_name . '-user-options', $custom );
			wp_enqueue_style( $this->plugin_name . '-user-options' );
		}


		// enqueue custom
		if($this->settings['advanced']['custom_css']){

			// if the file isn't writable we'll assume it is not up to date and use the DB
			if($this->settings['advanced']['save_to_file'] && is_writeable(AGE_GATE_PATH . 'public/css/age-gate-custom.css') && is_readable(AGE_GATE_PATH . 'public/css/age-gate-custom.css')){
				wp_register_style( $this->plugin_name . '-custom', plugin_dir_url( __FILE__ ) . 'css/age-gate-custom.css', array(), $this->version, 'all' );
			} else {
				wp_register_style( $this->plugin_name . '-custom', false);

				$css = stripslashes(htmlspecialchars_decode( html_entity_decode($this->settings['advanced']['custom_css']), ENT_QUOTES));


				// Get the first style block

				wp_add_inline_style( $this->plugin_name . '-custom', $css );
			}

			wp_enqueue_style( $this->plugin_name . '-custom' );

		}
		// plugin_dir_url( __FILE__ ) . 'css/age-gate-custom.css'

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

    if($this->settings['advanced']['use_js']){
		  wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/age-gate-public.js', array( 'jquery' ), $this->version, true );
    } else {
      wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/age-gate-public-cookie.js', array( 'jquery' ), $this->version, true );
      $params = array(
        'errors' => array(
          'cookies' => $this->settings['messages']['cookie_message']
        )
      );
      wp_localize_script( $this->plugin_name, 'age_gate_params', $params );
    }

    if($this->settings['appearance']['auto_tab']){
      wp_enqueue_script( $this->plugin_name . '-auto-tab', plugin_dir_url( __FILE__ ) . 'js/age-gate-public-tab.js', array( 'jquery' ), $this->version, true );
    }


	}

  /**
	 * Compile user settings to css
	 * @return 	string The CSS
	 * @since 	2.0.0
	 */
	private function _compile_css()
	{
		$css = '';
		if($this->settings['appearance']['background_colour']){
			$css .= ".age-gate-wrapper { background-color: rgba(" . $this->_hex2RGB($this->settings['appearance']['background_colour']) . ", ". $this->settings['appearance']['background_opacity'] . "); }";
		}
		if($this->settings['appearance']['background_image']){
			$css .= ".age-gate-background { background-image: url(" . wp_get_attachment_url($this->settings['appearance']['background_image']) ."); opacity: ". $this->settings['appearance']['background_image_opacity'] ." }";
		}
		if($this->settings['appearance']['foreground_colour']){
			$css .= ".age-gate-form { background-color: rgba(" . $this->_hex2RGB($this->settings['appearance']['foreground_colour']) . ", ". $this->settings['appearance']['foreground_opacity'] . "); }";
		}
		if($this->settings['appearance']['text_colour']){
			$css .= ".age-gate-form, .age-gate-form label, .age-gate-form h1, .age-gate-form h2, .age-gate-form p { color: {$this->settings['appearance']['text_colour']}; }";
		}

    if($this->settings['advanced']['use_js']){
      $css .= ".age-gate-error { display: none; }";
    }

		return $css;
	}

  private function _hex2RGB($colour){
    if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return implode(', ', array( 'red' => $r, 'green' => $g, 'blue' => $b ));
  }

  public function change_page_title($title = array())
  {
    $this->is_restricted();
    if($this->restricted && $this->settings['appearance']['switch_title'] && !$this->settings['advanced']['use_js']){
      return array_merge($title, ['title' => sprintf($this->settings['appearance']['custom_title'], $this->meta->age), 'site' => get_bloginfo('name')]);
    }
    return $title;


  }

  /**
   * Get the title parts when Yoast is messing with it
   * @return mixed
   * @since 1.4.8
   */
  public function return_page_title($title)
  {

    $this->is_restricted();

    if ($this->restricted && $this->settings['appearance']['switch_title'] && !$this->_is_bot() && !$this->settings['advanced']['use_js']) {
      $sep = ' - ';
      $desc = sprintf($this->settings['appearance']['custom_title'], $this->meta->age);
      $name = get_bloginfo( 'name' );
      return "{$desc} {$sep} {$name}";
    }

    return $title;
  }


}