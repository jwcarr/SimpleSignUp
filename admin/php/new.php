<?php

include_once("../php/class.user.php");
$user = new User($username, False);

include_once("../php/class.experiment.php");
$experiment = new Experiment($_REQUEST['exp'], False);

?>
