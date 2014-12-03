<?php

include('php/globals.php');

if (isset($_REQUEST['page'])) {
  if (in_array($_REQUEST['page'], $pages) OR in_array($_REQUEST['page'], $auth_pages)) {
    $page = $_REQUEST['page'];
  }
}
else {
  $page = 'main';
}

if ($page == 'authenticate') {
  include_once('../php/class.user.php');
  $user = new User($_REQUEST['username'], False);
  $password_hash = $user->authorize($_REQUEST['password'], False);
  if ($password_hash == True) {
    setcookie('SimpleSignUpAuth', $_REQUEST['username'] . ':' . $password_hash, time()+604800);
    $page = 'main';
  }
  else {
    $page = 'login';
  }
}
elseif ($page == 'logout') {
  setcookie('SimpleSignUpAuth', '', time()-3600);
  $page = 'login';
}
else {
  if (isset($_COOKIE['SimpleSignUpAuth'])) {
    include_once('../php/class.user.php');
    $identity = explode(':', $_COOKIE['SimpleSignUpAuth']);
    $user = new User($identity[0], False);
    if ($user->authorize($identity[1], True) == False) {
      $page = 'login';
    }
  }
  else {
    $page = 'login';
  }
}

if (in_array($page, $pages)) { include('php/' . $page . '.php'); }
elseif ($page != 'login') { $page = 'error'; }

include('index.html');

?>
