<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../../../lib/Database.php');
include_once($filepath . '/../../../helper/Format.php');
include_once($filepath . '/../../../classes/Common.php');
include_once($filepath . '/../../../helper.php');
include_once($filepath . '/../authMiddleware.php');

$database = new Database();
$format = new Format();
$common = new Common();