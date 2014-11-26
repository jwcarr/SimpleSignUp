<?php

include("../php/file.php");

$user = new User($username);

$experiment = new Experiment($_REQUEST['exp']);

$requirements = implode("\n", $experiment->getRequirements());

$exclusions = implode("\n", $experiment->getExclusions());

?>
