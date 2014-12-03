<?php

// Valid page IDs
$pages = array('main', 'view', 'edit', 'delete', 'change_status', 'new', 'change_details', 'change_password', 'save_password', 'save_details', 'save_edits', 'remind', 'remind_bulk', 'delete_subject', 'edit_subject');

// Path to data folder from Admin directory
$data_path = '../../../server_data/ssu/';

// Set to True if using PHP >= 5.5. Uses password_hash() instead of crypt()
$new_hash_method = True;

// Set timezone
date_default_timezone_set('UTC');

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
