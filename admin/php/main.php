<?php

include("../php/file.php");

$user = new User($username);

$experiments = $user->getExperiments();

?>
