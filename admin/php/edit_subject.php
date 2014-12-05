<?php

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);

  if ($_REQUEST['send_emails'] == 'checked') {
    $send_emails = True;
  }
  else {
    $send_emails = False;
  }

  if ($experiment->editSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject'], $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone'], $_REQUEST['new_timeslot'], $user->getName(), $user->getEmail(), $send_emails)) {
    if ($experiment->saveExperimentData()) {
      $notification = 'Your changes to "'. $_REQUEST['name'] .'" have been saved.';
      $notification_colour = 'green';
    }
    else {
      $notification = 'There was an error saving your changes.';
      $notification_colour = 'red';
    }
  }
  else {
    $notification = 'The requested date is no longer available.';
    $notification_colour = 'red';
  }
  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';
  include('php/view.php');
  $page = 'view';

}

else {
  $experiment = new Experiment($_REQUEST['exp'], False);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);
  $current_timeslot = date('jS M', strtotime($_REQUEST['date'])) . ', ' . $_REQUEST['time'];
  $alt_timeslots = $experiment->printAltTimeslots($_REQUEST['date'], $_REQUEST['time']);
}

?>
