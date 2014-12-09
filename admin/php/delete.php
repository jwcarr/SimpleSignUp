<?php

include_once("../php/class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False);

if (isset($_REQUEST['confirm'])) {

  $shared_access_users = $experiment->getSharedAccess();
  foreach ($shared_access_users as $shared_user) {
    $shared_user_object = new User($shared_user, True);
    $shared_user_object->removeSharedExperiment($_REQUEST['exp']);
    $shared_user_object->saveUserDetails();
    unset($shared_user_object);
  }

  unset($user);
  $user = new User($identity[0], True);
  $user->removeExperiment($_REQUEST['exp']);

  $experiments_file = new File($data_path . 'experiments', True);
  $experiments_file->data =str_replace($_REQUEST['exp'] . ' = {' . $identity[0] . "}\n", '', $experiments_file->data);

  if ($user->saveUserDetails()) {
    if ($experiments_file->overwrite()) {
      if (unlink($data_path . 'user_data/'. $identity[0] . '/' . $_REQUEST['exp'])) {
        $page = 'main';
        $notification = 'Experiment successfully deleted';
        $notification_colour = 'green';
      }
      else {
        $notification = '<strong>Error:</strong> failure to delete experiment file';
        $notification_colour = 'red';
      }
    }
    else {
      $notification = '<strong>Error:</strong> failure to remove experiment from SimpleSignUp.';
      $notification_colour = 'red';
    }
  }
  else {
    $notification = '<strong>Error:</strong> failure to remove experiment from this user.';
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

}

?>
