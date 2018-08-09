<ol class="age-gate-form-elements">
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'input-month.php' : 'input-day.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <?php include AGE_GATE_PATH . 'public/partials/form/sections/' . ($this->restrictions->date_format === 'mmddyyyy' ? 'input-day.php' : 'input-month.php'); ?>
  </li>
  <li class="age-gate-form-section">
    <label class="age-gate-label" for="age-gate-y"><?php _e('Year', 'age-gate'); ?></label>
    <input type="text" name="age_gate[y]" class="age-gate-input" id="age-gate-y" value="<?php echo (isset($age['y']) ? $age['y'] : '') ?>" placeholder="<?php _e('YYYY', 'age-gate'); ?>" required minlength="4" maxlength="4" pattern="\d+" autocomplete="off">
  </li>
</ol>
<?php if($this->restrictions->date_format === 'mmddyyyy'): ?>
  <?php echo age_gate_error('age_gate_m'); ?>
  <?php echo age_gate_error('age_gate_d'); ?>
<?php else: ?>
  <?php echo age_gate_error('age_gate_d'); ?>
  <?php echo age_gate_error('age_gate_m'); ?>
<?php endif; ?>
<?php echo age_gate_error('age_gate_y'); ?>