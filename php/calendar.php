<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

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
    $user = new User($experiment->owner);
  }

  else {
    if ($experiment->getPerSlot() > 1) {
      $additional_message = "<strong>This experiment requires {$experiment->getPerSlot()} participants per timeslot.</strong> You will not be guaranteed a place on the experiment until {$experiment->getPerSlot()} people have signed up for your slot. If possible, please choose a slot that someone else has already signed up for (highlighted in green). You’ll receive an email to confirm when your slot has been filled.";
    }
  }

}

?>
