<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: loginForm.php");
    exit;
}
session_unset(); 
session_destroy();
$_SESSION['loggedin'] = false;
header("Location: loginForm.php");
exit;
?>
