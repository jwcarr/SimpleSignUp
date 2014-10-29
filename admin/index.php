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
elseif ($page == 'view') { include('php/view.php'); }
elseif ($page == 'edit') { include('php/edit.php'); }
elseif ($page == 'delete') { include('php/delete.php'); }
elseif ($page == 'new') { include('php/new.php'); }
elseif ($page == 'change_details') { include('php/change_details.php'); }
elseif ($page == 'change_password') { include('php/change_password.php'); }

?>

</head>

<body>

<div id='header'><h1>Admin Area</h1></div>

<div id='body'>

<?php

if ($page == 'main') { include('html/main.html'); }
elseif ($page == 'view') { include('html/view.html'); }
elseif ($page == 'edit') { include('html/edit.html'); }
elseif ($page == 'delete') { include('html/delete.html'); }
elseif ($page == 'new') { include('html/new.html'); }
elseif ($page == 'change_details') { include('html/change_details.html'); }
elseif ($page == 'change_password') { include('html/change_password.html'); }
else { $error = '404'; include('html/error.html'); }

?>

</div>

</body>

</html>
