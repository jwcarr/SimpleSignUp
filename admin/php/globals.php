<?php

// Path to data folder from Admin directory
$data_path = '../../../server_data/ssu/';

// Set timezone for timestamps (default = UTC)
date_default_timezone_set('UTC');

function cleanInputVariables() {
  foreach ($_REQUEST as $key=>$value) {
    $_REQUEST[$key] = str_replace(array('"', ',' , ':', ';', '{', '}', '[', ']', '=' ), '', str_replace("'", '’', trim($value)));
  }
}

function generateMenu($name, $current_page) {
  $menu_items = array('main'=>'My experiments', 'new'=>'New experiment', 'change_details'=>'Change my details', 'change_password'=>'Change my password');
  $menu = '<p><strong>'. $name .'</strong></p><ul>';
  foreach ($menu_items as $page=>$description) {
    if ($page == $current_page) {
      $menu .= '<li>'. $description .'</li>';
    }
    else {
      $menu .= '<li><a href="index.php?page='. $page .'">'. $description .'</a></li>';
    }
  }
  $menu .= '<li><a href="index.php?page=logout">Logout</a></li></ul>';
  return $menu;
}

?>
