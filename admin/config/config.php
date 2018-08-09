<?php
$config = (object) array();

$config->tabs = array(
  'age-gate' =>
    array(
      'cap' => AGE_GATE_CAP_RESTRICTIONS,
      'title' => _x('Restriction settings', 'Admin tab title', 'age-gate')
  ),
  'age-gate-messaging' =>
    array(
      'cap' => AGE_GATE_CAP_MESSAGING,
      'title' => _x('Messaging', 'Admin tab title', 'age-gate')
  ),
  'age-gate-appearance' =>
    array(
      'cap' => AGE_GATE_CAP_APPEARANCE,
      'title' => _x('Appearance', 'Admin tab title', 'age-gate')
  ),
  'age-gate-advanced' =>
    array(
      'cap' => AGE_GATE_CAP_ADVANCED,
      'title' => _x('Advanced', 'Admin tab title', 'age-gate')
  ),
  'age-gate-access' =>
    array(
      'cap' => AGE_GATE_CAP_ACCESS,
      'title' => _x('Access Settings', 'Admin tab title', 'age-gate')
  ),
);

return $config;