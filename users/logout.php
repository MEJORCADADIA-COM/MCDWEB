<?php

ob_start();
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../config/config.php');
include_once ($filepath . '/../lib/Session.php');
include_once ($filepath . '/../lib/RememberCookie.php');
Session::checkSession();
Session::destroy();
(new RememberCookie())->destroyRememberCookie();
?>