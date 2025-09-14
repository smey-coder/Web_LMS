<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// User info
$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];

// Fetch books
$books_result = $conn->query("SELECT * FROM books WHERE status = 'available'");
$borrowed_result = $conn->query("
    SELECT b.id, b.title, b.author, br.borrow_date, br.due_date 
    FROM borrowed_books br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_email = '$user_email'
");

// Active page (from menu)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Library</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            font-size: 18px;
            background: #f4f6f8;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            min-height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #333;
            color: #fff;
        }
        .logout {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
            width: 100%;
        }
        .logout:hover {
            background: #c0392b;
        }
        @media (max-width: 768px) {
    body {
        font-size: 16px;
    }
    }

    /* Mobile */
    @media (max-width: 480px) {
        body {
            font-size: 14px;
        }

        .container {
            width: 100%;
            padding: 0 10px;
        }
    }
    .container {
    display: flex;
    flex-wrap: wrap;
    }

    .item {
        flex: 1 1 300px; /* Grow, shrink, base 300px */
        margin: 10px;
    }

    </style>
</head>
<body>
    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h2>📚 Library</h2>
        <a href="user_page.php?page=dashboard">🏠 Dashboard</a>
        <a href="user_page.php?page=books">📖 Available Books</a>
        <a href="user_page.php?page=borrowed">📂 My Borrowed Books</a>
        <a href="user_page.php?page=profile">👤 Profile</a>
        <form action="logout.php" method="post">
            <button class="logout" type="submit">🚪 Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php if ($page == "dashboard"): ?>
            <h1>Welcome, user<?= htmlspecialchars($user_name); ?> 👋</h1>
            <p>This is your <b>dashboard</b>. Use the menu to navigate.</p>
            <img src="image/LMS.jpg" width="1000" height="600">
            <style>
                img {
                    border-radius: 10px;
                    max-width: 100%;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                }
            </style>

        <?php elseif ($page == "books"): ?>
            <h1>📖 Available Books</h1>
            <table>
                <tr><th>Title</th><th>Author</th><th>Action</th></tr>
                <?php while($row = $books_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['author']); ?></td>
                    <td><a href="borrow.php?id=<?= $row['id']; ?>">Borrow</a></td>
                </tr>
                <?php endwhile; ?>
            </table>

        <?php elseif ($page == "borrowed"): ?>
            <h1>📂 My Borrowed Books</h1>
            <table>
                <tr><th>Title</th><th>Author</th><th>Borrow Date</th><th>Due Date</th><th>Action</th></tr>
                <?php while($row = $borrowed_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['author']); ?></td>
                    <td><?= htmlspecialchars($row['borrow_date']); ?></td>
                    <td><?= htmlspecialchars($row['due_date']); ?></td>
                    <td><a href="return.php?id=<?= $row['id']; ?>">Return</a></td>
                </tr>
                <?php endwhile; ?>
            </table>

        <?php elseif ($page == "profile"): ?>
            <h1>👤 My Profile</h1>
            <p><b>Name:</b> <?= htmlspecialchars($user_name); ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user_email); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
