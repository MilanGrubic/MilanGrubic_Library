<?php

    // Start or resume session to track if user is logged in
    session_start();

    // Include connection to database
    include 'libraryConnect.php';

    // Check if user isn't logged in, if so, user is redirected to Login page
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
    {
        header("Location: libraryLogin.php");
        exit;
    } // End if (access control)

?> <!-- End PHP block (session + access control) -->

<!-- HTML to display search  -->
<!DOCTYPE html>
<html>
    <head>
        <title>Library Home Page</title>
        <link rel="stylesheet" type="text/css" href="libraryStyles.css">
    </head>
    <body>
        <header>
            <h1>Donabate Library</h1>
            <nav>
                <a href="libraryHome.php">Home</a>
                <a href="libraryReservations.php">My Reservations</a>
                <a href="libraryLogout.php">Log Out</a>
                <p class="logged-in">
                    Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                </p>
            </nav>
        </header>
        <main>
            <h3>Search for a Book</h3>
            <!-- Send user input to librarySearch.php -->
            <form action="librarySearch.php" method="get">

                <!-- Search by title -->
                <label for="title">Title:</label>
                <input type="text" id="title" name="title">

                <!-- Search by author -->
                <label for="author">Author:</label>
                <input type="text" id="author" name="author">

                <!-- Search by category -->
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">-- Select Category --</option>
                    <?php
                        // Populate dropdown by looping through selected columns
                        $sql = "SELECT CategoryID, CategoryDescription FROM Categories ORDER BY CategoryDescription";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) 
                        {
                            echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryDescription'] . "</option>";
                        }
                    ?> <!-- End PHP block (search user input) -->
                </select>

                <button type="submit">Search</button>

            </form>
        </main>
        <footer>
            <p>&copy; 2025 Donabate Library. All rights reserved.</p>
            <p>Contact: info@donabatelibrary.ie</p>
        </footer>
    </body>
</html>
