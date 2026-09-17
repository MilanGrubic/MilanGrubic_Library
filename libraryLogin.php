<?php

    // Start or resume session to track if user is logged in
    session_start();

    // Include connection to database
    include 'libraryConnect.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" href="libraryStyles.css">
    </head>
    <body>
        <header>
            <h1>Donabate Library</h1>
            <nav>
                <a href="libraryHome.php">Home</a>
                <a href="libraryRegister.php">Register</a>
                <a href="libraryLogin.php">Log in</a>
            </nav>
        </header>
        <main>
            <h2>Login</h2>
            <?php
            // Login block only runs if form is submitted
            if ($_SERVER["REQUEST_METHOD"] === "POST") 
            {
                // Retrieve inputs
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                // Prepared statement is safe against SQL injection
                // Finds password hash for inputted username
                $stmt = $conn->prepare("SELECT Password FROM Users WHERE Username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->bind_result($hash);

                // Compare password entered and password on database
                // If it is correct, user is logged in and redirected to the home page
                if ($stmt->fetch() && password_verify($password, $hash)) 
                {
                    $_SESSION['username'] = $username;
                    $_SESSION['loggedin'] = true;
                    session_regenerate_id(true);
                    header("Location: libraryHome.php");
                    exit;
                } 
                else 
                {
                    // Else error message
                    echo "<p class='error'>Invalid username or password.</p>";
                }
                $stmt->close();


            }
            ?> <!-- End PHP block (login logic) -->

            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" required><br>
                <label>Password:</label>
                <input type="password" name="password" required><br>
                <button type="submit">Login</button>
            </form>
            <br><h4>If you don't have an account, please register:</h4>
            <form action="libraryRegister.php" method="GET">
                <button type="submit">Register</button>
            </form>
        </main>
        <footer>
                <p>&copy; 2025 Donabate Library. All rights reserved.</p>
                <p>Contact: info@donabatelibrary.ie</p>
            </footer>
    </body>
</html>
