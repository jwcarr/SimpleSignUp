<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$timeslot = explode('|', $_REQUEST['time']);

?>
