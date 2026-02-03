<?php
require_once '../config/db.php';

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
