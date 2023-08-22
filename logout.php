<?php
session_start();

// Unset and destroy all session variables
$_SESSION = array();
session_destroy();

// Redirect back to the login page
header("Location: login.php");
exit();
?>

