<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp'], True);

$user = new User($experiment->owner);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}
else {

  $excluded_email_addresses = $experiment->getExclusionEmails();
  foreach ($experiment->getExclusions() as $exclusion) {
    $alt_experiment = new Experiment($exclusion);
    $excluded_email_addresses = array_merge($excluded_email_addresses, $alt_experiment->getExclusionEmails());
  }
  if (in_array(strtolower($_REQUEST['email']), $excluded_email_addresses)) {
    $page = 'not_eligible';
  }

  else {
    $date_time = explode('|', $_REQUEST['timeslot']);
    $date = $date_time[0];
    $time = $date_time[1];
    $formatted_date = date('l jS F', strtotime($date));

    // Get a list of the participants who have currently signed up for this slot
    $current_subjects = $experiment->getSlot($_REQUEST['timeslot']);

    // Double check that the timeslot is still free
    if (count($current_subjects) < $experiment->getPerSlot()) {
      // Set the time slot with the participant's details
      $experiment->setSlot($_REQUEST['timeslot'], $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone']);
      // Add the participant's email to the list of excluded email addresses
      $experiment->addExclusionEmails(array($_REQUEST['email']));
      $experiment->setExclusionEmails();
      // If the new data is successfully written out...
      if ($experiment->saveExperimentData() == True) {
        // If this is a multi-person experiment AND the slot is now full...
        if ($experiment->getPerSlot() > 1 AND $experiment->getPerSlot() == count($current_subjects)+1) {
          // For each participant who already signed up...
          foreach ($current_subjects as $subject) {
            // Send an email to say that the experiment will go ahead as planned
            $experiment->sendEmail($subject[1], $user->getName(), $user->getEmail(), 'email_full', array('NAME'=>$subject[0], 'DATE'=>$slot_date, 'TIME'=>$slot_time));
          }
          // Send an email to this participant to confirm the appointment
          $experiment->sendEmail($_REQUEST['email'], $user->getName(), $user->getEmail(), 'email_conf_full', array('NAME'=>$_REQUEST['name'], 'DATE'=>$slot_date, 'TIME'=>$slot_time));
        }
        else {
          // Send an email to this participant to confirm the booking
          $experiment->sendEmail($_REQUEST['email'], $user->getName(), $user->getEmail(), 'email_conf', array('NAME'=>$_REQUEST['name'], 'DATE'=>$slot_date, 'TIME'=>$slot_time));
        }
      }
      else {
        $page = 'error';
        $error = '100';
      }
    }
    else {
      // If not, send back to calendar page
      $page = 'calendar';
      $warning_message = True;
    }
  }
}

?>
