<?php

if (isset($_REQUEST['status'])) {

  include_once("../php/class.experiment.php");
  $experiment = new Experiment($_REQUEST['exp'], True);

  $experiment->setStatus($_REQUEST['status']);
  if ($experiment->saveExperimentData() == True) {
    $notification = '"' . $experiment->getName() . '" has been ';
    if ($_REQUEST['status'] == 'closed') { $notification .= 'closed'; }
    else { $notification .= 'opened'; }
    $notification_colour = 'green';
    $page = 'main';
    $experiments = $user->getExperiments();
  }
  else {
    $notification = 'There was an error changing the status of the experiment';
    $notification_colour = 'red';
  }
  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

}
else {

  include_once("../php/class.experiment.php");
  $experiment = new Experiment($_REQUEST['exp'], False);

  if ($experiment->getStatus() == 'closed') {
    $change_status = 'open';
    $button = '<button type="submit" class="orange">Open experiment</button>';
    $warning_message = 'Are you sure you wish to open this experiment? Once your experiment is opened, participants will be able to sign up. You will be able to close the experiment at a later time.';
  }
  else {
    $change_status = 'closed';
    $button = '<button type="submit" class="red">Close experiment</button>';
    $warning_message = 'Are you sure you wish to close this experiment? Once an experiment is closed, participants will no longer be able to sign up. You will still be able to reopen the experiment at a later time.';
  }

}

?>
