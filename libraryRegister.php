<?php

    // Start or resume session to track if user is logged in
    session_start();

    // Include connection to database
    include 'libraryConnect.php';

    // Handle form submission
    // Block runs if form was submitted and collects inputs 
    if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        // Collect and trim inputs
        $username     = trim($_POST['username']);
        $password     = $_POST['password'];
        $confirmPass  = $_POST['confirm_password'];
        $firstName    = trim($_POST['firstname']);
        $surname      = trim($_POST['surname']);
        $address1     = trim($_POST['address1']);
        $address2     = trim($_POST['address2']);
        $city         = trim($_POST['city']);
        $telephone    = trim($_POST['telephone']);
        $mobile       = trim($_POST['mobile']);

        // An array to collect error messages
        $errors = [];

        // If any input is empty, give an error message
        if (empty($username) || empty($password) || empty($confirmPass) || empty($firstName) ||
            empty($surname) || empty($address1) || empty($address2) || empty($city) ||
            empty($telephone) || empty($mobile)) {
            $errors[] = "All fields are required.";
        }

        // If password is too long/short, give an error message
        if (strlen($password) !== 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        // If password confirmation doesn't match, give an error message
        if ($password !== $confirmPass) {
            $errors[] = "Passwords do not match.";
        }

        // If input isn't a number or 10 digits, give an error message
        if (!preg_match("/^[0-9]{10}$/", $mobile)) {
            $errors[] = "Mobile number must be exactly 10 digits.";
        }

        // Check if username already exists in any rows, if it does, give error
        $stmt = $conn->prepare("SELECT Username FROM Users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) 
        {
            $errors[] = "Username already taken.";
        }
        $stmt->close();

        // If no errors, insert all of the inputs into the database
        if (empty($errors)) 
        {
            // Hash the password before inserting
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO Users 
                (Username, Password, FirstName, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $username, $hash, $firstName, $surname, $address1, $address2, $city, $telephone, $mobile);
            
            // Confirmation message, give link to login
            if ($stmt->execute()) 
            {
                echo "<p class='success'>Registration successful! Please log in</p>";
                echo "<form action=\"libraryLogin.php\" method=\"GET\">
                        <button type=\"submit\">Log in</button>
                    </form>";
            } 
            else 
            {
                // Failed to register
                echo "<p class='error'>Error: Could not register user.</p>";
            }
            $stmt->close();
        } 
        else 
        {
            // Display errors
            foreach ($errors as $error) 
            {
                echo "<p class='error'>$error</p>";
            }
        }
    } // End if (handle form submission)

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Donabate Library</title>
    <link rel="stylesheet" href="libraryStyles.css">
</head>
<body>
    <header>
        <h1>Donabate Library</h1>
        <nav>
            <a href="libraryHome.php">Home</a>
            <a href="libraryRegister.php">Register</a>
            <a href="libraryLogin.php">Log In</a>
        </nav>
    </header>
    <main>
        <h2>Register</h2>
        <form method="POST">
            <!-- User Inputs-->
            <label>Username:</label>
            <input type="text" name="username" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>

            <label>First Name:</label>
            <input type="text" name="firstname" required><br>

            <label>Surname:</label>
            <input type="text" name="surname" required><br>

            <label>Address Line 1:</label>
            <input type="text" name="address1" required><br>

            <label>Address Line 2:</label>
            <input type="text" name="address2" required><br>

            <label>City:</label>
            <input type="text" name="city" required><br>

            <label>Telephone:</label>
            <input type="text" name="telephone" required><br>

            <label>Mobile (10 digits):</label>
            <input type="text" name="mobile" required><br>

            <button type="submit">Register</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Donabate Library. All rights reserved.</p>
        <p>Contact: info@donabatelibrary.ie</p>
    </footer>
</body>
</html>
