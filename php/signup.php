<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False, $_REQUEST['own']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}

else {
  if (isset($_REQUEST['name'])) {
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $phone = $_REQUEST['phone'];
  }
  elseif (isset($_COOKIE['SimpleSignUp'])) {
    $details = explode('|', $_COOKIE['SimpleSignUp']);
    $name = $details[0];
    $email = $details[1];
    $phone = $details[2];
  }
  $requirement_checkboxes = $experiment->printRequirementCheckboxes();
  if ($requirement_checkboxes != False) {
    $requirements = '<h2>Requirements</h2><p>Please indicate that you meet the requirements for this experiment.</p><div id="reqs">'. $requirement_checkboxes .'</div>';
  }
}

?>
