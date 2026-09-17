<?php
    // Start or resume session to track if user is logged in
    session_start();

    // Include connection to database
    include 'libraryConnect.php';

    // Check if user logged in, if not, redirect to Login page
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
    {
        header("Location: libraryLogin.php");
        exit;
    } // End if (access control)

    // Get search inputs from libraryHome.php
    $title = isset($_GET['title']) ? trim($_GET['title']) : '';
    $author = isset($_GET['author']) ? trim($_GET['author']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    // Set number of results to show on each page
    // Check if the url has a page number, if not then it is page 1 by default
    // Gets results using formula: "(page number - 1) * results_per_page"
    // e.g. (1 - 1) x 5 + start at 0
    $results_per_page = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $results_per_page;

    // Build SQL query
    $sql = "SELECT b.ISBN, b.BookTitle, b.Author, b.Edition, b.Year, c.CategoryDescription, b.Reserved
            FROM Books b
            JOIN Categories c ON b.Category = c.CategoryID";

    // Checking if input is similar to values in database
    if ($title !== '') {
        $sql .= " AND b.BookTitle LIKE '%" . mysqli_real_escape_string($conn, $title) . "%'";
    }
    if ($author !== '') {
        $sql .= " AND b.Author LIKE '%" . mysqli_real_escape_string($conn, $author) . "%'";
    }
    if ($category !== '') {
        $sql .= " AND b.Category = '" . mysqli_real_escape_string($conn, $category) . "'";
    }

    // Count total results and only display [results per page] number of results
    $count_sql = str_replace("SELECT b.ISBN, b.BookTitle, b.Author, b.Edition, b.Year, c.CategoryDescription, b.Reserved",
                            "SELECT COUNT(*) AS total", $sql);
    $count_result = mysqli_query($conn, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $results_per_page);

    // Add LIMIT for pagination
    $sql .= " LIMIT $results_per_page OFFSET $offset";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
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
    <h3>Search Results</h3>
    <!--If there are results, display them -->
    <?php if ($total_rows > 0): ?>
        <table class="results">
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Edition</th>
                <th>Year</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ISBN']); ?></td>
                    <td><?php echo htmlspecialchars($row['BookTitle']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['Edition']); ?></td>
                    <td><?php echo htmlspecialchars($row['Year']); ?></td>
                    <td><?php echo htmlspecialchars($row['CategoryDescription']); ?></td>
                    <td><?php echo ($row['Reserved'] === 'N') ? "Available" : "Reserved"; ?></td>
                    <td>
                        <!-- If book is not reserved, show option to reserve it -->
                        <?php if ($row['Reserved'] === 'N'): ?>
                            <form action="libraryReserve.php" method="post">
                                <input type="hidden" name="isbn" value="<?php echo $row['ISBN']; ?>">
                                <button type="submit">Reserve</button>
                            </form>
                        <!-- If the book is reserved, say it is not available -->
                        <?php else: ?>
                            <button disabled>Not Available</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table class="results"> 


        <!-- Display page number at the bottom with a link to other pages if they exist, passing parameters -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?title=<?php echo urlencode($title); ?>&author=<?php echo urlencode($author); ?>&category=<?php echo urlencode($category); ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
            <!-- Display message if no results -->
    <?php else: ?>
        <p>No books found matching your search.</p>
    <?php endif; ?>

</main>

<footer>
    <p>&copy; 2025 Donabate Library. All rights reserved.</p>
    <p>Contact: info@donabatelibrary.ie</p>
</footer>
</body>
</html>
