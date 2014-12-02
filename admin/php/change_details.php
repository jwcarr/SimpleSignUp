<?php

include_once("../php/class.user.php");

if (isset($_REQUEST['confirm'])) {

  $user = new User($username, True);

  $user->setName($_REQUEST['name']);
  $user->setEmail($_REQUEST['email']);
  $user->setPhone($_REQUEST['phone']);

  if ($user->saveUserDetails()) {
    $page = 'main';
    $experiments = $user->getExperiments();
    $notification = 'Your details have successfully been changed.';
  }
  else {
    $notification = 'Error saving your details.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

?>
