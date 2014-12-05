<?php

include_once("../php/class.user.php");

if (isset($_REQUEST['confirm'])) {

  unset($user);
  $user = new User($identity[0], True);

  $user->setName($_REQUEST['name']);
  $user->setEmail($_REQUEST['email']);
  $user->setPhone($_REQUEST['phone']);

  if ($user->saveUserDetails()) {
    $page = 'main';
    $experiments = $user->getExperiments();
    $notification = 'Your details have successfully been changed.';
    $notification_colour = 'green';
  }
  else {
    $notification = 'Error saving your details.';
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

}

?>
