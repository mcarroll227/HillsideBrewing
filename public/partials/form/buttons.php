<?php if ($this->messages->buttons->message): ?>
  <?php $message = '<p class="age-gate-challenge">' . sprintf(__($this->messages->buttons->message), $this->age) . '</p>'; $message = apply_filters('age_gate_button_message', $message, sprintf(__($this->messages->buttons->message), $this->age)); echo $message; ?>
<?php endif; ?>

<button type="submit" value="1" name="age_gate[confirm]" class="age-gate-submit-yes"><?php echo ($this->messages->buttons->yes) ? __($this->messages->buttons->yes) : __('Yes', 'age-gate'); ?></button>
<button type="submit" <?php echo (!$this->settings['advanced']['use_js']) ? 'value="0" ': '' ?>name="age_gate[confirm]" class="age-gate-submit-no"><?php echo ($this->messages->buttons->no) ? __($this->messages->buttons->no) : __('No', 'age-gate'); ?></button>