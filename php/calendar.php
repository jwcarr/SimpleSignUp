<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False, $_REQUEST['own']);

$page_header = $experiment->getName();

setcookie('SimpleSignUp', $_REQUEST['name'] . '|' . $_REQUEST['email'] . '|' . $_REQUEST['phone'], time()+31536000);

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}
else {

  if ($_COOKIE[$experiment->id] == 'ineligible') {
    $page = 'not_eligible';
    include_once("class.user.php");
    $user = new User($experiment->owner);
  }

  else {

    $excluded_email_addresses = $experiment->getExclusionEmails();

    foreach ($experiment->getExclusions() as $exclusion) {
      $alt_experiment = new Experiment($exclusion, False);
      $excluded_email_addresses = array_merge($excluded_email_addresses, $alt_experiment->getExclusionEmails());
    }

    if (in_array(strtolower($_REQUEST['email']), $excluded_email_addresses)) {
      $page = 'not_eligible';
      setcookie($experiment->id, 'ineligible', time()+604800);
      include_once("class.user.php");
      $user = new User($experiment->owner);
    }

  }

}

?>
