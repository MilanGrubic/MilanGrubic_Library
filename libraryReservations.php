<?php
session_start();
include 'libraryConnect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: libraryLogin.php");
    exit;
}

$username = $_SESSION['username'];

// Get reservation details
$sql = "SELECT r.ISBN, b.BookTitle, b.Author, r.ReservedDate
        FROM Reservations r
        JOIN Books b ON r.ISBN = b.ISBN
        WHERE r.Username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations</title>
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
    <h2>My Reservations</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="results">
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Reserved Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ISBN']); ?></td>
                    <td><?php echo htmlspecialchars($row['BookTitle']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['ReservedDate']); ?></td>
                    <td>
                        <form action="libraryUnreserve.php" method="post">
                            <input type="hidden" name="isbn" value="<?php echo $row['ISBN']; ?>">
                            <button type="submit">Unreserve</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
        <p>You have no reservations.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Donabate Library. All rights reserved.</p>
    <p>Contact: info@donabatelibrary.ie</p>
</footer>
</body>
</html>
