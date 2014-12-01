<?php

include_once('../php/file.php');

$user = new User($username);
$experiment = new Experiment($_REQUEST['exp'], True);

$experiment->setName($_REQUEST['name']);
$experiment->setDescription($_REQUEST['description']);
$experiment->setLocation($_REQUEST['location']);
$experiment->setPerSlot($_REQUEST['per_slot']);

if ($experiment->saveExperimentData()) {
  include('view.php');
  $page = 'view';
  $notification = 'Your edits have successfully been changed.';
}
else {
  $page = 'edit';
  $notification = 'Error saving your edits.';
}

$notification = '<div id="notification"><p>' . $notification . '</p></div>';

?>
