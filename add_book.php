<?php
session_start();
require_once "config.php";

// Only allow admin
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);

    if (!empty($title) && !empty($author)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, status) VALUES (?, ?, 'available')");
        $stmt->bind_param("ss", $title, $author);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Book added successfully!";
            header("Location: admin_page.php?page=books");
            exit();
        } else {
            $message = "Error adding book.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Book - Admin</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; }
    .form-container {
        width: 400px; margin: 60px auto; padding: 20px;
        background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; }
    label { display: block; margin-top: 10px; }
    input[type="text"] {
        width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;
    }
    button {
        margin-top: 15px; width: 100%; padding: 10px;
        background: #2c3e50; color: white; border: none; border-radius: 5px;
        cursor: pointer;
    }
    button:hover { background: #1abc9c; }
    .back-link { display: block; margin-top: 15px; text-align: center; }
    .msg { text-align: center; color: red; margin-top: 10px; }
  </style>
</head>
<body>

<div class="form-container">
    <h2>➕ Add New Book</h2>
    <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

    <form method="POST">
        <label for="title">Book Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>

        <button type="submit">Add Book</button>
    </form>

    <a class="back-link" href="admin_page.php?page=books">⬅ Back to Books</a>
</div>

</body>
</html>
