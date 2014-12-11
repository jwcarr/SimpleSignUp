<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False, $_REQUEST['own']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}
else {
  $date_time = explode('|', $_REQUEST['timeslot']);
  $date = $date_time[0];
  $time = $date_time[1];
  if (count($experiment->getSlot($date, $time)) < $experiment->getPerSlot()) {
    $edit_details_link = "index.php?page=signup&exp={$experiment->id}&name={$_REQUEST['name']}&email={$_REQUEST['email']}&phone={$_REQUEST['phone']}";
  }
  else {
    // If not, send back to calendar page
    $page = 'calendar';
    $warning_message = True;
  }
}

?>
