<?php
require_once 'config/config.php';
require_once 'classes/User.php';

$userObj = new User();
$userObj->logout();

header('Location: login.php');
exit;
