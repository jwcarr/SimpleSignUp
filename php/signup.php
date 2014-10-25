<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$page_header = "<h1>{$experiment->getName()}</h1>";

if ($experiment->getStatus() != 'open') {
  $page = 'fully_subscribed';
}

?>
