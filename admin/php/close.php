<?php

include("../php/file.php");

$user = new User($username);

if (isset($_REQUEST['status'])) {
  $experiment = new Experiment($_REQUEST['exp'], True);
  $experiment->setStatus($_REQUEST['status']);
  if ($experiment->saveExperimentData() == True) {
    $notification = '"' . $experiment->getName() . '" has been ';
    if ($_REQUEST['status'] == 'closed') { $notification .= 'closed'; }
    else { $notification .= 'opened'; }
    $page = 'main';
    $experiments = $user->getExperiments();
  }
  else {
    $notification = 'There was an error changing the status of the experiment';
  }
  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}
else {
  $experiment = new Experiment($_REQUEST['exp']);
  if ($experiment->getStatus() == 'closed') {
    $change_status = 'open';
    $button_text = 'Open experiment';
    $warning_message = 'Are you sure you wish to open this experiment? Once your experiment is opened, participants will be able to sign up. You will be able to close the experiment at a later time.';
  }
  else {
    $change_status = 'closed';
    $button_text = 'Close experiment';
    $warning_message = 'Are you sure you wish to close this experiment? Once an experiment is closed, participants will no longer be able to sign up. You will still be able to reopen the experiment at a later time.';
  }
}

?>
