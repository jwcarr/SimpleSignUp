<?php

include_once("../php/file.php");

if (isset($user) == False) {
  $user = new User($username);
}

if (isset($experiment) == False) {
  $experiment = new Experiment($_REQUEST['exp']);
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
      $schedule .= '<div id="view-title-present"><h3>▼ Today</h3></div><div id="view-present"><table style="width: 100%; border-spacing: 10px;">';
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

  $schedule .= '<tr><td colspan="4"><strong>'. date('l, jS F Y', $unix_date) .'</strong></td></tr>';

  if ($unix_date == $unix_tomorrow) {
    $schedule .= '<tr><td colspan="4"><form method="post" action="index.php"><input type="hidden" name="page" value="remind" /><input type="hidden" name="exp" value="<?php echo $experiment->id; ?>" /><input type="submit" id="button" name="send_reminder" value="Send reminder emails to tomorrow’s participants" /></form></td></tr>';
  }

  foreach ($slots as $time=>$slot) {
    $subjects = explode('; ', $experiment->extractElement('slot'.$slot[1], $experiment->file->data));
    for ($i=0; $i<$experiment->getPerSlot(); $i++) {
      $subject_info = explode(', ', $subjects[$i]);
      if (count($subject_info) != 3) {
        $subject_info = array('-', '-', '-');
      }
      if ($subject_info[1] != '-') {
        $subject_info[1] = '<a href="mailto:' . $subject_info[1] . '">' . $subject_info[1] . '</a>';
      }
      if ($i == 0) {
        $show_time = $slot[0];
      }
      else {
        $show_time = '';
      }
      $schedule .= '<tr><td width="10%">' . $show_time . '</td><td width="30%">' . $subject_info[0] . '</td><td width="40%">' . $subject_info[1] .'</td><td width="20%">' . $subject_info[2] . '</td></tr>';
    }
  }
}
$schedule .= '</table></div>';

if ($experiment->getStatus() == 'closed') {
  $change_status = 'Open experiment';
}
else {
  $change_status = 'Close experiment';
}

?>
