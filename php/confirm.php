<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp'], True);

$user = new User($experiment->owner);

$page_header = "<h1>{$experiment->getName()}</h1>";

foreach ($experiment->getCalendar() as $date=>$slots) {
  foreach ($slots as $slot) {
    if ($slot[1] == $_REQUEST['timeslot']) {
      $slot_date = $date;
      $slot_time = $slot[0];
    }
  }
}

// Double check that the timeslot is still free
if (count($experiment->getSlot($_REQUEST['timeslot'])) < $experiment->getPerSlot()) {
  $experiment->setSlot($_REQUEST['timeslot'], $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone']);
  $experiment->saveExperimentData();
}
else {
  // If not, send back to calendar page
  $page = 'calendar';
  $warning_message = True;
}

//$a = mail($_REQUEST['email'], $experiment->getName(), "Dear Jon", "From: {$user->getName()} <{$user->getEmail()}>");

?>
