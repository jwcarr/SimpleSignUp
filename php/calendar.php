<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}
else {

  if ($_COOKIE[$experiment->id] == 'ineligible') {
    $page = 'not_eligible';
    $user = new User($experiment->owner);
  }

  else {

    $excluded_email_addresses = $experiment->getExclusionEmails();

    foreach ($experiment->getExclusions() as $exclusion) {
      $alt_experiment = new Experiment($exclusion);
      $excluded_email_addresses = array_merge($excluded_email_addresses, $alt_experiment->getExclusionEmails());
    }

    if (in_array(strtolower($_REQUEST['email']), $excluded_email_addresses)) {
      $page = 'not_eligible';
      setcookie($experiment->id, 'ineligible', time()+604800);
      $user = new User($experiment->owner);
    }

  }

}

?>
