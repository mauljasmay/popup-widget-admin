<?php
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();
$auth->logout();
?>