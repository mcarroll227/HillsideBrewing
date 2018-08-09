<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public/partials
 */
?>
<div class="wrap">
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <?php include AGE_GATE_PATH . 'admin/partials/parts/tabs.php'; ?>

  <form class="custom-form-fields" action="admin-post.php" method="post">
    <input type="hidden" name="action" value="age_gate_messages">
    <?php wp_nonce_field( 'age_gate_update_messages', 'nonce'); ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="wp_age_gate_instruction"><?php _e("Headline", 'age-gate') ;?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[instruction]',
              'type' => 'text',
              'id' => 'wp_age_gate_instruction'
            ), $values['instruction'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_messaging"><?php _e("Sub headline", 'age-gate') ;?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[messaging]',
              'type' => 'text',
              'id' => 'wp_age_gate_messaging'
            ), $values['messaging'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="wp_age_gate_remember_me_text"><?php _e("Remember me text", 'age-gate') ;?></label></th>
          <td>
            <?php if(!$this->settings['restrictions']['remember']): ?>
              <?php _e("Only applicable if Remember me is enabled", 'age-gate'); ?>
              <?php echo form_hidden('ag_settings[remember_me_text]', $values['remember_me_text']); ?>
            <?php else: ?>
            <?php echo form_input(array(
              'name' => 'ag_settings[remember_me_text]',
              'type' => 'text',
              'id' => 'wp_age_gate_remember_me_text'
            ), $values['remember_me_text'], array('class' => 'regular-text ltr'));
            ?>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_yes_no_message"><?php _e("Yes/No sub question", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>
              <?php echo form_hidden('ag_settings[yes_no_message]', $values['yes_no_message']); ?>
            <?php else: ?>
            <?php echo form_input(array(
              'name' => 'ag_settings[yes_no_message]',
              'type' => 'text',
              'id' => 'wp_age_gate_yes_no_message'
            ), $values['yes_no_message'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_yes_text"><?php _e("Yes button text", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>
              <?php echo form_hidden('ag_settings[yes_text]', $values['yes_text']); ?>
            <?php else: ?>
            <?php echo form_input(array(
              'name' => 'ag_settings[yes_text]',
              'type' => 'text',
              'id' => 'wp_age_gate_yes_text'
            ), $values['yes_text'], array('class' => 'small-text ltr'));
            ?> <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>
          <?php endif; ?>
          </td>

        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_no_text"><?php _e("No button text", 'age-gate') ;?></label></th>
          <td>
            <?php if ($this->settings['restrictions']['input_type'] !== 'buttons'): ?>
              <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>
              <?php echo form_hidden('ag_settings[no_text]', $values['no_text']); ?>
            <?php else: ?>
            <?php echo form_input(array(
              'name' => 'ag_settings[no_text]',
              'type' => 'text',
              'id' => 'wp_age_gate_no_text'
            ), $values['no_text'], array('class' => 'small-text ltr'));
            ?> <?php _e("Only applicable if using yes/no buttons", 'age-gate'); ?>
            <?php endif; ?>
          </td>

        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_button_text"><?php _e('Submit button text', 'age-gate'); ?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[button_text]',
              'type' => 'text',
              'id' => 'wp_age_gate_button_text'
            ), $values['button_text'], array('class' => 'medium-text ltr'));
            ?>

          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_cookie_message"><?php _e("No cookies message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[cookie_message]',
              'type' => 'text',
              'id' => 'wp_age_gate_cookie_message'
            ), $values['cookie_message'], array('class' => 'regular-text ltr'));
            ?>

          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_additional"><?php _e('Additional content', 'age-gate'); ?></label>
          </th>
          <td>
            <p><?php _e('Use this area to add an addtional info or terms of entry.', 'age-gate'); ?></p><br />
            <div class="wysiwyg-wrapper">
		           <?php
               $wysiwyg = array(
                 'media_buttons' => false,
                 'quicktags' => false,
                 'tinymce' => array(
                   'wp_autoresize_on' => false,
                   'resize' => false,
                   'statusbar' => false,
                   'mce_buttons' => 'bold, italic'
                 ),
                 'textarea_name' => 'ag_settings[additional]'
               );

               wp_editor( html_entity_decode(stripslashes($values['additional'])), 'additional', $wysiwyg );
              ?>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <h3><?php _e('Validation messages', 'age-gate') ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="wp_age_gate_invalid_input_msg"><?php _e("Invalid inputs", 'age-gate') ;?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[invalid_input_msg]',
              'type' => 'text',
              'id' => 'wp_age_gate_invalid_input_msg'
            ), $values['invalid_input_msg'], array('class' => 'regular-text ltr'));
            ?>
            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_under_age_msg"><?php _e("Under age", 'age-gate') ;?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[under_age_msg]',
              'type' => 'text',
              'id' => 'wp_age_gate_under_age_msg'
            ), $values['under_age_msg'], array('class' => 'regular-text ltr'));
            ?>
            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_generic_error_msg"><?php _e("Generic error", 'age-gate') ;?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[generic_error_msg]',
              'type' => 'text',
              'id' => 'wp_age_gate_generic_error_msg'
            ), $values['generic_error_msg'], array('class' => 'regular-text ltr'));
            ?>
            <?php /*<p class="note"><?php _e("Adding “%s” to this field will output the minimum age", 'age-gate'); ?></p>*/ ?>
          </td>
        </tr>
      </tbody>
    </table>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_required"><?php _e("Required field message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_required]',
              'type' => 'text',
              'id' => 'wp_age_gate_required'
            ), $validation['validate_required'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_numeric"><?php _e("Numeric field message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_numeric]',
              'type' => 'text',
              'id' => 'wp_age_gate_numeric'
            ), $validation['validate_numeric'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_min_len"><?php _e("Minimum length message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_min_len]',
              'type' => 'text',
              'id' => 'wp_age_gate_min_len'
            ), $validation['validate_min_len'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_len"><?php _e("Maximum length message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_max_len]',
              'type' => 'text',
              'id' => 'wp_age_gate_max_len'
            ), $validation['validate_max_len'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_numeric"><?php _e("Maximum numeric message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_max_numeric]',
              'type' => 'text',
              'id' => 'wp_age_gate_max_numeric'
            ), $validation['validate_max_numeric'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wp_age_gate_max_numeric"><?php _e("Minimum numeric message", 'age-gate'); ?></label>
          </th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_validation[validate_min_numeric]',
              'type' => 'text',
              'id' => 'wp_age_gate_min_numeric'
            ), $validation['validate_min_numeric'], array('class' => 'regular-text ltr'));
            ?>
            <p class="note"><?php _e("Adding “{field}” will output the field name. Adding “{param}” will output the required length.", 'age-gate'); ?></p>
          </td>
        </tr>

      </tbody>
    </table>
    <p><?php _e("More validators are available for custom fields.", 'age-gate'); ?> <a href="https://agegate.io/docs/guides/custom-form-fields/available-validators" target="_blank"><?php _e("See the documentation", 'age-gate'); ?> <i aria-hidden="true" class="dashicons dashicons-external"></i></a></p>
    <?php submit_button(); ?>
  </form>
</div>