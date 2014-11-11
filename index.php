<?php

include('php/globals.php');

cleanInputVariables();

// If this is a re-signup, delete the cookie for this experiment ID
if ($_REQUEST['re-sign'] != '') {
  setcookie($_REQUEST['exp'], '', time()-3600);
}

if ($_REQUEST['page'] == '') {
  if ($_REQUEST['exp'] != '') {
    $page = 'display';
  }
  else {
    $page = 'error'; $page_header = 'SimpleSignUp';
  }
}
else {
  $page = $_REQUEST['page'];
}

if ($page == 'signup') { include('php/signup.php'); }
elseif ($page == 'calendar') { include('php/calendar.php'); }
elseif ($page == 'display') { include('php/display.php'); }
elseif ($page == 'summary') { include('php/summary.php'); }
elseif ($page == 'confirm') { include('php/confirm.php'); }

?>
<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $page_header; ?></title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
<script src='js/jquery.js'></script>
</head>

<body>

<div id='header'><h1><?php echo $page_header; ?></h1></div>

<div id='body'>

<?php

if ($page == 'display') { include('html/display.html'); }

elseif ($page == 'signup') { include('html/signup.html'); include('js/signup.js'); }

elseif ($page == 'calendar') { include('html/calendar.html'); include('js/calendar.js'); }

elseif ($page == 'summary') { include('html/summary.html'); include('js/summary.js'); }

elseif ($page == 'confirm') { include('html/confirm.html'); }

elseif ($page == 'not_eligible') { include('html/not_eligible.html'); }

elseif ($page == 'fully_subscribed') { include('html/fully_subscribed.html'); }

elseif ($page == 'error') { include('html/error.html'); }

else { $error = '404'; include('html/error.html'); }

?>

</div>

</body>

</html>
