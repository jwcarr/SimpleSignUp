<?php

include_once("class.experiment.php");
$experiment = new Experiment($_REQUEST['exp']);

include_once("class.user.php");
$user = new User($experiment->owner);

$page_header = $experiment->getName();

?>
