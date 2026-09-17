<?php
// Start session
session_start();
include 'libraryConnect.php';

// Access control: only logged-in users can reserve
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: libraryLogin.php");
    exit;
}

$username = $_SESSION['username'];   // from login session
$isbn = $_POST['isbn'];              // from hidden form field

// Safety check
if (empty($isbn)) {
    die("Invalid request: no ISBN provided.");
}

// 1. Insert reservation record
$sql = "INSERT INTO Reservations (ISBN, Username, ReservedDate) VALUES (?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $isbn, $username);
mysqli_stmt_execute($stmt);

// 2. Update book status to Reserved
$update_sql = "UPDATE Books SET Reserved = 'Y' WHERE ISBN = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "s", $isbn);
mysqli_stmt_execute($update_stmt);

header("Location: libraryReservations.php");
exit;


?>
