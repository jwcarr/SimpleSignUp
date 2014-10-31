<?php

include('../php/file.php');
include('php/htaccess.php');

$user = new User($username);
$experiments = $user->getExperiments();

if ($_REQUEST['password1'] == $_REQUEST['password2']) {
  $ht_password_file = new HTaccess();
  if ($ht_password_file->changePassword($username, $_REQUEST['password'], $_REQUEST['password1'])) {
    $page = 'main';
    $notification = 'Password successfully changed.';
  }
  else {
    $page = 'change_password';
    $notification = 'You entered the incorrect password. Please try agian.';
  }
}
else {
  $page = 'change_password';
  $notification = 'The new passwords you entered do not match. Please try again.';
}

$notification = '<div id="notification"><p>' . $notification . '</p></div>';

?>
