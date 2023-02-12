<?php
require 'startup.php';
    // Unset all of the session variables
    $_SESSION = array();
    // Destroy the session.
    session_destroy();
    // Redirect to login page
    header("location: index.php");
    exit;
?>
<?php 
//Testing Console_Log
//console_log("Page Loading");
//console_log($_SESSION); 
?>