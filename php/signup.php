<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp']);

$page_header = $experiment->getName();

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}

?>
