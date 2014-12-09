<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}

else {
  $requirement_checkboxes = $experiment->printRequirementCheckboxes();
  if ($requirement_checkboxes != False) {
    $requirements = '<h2>Requirements</h2><p>Please indicate that you meet the requirements for this experiment.</p><div id="reqs">'. $requirement_checkboxes .'</div>';
  }
}

?>
