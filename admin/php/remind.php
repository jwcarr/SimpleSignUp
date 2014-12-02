<?php

include_once("../php/class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False);

$subject_details = $experiment->getSlot($_REQUEST['slot_num']);
$subject_name = $subject_details[$_REQUEST['subject']][0];
$subject_email = $subject_details[$_REQUEST['subject']][1];

$email_content = str_replace("\r\n", "<br />", $experiment->createEmailContent('email_reminder', array('NAME'=>$subject_name, 'TIME'=>$_REQUEST['time'])));

if ($_REQUEST['confirm'] == 'true') {
  $page = 'view';
  if ($experiment->sendEmail($subject_email, $user->getName(), $user->getEmail(), 'email_reminder', array('NAME'=>$subject_name, 'TIME'=>$_REQUEST['time']))) {
    $notification = "Sent reminder email to $subject_name";
  }
  else {
    $notification = "Email to $subject_name failed to send";
  }
  $notification = '<div id="notification"><p>' . $notification . '</p></div>';
}

?>
