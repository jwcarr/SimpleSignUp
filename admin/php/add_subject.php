<?php

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);

  $excluded_email_addresses = $experiment->getExclusionEmails();
  foreach ($experiment->getExclusions() as $exclusion) {
    $alt_experiment = new Experiment($exclusion);
    $excluded_email_addresses = array_merge($excluded_email_addresses, $alt_experiment->getExclusionEmails());
  }
  if (in_array(strtolower($_REQUEST['email']), $excluded_email_addresses)) {
    $notification = 'Sorry, "' . $_REQUEST['name'] . '" is excluded from this experiment.';
    $notification_colour = 'red';
  }

  else {

    if ($_REQUEST['send_emails'] == 'checked') {
      $send_emails = True;
    }
    else {
      $send_emails = False;
    }

    if ($_REQUEST['send_conf_email'] == 'checked') {
      $send_conf_email = True;
    }
    else {
      $send_conf_email = False;
    }

    $current_subjects = $experiment->getSlot($_REQUEST['date'], $_REQUEST['time']);

    if (count($current_subjects) < $experiment->getPerSlot()) {
      $experiment->addToSlot($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone']);
      $experiment->addExclusionEmails(array($_REQUEST['email']));
      $experiment->setExclusionEmails();
      if ($experiment->saveExperimentData()) {
        $formatted_date = date('l jS F', strtotime($_REQUEST['date']));
        $time = $_REQUEST['time'];
        // If this is a multi-person experiment AND the slot is now full AND the send emails box was checked...
        if ($experiment->getPerSlot() > 1 AND $experiment->getPerSlot() == count($current_subjects)+1) {
          if ($send_emails == True) {
            // For each participant who already signed up...
            foreach ($current_subjects as $subject) {
              // Send an email to say that the experiment will go ahead as planned
              $experiment->sendEmail($subject[1], $user->getName(), $user->getEmail(), 'email_full', array('NAME'=>$subject[0], 'DATE'=>$formatted_date, 'TIME'=>$time));
            }
          }
          // Send an email to this participant to confirm the appointment
          if ($send_conf_email == True) {
            $experiment->sendEmail($_REQUEST['email'], $user->getName(), $user->getEmail(), 'email_conf_full', array('NAME'=>$_REQUEST['name'], 'DATE'=>$formatted_date, 'TIME'=>$time));
          }
        }
        elseif ($experiment->getPerSlot() == 1) {
          if ($send_conf_email == True) {
            // Send an email to this participant to confirm the booking
            $experiment->sendEmail($_REQUEST['email'], $user->getName(), $user->getEmail(), 'email_conf', array('NAME'=>$_REQUEST['name'], 'DATE'=>$formatted_date, 'TIME'=>$time));
          }
        }
        else {
          if ($send_conf_email == True) {
            $experiment->sendEmail($_REQUEST['email'], $user->getName(), $user->getEmail(), 'email_conf', array('NAME'=>$_REQUEST['name'], 'DATE'=>$formatted_date, 'TIME'=>$time));
          }
        }
        $notification = '"' . $_REQUEST['name'] . '" has been added to the experiment.';
        $notification_colour = 'green';
      }
      else {
        $notification = 'There was an error saving your changes.';
        $notification_colour = 'red';
      }
    }
    else {
      $notification = 'The requested timeslot is no longer available.';
      $notification_colour = 'red';
    }

  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';
  include('php/view.php');
  $page = 'view';

}

else {
  $experiment = new Experiment($_REQUEST['exp'], False);
}

?>
