<?php

$current = (isset($_GET['page']) ? $_GET['page'] : null);
$custom = array();
$custom = apply_filters('age_gate_admin_tabs', $custom);
// merge default first to keep order, then theirs to get filters,
// then default again to ensure nothing is overwritten
$tabs = array_merge($this->config->tabs, $custom, $this->config->tabs);


//
// $custom = (!$custom) ? array() : $custom;
//
// print_r($custom);
?>
<h3 class="nav-tab-wrapper">
  <?php
  $markup = '';
  $link = '<a href="%s" class="nav-tab %s">%s</a>';
  foreach ($tabs as $slug => $tab){
    if (current_user_can($tab['cap'])) {
      $url = esc_url(add_query_arg( array('page' => $slug), admin_url('admin.php') ));
      $class = ($current === $slug) ? 'nav-tab-active' : '';
      $markup .= sprintf($link, $url, $class, $tab['title']);
    }

  }
  echo $markup;
  ?>
</h3>