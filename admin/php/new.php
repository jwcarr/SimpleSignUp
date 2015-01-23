<?php

if (isset($_REQUEST['confirm'])) {

  $new_experiment_id = preg_replace('/[^A-Za-z0-9_]/', '', $_REQUEST['exp']);

  if ($new_experiment_id == '') {
    $notification = '<strong>Error:</strong> a code name is required to set up an experiment.';
    $notification_colour = 'red';
  }
  else {

    $experiments_file = new File($data_path .'experiments', True);

    preg_match('/'. $new_experiment_id .' = \{(.*?)\}/s', $experiments_file->data, $matches);

    if (count($matches) > 0) {
      $notification = '<strong>Error:</strong> this code name is already owned by "'. $matches[1] .'". Please choose something else.';
      $notification_colour = 'red';
    }

    else {

      $experiments_file->data = $experiments_file->data . $new_experiment_id . ' = {' . $identity[0] . "}\n";

      if ($experiments_file->overwrite()) {
        unset($experiments_file);
        unset($user);
        $user = new User($identity[0], True);
        $user->addExperiment($new_experiment_id);

        $f = fopen($data_path . 'user_data/'. $identity[0] . '/' . $new_experiment_id, 'w');
        fclose($f);

        if ($user->saveUserDetails()) {

          if (is_null($_REQUEST['shared_access']) == False) {
            $usernames = $user->getAllUsernames();
          }
          unset($user);

          $experiment = new Experiment($new_experiment_id, True, $identity[0]);
          $experiment->setName($_REQUEST['name']);
          $experiment->setStatus('closed');
          $experiment->setDescription($_REQUEST['description']);
          $experiment->setLocation($_REQUEST['location']);
          $experiment->setRequirements($_REQUEST['requirements']);
          $experiment->setExclusions($_REQUEST['exclusions']);
          $experiment->setManualExclusions($_REQUEST['manual_exclusions']);
          $experiment->setPerSlot($_REQUEST['per_slot']);
          $experiment->setSharedAccess($_REQUEST['shared_access'], $usernames);
          $experiment->setCalendar($_REQUEST['new_times']);
          $experiment->setAutomatedStatus($_REQUEST['automated_status']);
          $experiment->setEmail($_REQUEST['email_conf'], 'email_conf');
          $experiment->setEmail($_REQUEST['email_full'], 'email_full');
          $experiment->setEmail($_REQUEST['email_conf_full'], 'email_conf_full');
          $experiment->setEmail($_REQUEST['email_reminder'], 'email_reminder');

          if ($experiment->saveExperimentData()) {
            unset($experiment);
            $page = 'main';
            $notification = 'Your new experiment has been created.';
            $notification_colour = 'green';
            if (isset($user) == False) {
              $user = new User($identity[0], False);
            }
          }
          else {
            $notification = '<strong>Error:</strong> failure to create experiment.';
            $notification_colour = 'red';
          }

        }
        else {
          $notification = '<strong>Error:</strong> failure to add experiment to this user.';
          $notification_colour = 'red';
        }
      }

      else {
        $notification = '<strong>Error:</strong> failure to add experiment to SimpleSignUp.';
        $notification_colour = 'red';
      }

    }
  }
  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';
}

if ($page == 'new') {

  if (isset($experiments_file) == False) {
    $experiments_file = new File($data_path .'experiments', False);
    $experiments = explode("\n", $experiments_file->data);
  }
  else {
    $experiments = explode("\n", $experiment->experiments_file->data);
  }

  if (isset($user) == False) {
    $user = new User($identity[0], False);
  }

  $all_experiments = array();
  foreach ($experiments as $experiment) {
    if ($experiment != '') {
      preg_match('/(.*?) = \{(.*?)\}/s', $experiment, $matches);
      $all_experiments[$matches[2]][] = $matches[1];
    }
  }
  ksort($all_experiments);
  foreach ($all_experiments as $username=>$experiments) {
    foreach ($experiments as $experiment) {
      $exclusion_options .= '<option value="' . $experiment . '">' . $username . ': ' . $experiment . '</option>';
    }
  }

  if (isset($usernames) == False) {
    $usernames = $user->getAllUsernames();
    foreach ($usernames as $username) {
      if ($username != $identity[0]) {
        $shared_access_options .= '<option value="' . $username . '">' . $username . '</option>';
      }
    }
  }
}

?>
