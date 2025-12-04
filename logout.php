<?php
require_once 'config/auth.php';

// Destroy session
session_destroy();

// Redirect to homepage
header('Location: index.php');
exit();
?>

