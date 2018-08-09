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
 * Adds the restriction options to the posts types
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Post_Types extends Age_Gate_Common {

  public function __construct() {

		parent::__construct();

	}

  public function enqueue_scripts()
  {
    wp_enqueue_script( $this->plugin_name .'-post', AGE_GATE_URL . 'admin/js/age-gate-post.js', array( 'jquery' ), $this->version, true );

    wp_localize_script( $this->plugin_name .'-post', 'ag_post', array(
      'restricted_to' => __('Restricted to', 'age-gate'),
      'change' => __('Change', 'age-gate'),
      'unrestricted' => __('Unrestricted', 'age-gate'),
    ));

  }

  /**
   * [add_restriction_options description]
   * @since 2.0.0
   */
  public function add_restriction_options(){
    if(!isset($this->settings['access'][get_post_type()]) || !$this->settings['access'][get_post_type()]){
      $id = get_the_ID();
      $post_meta = array(
        'age' => get_post_meta( $id, '_age_gate-age', true ),
        'bypass' => get_post_meta( $id, '_age_gate-bypass', true ),
        'restrict' => get_post_meta( $id, '_age_gate-restrict', true ),
      );

      if (empty($post_meta['age'])) {
        $post_meta['age'] = $this->settings['restrictions']['min_age'];
      }

      $post_meta['is_restricted'] = $this->settings['restrictions']['restriction_type'] === 'selected' && $post_meta['restrict'] || $this->settings['restrictions']['restriction_type'] !== 'selected' && !$post_meta['bypass'];

      include AGE_GATE_PATH . 'admin/partials/parts/publish-options.php';
    }
  }

  /**
   * Save any custom options on a per post basis
   * @return [type] [description]
   * @since 2.0.0
   *
   */
  public function save_post($post_id){
    // Return if autosaving
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || !isset($_POST['ag_settings'])) return;

    $post = $this->_filter_values($this->validation->sanitize($_POST['ag_settings']), null);

    // check the nonce
    if ( ! wp_verify_nonce( $post['ag_nonce'], 'ag_save_post' ) ) {
			return;
		}

    // set custom age
    $this->_set_age($post['age'], $post_id);
    $this->_set_restrict($post['restrict'], $post_id);
    $this->_set_bypass($post['bypass'], $post_id);


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
      'bypass',
      'restrict',
      'age',
    ], $fill);

    return array_merge($empties, $data);
  }

  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_age($age, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE)){
      return false;
    }

    if(!$this->settings['restrictions']['multi_age']){
      return false;
    }

    if(!$age || $age == $this->settings['restrictions']['min_age']){
      delete_post_meta($id, '_age_gate-age');
    } else {
      update_post_meta( $id, '_age_gate-age', $age );

    }
  }

  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_restrict($restrict, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_CONTENT)){

      return false;
    }

    if ($this->settings['restrictions']['restriction_type'] !== 'selected') {

      return false;
    }

    if(!$restrict){

      delete_post_meta($id, '_age_gate-restrict');
    } else {

      update_post_meta($id, '_age_gate-restrict', 1);
    }
    // _age_gate-restrict
  }


  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_bypass($bypass, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_BYPASS)){

      return false;
    }

    if ($this->settings['restrictions']['restriction_type'] !== 'all') {

      return false;
    }

    if(!$bypass){

      delete_post_meta($id, '_age_gate-bypass');
    } else {

      update_post_meta($id, '_age_gate-bypass', 1);
    }
    // all
    // _age_gate-bypass
  }

}