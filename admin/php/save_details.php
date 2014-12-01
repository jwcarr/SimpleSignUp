<?php

include('../php/file.php');

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
  $page = 'change_details';
  $notification = 'Error saving your details.';
}

$notification = '<div id="notification"><p>' . $notification . '</p></div>';

?>
