<!DOCTYPE HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SimpleSignUp Admin Area</title>
<link rel="stylesheet" type="text/css" href="../css/stylesheet.css" />

<script src='../js/jquery.js'></script>

<?php

include('php/globals.php');

$username = $_SERVER['PHP_AUTH_USER'];

if ($_REQUEST['page'] == '') { $page = 'main'; }
else { $page = $_REQUEST['page']; }

if ($page == 'main') { include('php/main.php'); }

?>

</head>

<body>

<div id='header'><h1>Admin Area</h1></div>

<div id='body'>

<?php

if ($page == 'main') { include('html/main.html'); }

else { $error = '404'; include('html/error.html'); }

?>

</div>

</body>

</html>
