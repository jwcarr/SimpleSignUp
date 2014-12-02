<?php

include_once("../php/class.user.php");
$user = new User($username, False);

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);

  $experiment->setName($_REQUEST['name']);
  $experiment->setDescription($_REQUEST['description']);
  $experiment->setLocation($_REQUEST['location']);
  $experiment->setPerSlot($_REQUEST['per_slot']);

  if ($experiment->saveExperimentData()) {
    include('view.php');
    $page = 'view';
    $notification = 'Your edits have successfully been changed.';
  }
  else {
    $notification = 'Error saving your edits.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

else {

  $experiment = new Experiment($_REQUEST['exp'], False);
  $requirements = implode("\n", $experiment->getRequirements());
  $exclusions = implode("\n", $experiment->getExclusions());

}

?>
