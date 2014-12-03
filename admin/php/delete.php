<?php

if (isset($_REQUEST['confirm'])) {

  unset($user);
  $user = new User($identity[0], True);
  $user->removeExperiment($_REQUEST['exp']);

  $experiments_file = new File($data_path . 'experiments', True);
  $experiments_file->data =str_replace($_REQUEST['exp'] . ' = {' . $identity[0] . "}\n", '', $experiments_file->data);

  if ($user->saveUserDetails()) {
    if ($experiments_file->overwrite()) {
      if (unlink($data_path . 'user_data/'. $identity[0] . '/' . $_REQUEST['exp'])) {
        $page = 'main';
        $notification = 'Experiment deleted';
      }
      else {
        $notification = '<strong>Error:</strong> failure to delete experiment file';
      }
    }
    else {
      $notification = '<strong>Error:</strong> failure to remove experiment from SimpleSignUp.';
    }
  }
  else {
    $notification = '<strong>Error:</strong> failure to remove experiment from this user.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

else {

  include_once("../php/class.experiment.php");
  $experiment = new Experiment($_REQUEST['exp'], False);

}

?>
