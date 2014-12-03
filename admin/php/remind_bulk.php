<?php

include_once("../php/class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False);

$tomorrow = date('Y/m/d', strtotime(date('Y-m-d')) + 86400);
$tomorrow_slots = $experiment->getDate($tomorrow);

if ($_REQUEST['confirm'] == 'true') {

  $page = 'view';

  $fails = array();

  foreach ($tomorrow_slots as $time=>$slot) {
    if ($slot != None) {
      foreach ($slot as $subject) {
        if ($experiment->sendEmail($subject[0], $user->getName(), $user->getEmail(), 'email_reminder', array('NAME'=>$subject[0], 'TIME'=>$time)) == False) {
          $fails[] = $subject_name;
        }
      }
    }
  }

  $fails = implode(', ', $fails);
  if ($fails == '') {
    $notification = 'Reminder emails were successfully sent to tomorrow’s participants';
  }
  else {
    $notification = 'Emails failed to send to: ' . $fails;
  }

  $notification = '<div id="notification"><p>' . $notification . '</p></div>';

}

else {

  $name_email = array();

  foreach ($tomorrow_slots as $time=>$slot) {
    if ($slot != None) {
      foreach ($slot as $subject) {
        $name_email[] = $subject[0] . ' &lt;' . $subject[1] . '&gt;';
      }
    }
  }

  $name_email = implode('<br />', $name_email);

  $email_content = str_replace("\r\n", "<br />", $experiment->createEmailContent('email_reminder', array('NAME'=>'&lt;NAME&gt;', 'TIME'=>'&lt;TIME&gt;')));

}

?>
