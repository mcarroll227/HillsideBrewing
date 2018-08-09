<label class="age-gate-label" for="age-gate-d"><?php _e('Day', 'age-gate'); ?></label>
<select name="age_gate[d]" id="age-gate-d" class="age-gate-select" required>
  <option value=""><?php _e('DD', 'age-gate'); ?></option>
  <?php for ($i = 1; $i <= 31; $i++): $val = str_pad($i, 2, 0, STR_PAD_LEFT); ?>
    <option value="<?php echo $val ?>"><?php echo $val; ?></option>
  <?php endfor; ?>
</select>