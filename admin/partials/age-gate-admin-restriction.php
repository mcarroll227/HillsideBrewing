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
    <input type="hidden" name="action" value="age_gate_restriction">
    <?php wp_nonce_field( 'age_gate_update_restrictions', 'nonce'); ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row"><label for="wp_age_gate_min_age"><?php _e("Default age", 'age-gate'); ?></label></th>
          <td>
            <?php echo form_input(array(
              'name' => 'ag_settings[min_age]',
              'type' => 'number',
              'id' => 'wp_age_gate_min_age'
            ), $values['min_age'], array('class' => 'small-text ltr', 'required' => 'required'));
            ?> <?php _e("years or older to view content", 'age-gate'); ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_restriction_type"><?php _e("Restrict", 'age-gate'); ?></label></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><?php _e("Select", 'age-gate'); ?></legend>
              <label>
                <?php echo form_radio(
                  array(
                    'name' => 'ag_settings[restriction_type]',
                    'id' => 'wp_age_gate_restriction_type'
                  ),
                  'all',
                  $values['restriction_type'] === 'all'
                ); ?> <?php _e("All content", 'age-gate'); ?></label><br>
              <label>
                <?php echo form_radio(
                  array(
                    'name' => 'ag_settings[restriction_type]',
                    'id' => 'wp_age_gate_restriction_type'
                  ),
                  'selected',
                  $values['restriction_type'] === 'selected'
                ); ?> <?php _e("Selected Content", 'age-gate'); ?>
              </label><br>
            </fieldset>
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="wp_age_gate_multi_age"><?php _e("Varied ages", 'age-gate'); ?></label></th>
          <td>
            <?php if (!$this->settings['advanced']['anonymous_age_gate']): ?>
            <label>
              <?php echo form_checkbox(
                array(
                  'name' => "ag_settings[multi_age]",
                  'id' => "wp_age_gate_multi_age"
                ),
                1, // value
                $values['multi_age'] // checked
              ); ?> <?php _e("Ability to add a custom age on a per page level ", 'age-gate'); ?>
            </label>
            <?php else: ?>
              <p><?php _e('This setting is unavailable with "Anonymous Age Gate" selected in the advanced tab', 'age-gate'); ?></p>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_restrict_register"><?php _e("Restrict registration", 'age-gate'); ?></label></th>
          <td>
            <?php if (!get_option('users_can_register')): ?>
              <?php _e("Your site does not allow registration, so this option is not applicable", 'age-gate'); ?>
              <?php echo form_hidden('ag_settings[restrict_register]', $values['restrict_register']); ?>
            <?php else: ?>
              <label>
                <?php echo form_checkbox(
                  array(
                    'name' => "ag_settings[restrict_register]",
                    'id' => "wp_age_gate_restrict_register"
                  ),
                  1, // value
                  $values['restrict_register'] // checked
                ); ?> <?php _e("Age check users during registering", 'age-gate'); ?>
              </label>
              <p class="note"><small><?php _e("Deprecated. From 2.0.3 this feature has been deprecated and will be removed in a future release.", 'age-gate'); ?><a href="https://agegate.io/deprecation-notice-restrict-registration" target="_blank"><span class="dashicons dashicons-external"></span></a></small></p>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_input_type"><?php _e("Validate age using", 'age-gate'); ?></label></th>
          <td>
            <?php echo form_dropdown(
              array(
                'name' => 'ag_settings[input_type]',
                'id' => 'wp_age_gate_input_type'
              ),
              array(
                'inputs' => __("Input fields", 'age-gate'),
                'selects' => __("Dropdown boxes", 'age-gate'),
                'buttons' => __("Yes/No", 'age-gate'),
              ),
              $values['input_type']
            ); ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_remember"><?php _e("Remember", 'age-gate'); ?></label></th>
          <td>
            <label>
              <?php echo form_checkbox(
                array(
                  'name' => "ag_settings[remember]",
                  'id' => "wp_age_gate_remember"
                ),
                1, // value
                $values['remember'] // checked
              ); ?> <?php _e("Enable \"remember me\" checkbox", 'age-gate'); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_remember_days"><?php _e("Remember length", 'age-gate'); ?></label></th>
          <td>
            <?php echo form_input(
              array(
                'name' => 'ag_settings[remember_days]',
                'type' => 'number',
                'id' => 'wp_age_gate_remember_days'
              ),
              $values['remember_days'],
              array('class' => 'small-text ltr')
            ); ?>
            <?php $options = array(
              'days'         => __('Days', 'age-gate'),
              'hours'        => __('Hours', 'age-gate'),
              'minutes'      => __('Minutes', 'age-gate')
            );

            echo form_dropdown('ag_settings[remember_timescale]', $options, $values['remember_timescale']);

            ?>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_remember_auto_check"><?php _e("Auto check remember me", 'age-gate'); ?></label></th>
          <td>
            <label>
              <?php echo form_checkbox(
                array(
                  'name' => "ag_settings[remember_auto_check]",
                  'id' => "wp_age_gate_remember_auto_check"
                ),
                1, // value
                $values['remember_auto_check'] // checked
              ); ?> <?php _e("\"Remember me\" will be checked by default", 'age-gate'); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_date_format"><?php _e("Date format", 'age-gate'); ?></label></th>
          <td>
            <?php echo form_dropdown(
              array(
                'name' => 'ag_settings[date_format]',
                'id' => 'wp_age_gate_date_format'
              ),
              array(
                'ddmmyyyy' => __("DD MM YYYY", 'age-gate'),
                'mmddyyyy' => __("MM DD YYYY", 'age-gate')
              ),
              $values['date_format']
            ); ?>


          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_ignore_logged"><?php _e("Ignore logged in", 'age-gate'); ?></label></th>
          <td>
            <label>
              <?php echo form_checkbox(
                array(
                  'name' => "ag_settings[ignore_logged]",
                  'id' => "wp_age_gate_ignore_logged"
                ),
                1, // value
                $values['ignore_logged'] // checked
              ); ?> <?php _e("Logged in users will not need to provide their age", 'age-gate'); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_rechallenge"><?php _e("Rechallenge", 'age-gate'); ?></label></th>
          <td>
            <label>
              <?php echo form_checkbox(
                array(
                  'name' => "ag_settings[rechallenge]",
                  'id' => "wp_age_gate_rechallenge"
                ),
                1, // value
                $values['rechallenge'] // checked
              ); ?> <?php _e("If someone fails the age test, they can try again.", 'age-gate'); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="wp_age_gate_fail_link"><?php _e("Redirect failures", 'age-gate'); ?></label></th>
          <td>
            <div class="link-container">
              <?php if ($values['fail_link_title'] && $values['fail_link']): ?>
              <p><strong><?php echo ($values['fail_link_title'] === 'Custom') ? __('Custom', 'age-gate') : $values['fail_link_title'] ?></strong> (<?php echo $values['fail_link']; ?>)</p>
              <?php endif; ?>
            </div>
            <a class="button" data-action="link-modal" href="#" title=""><?php _e("Choose link", 'age-gate'); ?></a>
            <?php if ($values['fail_link_title'] && $values['fail_link']): ?>
            <button type="button" class="button remove" data-action="remove-link"><?php _e('Remove link', 'age-gate'); ?></button>
            <?php endif; ?>
            <p><?php _e("If someone fails the age test, redirect them to a page or external site rather than showing errors.", 'age-gate'); ?></p>
            <?php
            echo form_input(array(
              'name' => 'ag_settings[fail_link_title]',
              'type' => 'hidden',
              'id' => 'wp_age_gate_fail_link_title'
            ), $values['fail_link_title']);

            echo form_input(array(
              'name' => 'ag_settings[fail_link]',
              'type' => 'hidden',
              'id' => 'wp_age_gate_fail_link'
            ), $values['fail_link']);
            ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php submit_button(); ?>
  </form>
</div>