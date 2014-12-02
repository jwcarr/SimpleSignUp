<?php

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);
  $experiment->deleteSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);

  if ($experiment->saveExperimentData()) {
    $notification = '"'. $subject[0] .'" has been removed from this experiment.';
  }
  else {
    $notification = 'There was an error removing "'. $subject[0] .'" from this experiment.';
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

  include('php/view.php');
  $page = 'view';

}

else {
  $experiment = new Experiment($_REQUEST['exp'], False);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);
}

?>
