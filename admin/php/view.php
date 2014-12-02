<?php

include_once("../php/class.user.php");
include_once("../php/class.experiment.php");

if (isset($user) == False) {
  $user = new User($username, False);
}

if (isset($experiment) == False) {
  $experiment = new Experiment($_REQUEST['exp'], False);
}

$unix_today = strtotime(date('Y-m-d'));
$unix_tomorrow = $unix_today + 86400;

foreach ($experiment->getCalendar() as $date=>$slots) {
  $unix_date = strtotime($date);

  if ($unix_date < $unix_today) {
    if ($time_point != 'past') {
      $schedule .= '<div id="view-title-past"><h3>► Completed</h3></div><div id="view-past"><table style="width: 100%; border-spacing: 10px;">';
      $time_point = 'past';
    }
  }
  elseif ($unix_date == $unix_today) {
    if ($time_point == 'past') {
      $schedule .= '</table></div>';
    }
    if ($time_point != 'present') {
      $schedule .= '<div id="view-title-present"><h3>► Today</h3></div><div id="view-present"><table style="width: 100%; border-spacing: 10px;">';
      $time_point = 'present';
    }
  }
  elseif ($unix_date > $unix_today) {
    if ($time_point == 'past' OR $time_point == 'present') {
      $schedule .= '</table></div>';
    }
    if ($time_point != 'future') {
      $schedule .= '<div id="view-title-future"><h3>► Upcoming</h3></div><div id="view-future"><table style="width: 100%; border-spacing: 10px;">';
      $time_point = 'future';
    }
  }

  $schedule .= '<tr><td colspan="5"><strong>'. date('l, jS F Y', $unix_date) .'</strong></td></tr>';

  foreach ($slots as $time=>$slot) {
    for ($i=0; $i<$experiment->getPerSlot(); $i++) {
      if ($i == 0) { $show_time = $time; } else { $show_time = ''; }
      if ($slot == None) {
        $schedule .= '<tr>
        <td width="10%">' . $show_time . '</td>
        <td width="25%">-</td>
        <td width="35%">-</td>
        <td width="20%">-</td>
        <td width="10%"></td>
        </tr>';
      }
      else {
        if (isset($slot[$i])) {
          $subject = $slot[$i];
          $schedule .= '<tr>
          <td width="10%">' . $show_time . '</td>
          <td width="25%">' . $subject[0] . '</td>
          <td width="35%">' . $subject[1] .'</td>
          <td width="20%">' . $subject[2] . '</td>
          <td width="10%"><a href="index.php?page=edit_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'&subject='. $i .'">✎</a>&nbsp;<a href="index.php?page=delete_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'&subject='. $i .'">✘</a></td>
          </tr>';
        }
        else {
          $schedule .= '<tr>
          <td width="10%">' . $show_time . '</td>
          <td width="25%">-</td>
          <td width="35%">-</td>
          <td width="20%">-</td>
          <td width="10%"></td>
          </tr>';
        }
      }
    }
  }
  if ($unix_date == $unix_tomorrow) {
    $schedule .= '<tr><td colspan="2"></td><td colspan="3"><button id="reminders" class="green">Send reminder emails for tomorrow</button></td></tr>';
  }
}
$schedule .= '</table></div>';

if ($experiment->getStatus() == 'closed') {
  $change_status = '⎋ Open experiment';
}
else {
  $change_status = '⎋ Close experiment';
}

?>
