<?php

// Path to data folder from SimpleSignUp root
$data_path = '../../server_data/ssu/';


// Admin email address; logs will be sent here
$admin_email_address = 'PUT-AN-ADMIN-EMAIL-HERE';

// Set timezone for timestamps (default = UTC)
date_default_timezone_set('UTC');

function cleanInputVariables() {
  foreach ($_REQUEST as $key=>$value) {
    $_REQUEST[$key] = str_replace(array('"', ',' , '~', ';', '{', '}', '[', ']', '=', '&'), '', str_replace("'", '’', trim($value)));
  }
}

?>
