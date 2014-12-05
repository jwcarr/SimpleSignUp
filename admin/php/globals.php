<?php

// Valid page IDs
$pages = array('main', 'view', 'edit', 'delete', 'change_status', 'new', 'change_details', 'change_password', 'save_password', 'save_details', 'save_edits', 'remind', 'remind_bulk', 'delete_subject', 'add_subject', 'edit_subject');

$auth_pages = array('authenticate', 'login', 'logout');

// Path to data folder from Admin directory
$data_path = '../../../server_data/ssu/';

// Set to True if using PHP >= 5.5. Uses password_hash() instead of crypt()
$new_hash_method = True;

// Set timezone
date_default_timezone_set('UTC');

function generateMenu($name, $current_page) {
  $menu_items = array('main'=>'My experiments', 'new'=>'New experiment', 'change_details'=>'Change my details', 'change_password'=>'Change my password');
  $menu = '<p><strong>'. $name .'</strong></p>';
  foreach ($menu_items as $page=>$description) {
    if ($page == $current_page) {
      $menu .= '<p><img src="images/'. $page .'_black.png" width="16" height="16" style="vertical-align: bottom" />&nbsp;&nbsp;'. $description .'</p>';
    }
    else {
      $menu .= '<p><a href="index.php?page='. $page .'" onMouseOver="document.' . $page . '.src=\'images/' . $page . '_black.png\'" onMouseOut="document.' . $page . '.src=\'images/' . $page . '.png\'"><img name="' . $page . '" src="images/' . $page . '.png" width="16" height="16" style="vertical-align: bottom" />&nbsp;&nbsp;'. $description .'</a></p>';
    }
  }
  $menu .= '<p><a href="index.php?page=logout" onMouseOver="document.logout.src=\'images/logout_black.png\'" onMouseOut="document.logout.src=\'images/logout.png\'"><img name = "logout" src="images/logout.png" width="16" height="16" style="vertical-align: bottom" />&nbsp;&nbsp;Logout</a></p>';
  return $menu;
}

?>
