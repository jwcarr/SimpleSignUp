<?php

include_once("../php/class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False);

$tomorrow = date('Y/m/d', strtotime(date('Y-m-d')) + 86400);
$tomorrow_slots = $experiment->getDate($tomorrow);

if ($_REQUEST['confirm'] == 'true') {

  $fails = array();

  $formatted_date = date('l jS F', strtotime($date));

  foreach ($tomorrow_slots as $time=>$slot) {
    if ($slot != Null) {
      foreach ($slot as $subject) {
        if ($experiment->sendEmail($subject[1], $user->getName(), $user->getEmail(), 'email_reminder', array('NAME'=>$subject[0], 'DATE'=>$formatted_date, 'TIME'=>$time)) == False) {
          $fails[] = $subject_name;
        }
      }
    }
  }

  $fails = implode(', ', $fails);
  if ($fails == '') {
    $notification = 'Reminder emails were successfully sent to tomorrow’s participants';
    $notification_colour = 'green';
  }
  else {
    $notification = 'Emails failed to send to: ' . $fails;
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

  $page = 'view';
  include_once("php/view.php");

}

else {

  $name_email = array();

  foreach ($tomorrow_slots as $time=>$slot) {
    if ($slot != Null) {
      foreach ($slot as $subject) {
        $name_email[] = $subject[0] . ' &lt;' . $subject[1] . '&gt;';
      }
    }
  }

  $name_email = implode('<br />', $name_email);

  $email_content = str_replace("\r\n", "<br />", $experiment->createEmailContent('email_reminder', array('NAME'=>'&lt;NAME&gt;', 'TIME'=>'&lt;TIME&gt;')));

}

?>
