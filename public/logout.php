<?php
/**
 * Logout
 */
require_once '../config.php';

$auth->logout();
redirect('index.php');
?>
