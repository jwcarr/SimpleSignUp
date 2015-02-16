<?php

include_once("../php/class.experiment.php");

if (isset($experiment) == False) {
  $experiment = new Experiment($_REQUEST['exp'], False);
}

$unix_today = strtotime(date('Y-m-d'));
$unix_tomorrow = $unix_today + 86400;

if ($experiment->getCalendar() != Null) {

  $free_slots = 0;
  $past_slots = 0;
  $present_slots = 0;
  $future_slots = 0;

  foreach ($experiment->getCalendar() as $date=>$slots) {
    $unix_date = strtotime($date);

    $empty_date = True;

    if ($unix_date < $unix_today) {
      if ($time_point != 'past') {
        $schedule .= '<div id="view-title-past" class="view-title"><h3>► Completed</h3></div><div id="view-past" class="view-table"><table style="width: 100%; border-spacing: 10px;">';
        $time_point = 'past';
      }
    }
    elseif ($unix_date == $unix_today) {
      if ($time_point == 'past') {
        $schedule .= '</table></div>';
      }
      if ($time_point != 'present') {
        $schedule .= '<div id="view-title-present" class="view-title"><h3>► Today</h3></div><div id="view-present" class="view-table"><table style="width: 100%; border-spacing: 10px;">';
        $time_point = 'present';
      }
    }
    elseif ($unix_date > $unix_today) {
      if ($time_point == 'past' OR $time_point == 'present') {
        $schedule .= '</table></div>';
      }
      if ($time_point != 'future') {
        $schedule .= '<div id="view-title-future" class="view-title"><h3>► Upcoming</h3></div><div id="view-future" class="view-table"><table style="width: 100%; border-spacing: 10px;">';
        $time_point = 'future';
      }
    }

    $schedule .= '<tr><td colspan="5"><strong>'. date('l, jS F Y', $unix_date) .'</strong></td></tr>';

    if (is_null($slots)) { $slots = array(); }

    foreach ($slots as $time=>$slot) {
      $shown_plus = False;
      for ($i=0; $i<$experiment->getPerSlot(); $i++) {
        if ($i == 0) {
          $show_time = $time;
          $show_plus = '<a href="index.php?page=add_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'"><img src="images/add.png" width="16" height="16" style="vertical-align: bottom" /></a>';
          $show_cross = '<a href="index.php?page=edit_calendar&exp='. $experiment->id .'&date='. $date .'&time='. $time .'"><img src="images/delete.png" width="16" height="16" style="vertical-align: bottom" /></a>';
        }
        else {
          $show_time = '';
          $show_plus = '';
          $show_cross = '';
        }
        if (is_null($slot)) {
          $schedule .= '<tr>
          <td width="10%">' . $show_time . '</td>
          <td width="25%">-</td>
          <td width="35%">-</td>
          <td width="20%">-</td>
          <td width="10%">' . $show_plus . '&nbsp;' . $show_cross . '</td>
          </tr>';
          if ($time_point == 'future') {
            $free_slots += 1;
          }
        }
        else {
          $empty_date = False;
          if (isset($slot[$i])) {
            $subject = $slot[$i];
            $schedule .= '<tr>
            <td width="10%">' . $show_time . '</td>
            <td width="25%">' . $subject[0] . '</td>
            <td width="35%">' . $subject[1] .'</td>
            <td width="20%">' . $subject[2] . '</td>
            <td width="10%"><a href="index.php?page=edit_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'&subject='. $i .'"><img src="images/edit.png" width="16" height="16" style="vertical-align: bottom" /></a>&nbsp;<a href="index.php?page=delete_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'&subject='. $i .'"><img src="images/delete.png" width="16" height="16" style="vertical-align: bottom" /></a></td>
            </tr>';
            if ($time_point == 'past') {
              $past_slots += 1;
            }
            if ($time_point == 'present') {
              $present_slots += 1;
            }
            if ($time_point == 'future') {
              $future_slots += 1;
            }
          }
          else {
            if ($shown_plus == False) {
              $show_plus = '<a href="index.php?page=add_subject&exp='. $experiment->id .'&date='. $date .'&time='. $time .'"><img src="images/add.png" width="16" height="16" style="vertical-align: bottom" /></a>';
              $shown_plus = True;
            }
            else {
              $show_plus = '';
            }
            $schedule .= '<tr>
            <td width="10%">' . $show_time . '</td>
            <td width="25%">-</td>
            <td width="35%">-</td>
            <td width="20%">-</td>
            <td width="10%">'. $show_plus .'</td>
            </tr>';
            if ($time_point == 'future') {
              $free_slots += 1;
            }
          }
        }
      }
    }
    if ($unix_date == $unix_tomorrow) {
      if ($experiment->getRemindersSent() === True) {
        $schedule .= '<tr><td colspan="2"></td><td colspan="3" style="color: #5D9648;">Reminder emails have been sent to tomorrow’s participants</td></tr>';
      }
      else {
        if ($experiment->getAutomatedStatus() === False) {
          $schedule .= '<tr><td colspan="2"></td><td colspan="3"><button id="reminders" class="green">Send reminder emails for tomorrow</button></td></tr>';
        }
    }
    }
    if ($empty_date == True) {
      $schedule .= '<tr><td colspan="5"><a href="index.php?page=edit_calendar&exp='. $experiment->id .'&date='. $date .'"><img src="images/delete.png" width="16" height="16" style="vertical-align: bottom" /></a>&nbsp;Delete this date</td></tr>';
    }
  }
  $schedule .= '</table></div>';
}
else {
  $schedule .= '<p>No calendar has been set up for this experiment.</p>';
}

if ($experiment->getStatus() == 'closed') {
  $change_status_button = '<button id="change_status" class="orange">Open experiment</button>';
}
else {
  $change_status_button = '<button id="change_status" class="red">Close experiment</button>';
}

if ($experiment->owner == $identity[0] AND $experiment->getStatus() == 'closed') {
  $delete_button = '<button id="delete_experiment" class="red">Delete experiment</button>';
}

$total_slots = $past_slots + $present_slots + $future_slots + $free_slots;
if ($total_slots != 0) {
  $percentage_past = ($past_slots / $total_slots) * 100;
  $percentage_present = ($present_slots / $total_slots) * 100;
  $percentage_future = ($future_slots / $total_slots) * 100;
  $percentage_free = ($free_slots / $total_slots) * 100;
}

$legend = array();

if ($percentage_past != 0) {
  $past_div = '<div style="background-color: #5D9648; height: 10px; width: ' . $percentage_past . '%; float: left;"></div>';
  $legend[] = '<span style="color: #5D9648">Completed: ' . $past_slots . '</span>';
}

if ($percentage_present != 0) {
  $present_div = '<div style="background-color: #3B6C9D; height: 10px; width: ' . $percentage_present . '%; float: left;"></div>';
  $legend[] = '<span style="color: #3B6C9D">Today: ' . $present_slots . '</span>';
}

if ($percentage_future != 0) {
  $future_div = '<div style="background-color: #E8A13D; height: 10px; width: ' . $percentage_future . '%; float: left;"></div>';
  $legend[] = '<span style="color: #E8A13D">Upcoming: ' . $future_slots . '</span>';
}

if ($percentage_free != 0) {
  $free_div = '<div style="background-color: #EFEFEF; height: 10px; width: ' . $percentage_free . '%; float: left;"></div>';
  $legend[] = 'Available slots: ' . $free_slots;
}

$legend = implode('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $legend);

?>
