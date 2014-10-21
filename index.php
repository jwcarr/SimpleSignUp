<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SimpleSignUp</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
</head>

<body>

<?php

if ($_REQUEST['page'] == '') {
  if ($_REQUEST['exp'] != '') {
    $page = 'display';
  }
  else {
    $page = 'login';
  }
}
else {
  $page = $_REQUEST['page'];

}

if ($page == 'login') { include('html/login.html'); }

elseif ($page == 'display') { include('php/display.php'); include('html/display.html'); }

elseif ($page == 'signup') { include('php/signup.php'); include('html/signup.html'); }

elseif ($page == 'calendar') { include('php/calendar.php'); include('html/calendar.html'); }

elseif ($page == 'summary') { include('php/summary.php'); include('html/summary.html'); }

elseif ($page == 'confirm') { include('php/confirm.php'); include('html/confirm.html'); }

else { include('html/error.html'); }

?>

</body>

</html>
