<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$user = new User($experiment->owner);

$page_header = $experiment->getName();

// If usc (unset cookie) is set to 1, delete the cookie for this experiment ID
if ($_REQUEST['usc'] == 1) {
  setcookie($experiment->id, '', time()-3600);
}

?>
