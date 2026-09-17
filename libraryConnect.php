<?php

    // Database connection settings
    $servername = "localhost"; // Local server
    $username   = "root"; // Default MySQL username
    $password   = ""; // No MySQL password
    $dbname     = "libraryDb"; // The database to connect

    // Create a connection object (an instance of the mySQLi class)
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if connection failed
    if ($conn->connect_error)
    {
        //Show error message if connection failed
        die("Connection failed: " . $conn->connect_error);
    } // End if (connection check)

?> <!-- End PHP block (database connection) -->



