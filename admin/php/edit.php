<?php

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);
  $experiment->setName($_REQUEST['name']);
  $experiment->setDescription($_REQUEST['description']);
  $experiment->setLocation($_REQUEST['location']);
  $experiment->setRequirements($_REQUEST['requirements']);
  $experiment->setExclusions($_REQUEST['exclusions']);
  $experiment->setManualExclusions($_REQUEST['manual_exclusions']);
  $experiment->setPerSlot($_REQUEST['per_slot']);
  $experiment->setSharedAccess($_REQUEST['shared_access'], $user->getAllUsernames());
  $experiment->setCalendar($_REQUEST['new_times']);
  $experiment->setAutomatedStatus($_REQUEST['automated_status']);
  $experiment->setEmail($_REQUEST['email_conf'], 'email_conf');
  $experiment->setEmail($_REQUEST['email_full'], 'email_full');
  $experiment->setEmail($_REQUEST['email_conf_full'], 'email_conf_full');
  $experiment->setEmail($_REQUEST['email_reminder'], 'email_reminder');

  if ($experiment->saveExperimentData()) {
    include('view.php');
    $page = 'view';
    $notification = 'Your changes have successfully been saved.';
    $notification_colour = 'green';
  }
  else {
    $notification = 'Error saving your edits.';
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

}

else {

  $experiment = new Experiment($_REQUEST['exp'], False);
  $requirements = implode("\n", $experiment->getRequirements());
  $exclusions = implode("\n", $experiment->getExclusions());

}

if ($page == 'edit') {
  $calendar_table = '<table style="width: 80%;" id="calendar">
  <tr>
  <td style="width: 20%;"><strong>Date</strong></td>
  <td style="width: 40%;"><strong>Add new times (comma separated)</strong></td>
  <td style="width: 40%;"><strong>Current times</strong></td>
  </tr>';
  foreach ($experiment->getCalendar() as $date=>$times) {
    $calendar_table .= '<tr>
    <td style="width: 20%;">'. $date .'</td>
    <td style="width: 40%;"><input type="text" name="new_times['. $date .']" value="" size="30" /></td>
    <td style="width: 40%;">'. implode(', ', array_keys($times)) .'</td>
    </tr>';
  }
  $calendar_table .= '</table>';

}

?>
