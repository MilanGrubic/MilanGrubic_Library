<?php
session_start();
include 'libraryConnect.php';

// Access control
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: libraryLogin.php");
    exit;
}

$username = $_SESSION['username'];
$isbn = $_POST['isbn'] ?? '';

if (empty($isbn)) {
    die("Invalid request: no ISBN provided.");
}

// 1. Delete reservation
$delete_sql = "DELETE FROM Reservations WHERE ISBN = ? AND Username = ?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($delete_stmt, "ss", $isbn, $username);
mysqli_stmt_execute($delete_stmt);

// 2. Update book status to Available
$update_sql = "UPDATE Books SET Reserved = 'N' WHERE ISBN = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "s", $isbn);
mysqli_stmt_execute($update_stmt);

// Redirect back to reservations page
header("Location: libraryReservations.php");
exit;
?>