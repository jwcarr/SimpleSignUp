<?php

include("file.php");

$experiment = new Experiment($_REQUEST['exp']);

$page_header = "<h1>{$experiment->getName()}</h1>";

$timeslot = explode('|', $_REQUEST['time']);

$edit_details_link = "index.php?page=signup&exp={$experiment->id}&name={$_REQUEST['name']}&email={$_REQUEST['email']}&phone={$_REQUEST['phone']}";

?>
