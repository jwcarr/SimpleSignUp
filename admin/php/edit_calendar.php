<?php

include_once("../php/class.experiment.php");

$experiment = new Experiment($_REQUEST['exp'], True);

if ($experiment->removeTime($_REQUEST['date'], $_REQUEST['time'])) {
  if ($experiment->saveExperimentData()) {
    if (is_null($_REQUEST['time'])) {
      $notification = 'Successfully deleted date.';
    }
    else {
      $notification = 'Successfully deleted timeslot.';
    }
    $notification_colour = 'green';
  }
  else {
    $notification = '<strong>Error:</strong> Could not save changes.';
    $notification_colour = 'red';
  }
}
else {
  $notification = '<strong>Error:</strong> Could not delete; date or time no longer empty.';
  $notification_colour = 'red';
}

include('view.php');
$page = 'view';
$notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

?>
