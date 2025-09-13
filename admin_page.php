<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$admin_email = $_SESSION['email'];
$admin_name  = $_SESSION['name'];

// Current page (default = home)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Library</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; }
    header { background: #2c3e50; color: #fff; padding: 15px; text-align: center; }
    nav { background: #34495e; display: flex; justify-content: center; }
    nav a {
        color: white; padding: 14px 20px; text-decoration: none; display: block;
    }
    nav a:hover { background: #1abc9c; }
    .container { width: 90%; margin: auto; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #333; color: #fff; }
    .logout { background: #e74c3c; padding: 8px 14px; color: white; border-radius: 5px; }
    .logout:hover { background: #c0392b; }
  </style>
</head>
<body>

<header>
  <h1>⚙️ Library Management - Admin Panel</h1>
  <p>Welcome, <?= htmlspecialchars($admin_name); ?> (<?= htmlspecialchars($admin_email); ?>)</p>
</header>

<nav>
  <a href="admin_page.php?page=home">🏠 Home</a>
  <a href="admin_page.php?page=books">📚 Manage Books</a>
  <a href="admin_page.php?page=users">👤 Manage Users</a>
  <a href="admin_page.php?page=records">📖 Borrowed Records</a>
  <a class="logout" href="logout.php">🚪 Logout</a>
</nav>

<div class="container">
<?php
if ($page == 'home') {
    echo "<h2>Dashboard</h2>";
    echo "<p>This is page for Admin manager</p>";

    // Show image
    echo '
        <img src="image/Library-Management-System_admin.webp" width="1000" height="600" style="
            border-radius: 10px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
        ">
    ';
}

// ------------------- Manage Books -------------------
elseif ($page == 'books') {
    echo "<h2>Manage Books</h2>";
    echo "<a href='add_book.php'>➕ Add New Book</a><br><br>";
    $books = $conn->query("SELECT * FROM books");
    if ($books->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Title</th><th>Author</th><th>Status</th><th>Action</th></tr>";
        while ($row = $books->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>".htmlspecialchars($row['title'])."</td>
                    <td>".htmlspecialchars($row['author'])."</td>
                    <td>{$row['status']}</td>
                    <td>
                        <a href='edit_book.php?id={$row['id']}'>Edit</a> | 
                        <a href='delete_book.php?id={$row['id']}' onclick=\"return confirm('Delete this book?');\">Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No books found.</p>";
    }
}

// ------------------- Manage Users -------------------
elseif ($page == 'users') {
    echo "<h2>Manage Users</h2>";
    $users = $conn->query("SELECT id, name, email, role FROM users");
    if ($users->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        while ($row = $users->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>{$row['role']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found.</p>";
    }
}

// ------------------- Borrowed Records -------------------
elseif ($page == 'records') {
    echo "<h2>Borrowed Records</h2>";
    $records = $conn->query("
        SELECT br.id, u.name, u.email, b.title, b.author, br.borrow_date, br.due_date
        FROM borrowed_books br
        JOIN users u ON br.user_email = u.email
        JOIN books b ON br.book_id = b.id
    ");
    if ($records->num_rows > 0) {
        echo "<table><tr><th>User</th><th>Email</th><th>Book</th><th>Author</th><th>Borrow Date</th><th>Due Date</th></tr>";
        while ($row = $records->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>".htmlspecialchars($row['title'])."</td>
                    <td>".htmlspecialchars($row['author'])."</td>
                    <td>{$row['borrow_date']}</td>
                    <td>{$row['due_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No borrowed records found.</p>";
    }
}
?>
</div>

</body>
</html>
