<?php
// config/auth.php

// We need to start the session here too, or include db.php first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php"); // Redirect to login page
    exit;
}
?>