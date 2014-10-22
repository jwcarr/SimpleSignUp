<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

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

// Check that the slot is still free

// If not, send back to calendar page

// If so,

?>
