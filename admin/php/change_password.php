<?php

include_once("../php/class.user.php");

if (isset($_REQUEST['confirm'])) {

  unset($user);
  $user = new User($identity[0], True);

  if ($_REQUEST['password1'] == $_REQUEST['password2']) {

    if ($user->authorize($_REQUEST['password'], False)) {
      $password_hash = $user->setPassword($_REQUEST['password1']);
      if ($user->saveUserDetails()) {
        $page = 'main';
        $notification = 'Password successfully changed.';
        $notification_colour = 'green';
        setcookie('SimpleSignUpAuth', $identity[0] . ':' . $password_hash, time()+604800);
      }
      else {
        $notification = 'Error: could not save your new password';
        $notification_colour = 'red';
      }

    }
    else {
      $notification = 'You entered the incorrect password. Please try agian.';
      $notification_colour = 'red';
    }

  }
  else {
    $notification = 'The new passwords you entered do not match. Please try again.';
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

}

?>
