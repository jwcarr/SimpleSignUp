<?php

include_once("../php/class.experiment.php");

if (isset($_REQUEST['confirm'])) {

  $experiment = new Experiment($_REQUEST['exp'], True);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);
  $experiment->deleteSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);

  if ($experiment->saveExperimentData()) {
    $notification = '"'. $subject[0] .'" has been removed from this experiment.';
    $notification_colour = 'green';
  }
  else {
    $notification = 'There was an error removing "'. $subject[0] .'" from this experiment.';
    $notification_colour = 'red';
  }

  $notification = '<div id="notification" class="notification-'. $notification_colour .'"><p>' . $notification . '</p></div>';

  include('php/view.php');
  $page = 'view';

}

else {
  $experiment = new Experiment($_REQUEST['exp'], False);
  $subject = $experiment->getSubject($_REQUEST['date'], $_REQUEST['time'], $_REQUEST['subject']);
  $subject_name = str_replace(' ', '%20', $subject[0]);
  $subject_phone = str_replace(' ', '%20', $subject[2]);
  $re_sign_link = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  preg_match('/(.*?)admin\//s', $re_sign_link, $matches);
  $re_sign_link = $matches[1] . '?exp='. $experiment->id .'&re-sign=1&page=calendar&name='. $subject_name .'&email='. $subject[1] . '&phone='. $subject_phone;
}

?>
