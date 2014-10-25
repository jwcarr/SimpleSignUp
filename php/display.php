<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$user = new User($experiment->owner);

$page_header = $experiment->getName();

?>
