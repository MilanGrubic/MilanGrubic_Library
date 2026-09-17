<?php

    // Start or resume session to track if user is logged in
    session_start();

    // Include connection to database
    include 'libraryConnect.php';

    // Remove all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: libraryLogin.php");
    exit;
?>
