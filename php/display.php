<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$user = new User($experiment->owner);

?>
