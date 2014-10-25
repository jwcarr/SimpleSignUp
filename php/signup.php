<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}

?>
