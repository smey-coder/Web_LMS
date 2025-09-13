<?php
session_start();
require_once "config.php"; // database connection

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Get user info
$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];

// Fetch available books
$books_result = $conn->query("SELECT * FROM books WHERE status = 'Available'");

// Fetch borrowed books by this user
$borrowed_result = $conn->query("
    SELECT b.title, b.author, br.borrow_date, br.due_date 
    FROM borrowbook br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_email = '$user_email'
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Library</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f9f9f9; font-family: Arial, sans-serif; }
        .container { width: 90%; margin: auto; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #333; color: #fff; }
        .logout-btn { background: #e74c3c; color: white; padding: 10px; border: none; cursor: pointer; }
        .logout-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Library Management System</h1>
        <h2>Welcome, <?= htmlspecialchars($user_name); ?> (<?= htmlspecialchars($user_email); ?>)</h2>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>

        <h3>Available Books</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Action</th>
            </tr>
            <?php while($row = $books_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= htmlspecialchars($row['author']); ?></td>
                <td>
                    <a href="borrow.php?id=<?= $row['id']; ?>">Borrow</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>Your Borrowed Books</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
            <?php while($row = $borrowed_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= htmlspecialchars($row['author']); ?></td>
                <td><?= htmlspecialchars($row['borrow_date']); ?></td>
                <td><?= htmlspecialchars($row['due_date']); ?></td>
                <td>
                    <a href="return.php?title=<?= urlencode($row['title']); ?>">Return</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
