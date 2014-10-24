<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

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
  $edit_details_link = "index.php?page=signup&exp={$experiment->id}&name={$_REQUEST['name']}&email={$_REQUEST['email']}&phone={$_REQUEST['phone']}";
}
else {
  // If not, send back to calendar page
  $page = 'calendar';
  $warning_message = True;
}

?>
