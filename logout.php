<?php 
    session_start(); // Ensure the session is started before destroying it
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Corrected syntax and URL
    exit(); // Always add exit after a header redirect
?>
