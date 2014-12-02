<?php

include_once("../php/class.user.php");

if (isset($_REQUEST['confirm'])) {

  unset($user);
  $user = new User($identity[0], True);

  if ($_REQUEST['password1'] == $_REQUEST['password2']) {

    include_once("php/class.htaccess.php");
    $ht_password_file = new HTaccess();

    if ($ht_password_file->changePassword($username, $_REQUEST['password'], $_REQUEST['password1'])) {
      $page = 'main';
      $notification = 'Password successfully changed.';
    }
    else {
      $notification = 'You entered the incorrect password. Please try agian.';
    }

  }
  else {
    $notification = 'The new passwords you entered do not match. Please try again.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

?>
