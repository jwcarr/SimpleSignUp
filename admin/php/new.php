<?php

if (isset($_REQUEST['confirm'])) {

  unset($user);
  $user = new User($identity[0], True);
  $user->addExperiment($_REQUEST['exp']);

  $experiments_file = new File($data_path .'experiments', True);
  $experiments_file->data = $experiments_file->data . $_REQUEST['exp'] . ' = {' . $identity[0] . "}\n";

  $f = fopen($data_path . 'user_data/'. $identity[0] . '/' . $_REQUEST['exp'], 'w');
  fclose($f);

  $experiment = new Experiment($_REQUEST['exp'], True, $identity[0]);
  $experiment->setName($_REQUEST['name']);
  $experiment->setStatus('closed');
  $experiment->setDescription($_REQUEST['description']);
  $experiment->setLocation($_REQUEST['location']);
  //requirements
  //exclusions
  $experiment->setPerSlot($_REQUEST['per_slot']);
  //calendar
  //email_conf
  //email_full
  //email_conf_full

  if ($experiment->saveExperimentData()) {
    if ($experiments_file->overwrite()) {
      if ($user->saveUserDetails()) {
        $page = 'main';
        $notification = 'Your new experiment has been created.';
      }
      else {
        $notification = '<strong>Error:</strong> failure to add experiment to this user.';
      }
    }
    else {
      $notification = '<strong>Error:</strong> failure to add experiment to SimpleSignUp.';
    }
  }
  else {
    $notification = '<strong>Error:</strong> failure to create experiment.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

?>
