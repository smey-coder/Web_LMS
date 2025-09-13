<?php
session_start();
require_once "config.php";

// Only allow admin
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Invalid request.";
    header("Location: admin_page.php?page=books");
    exit();
}

$book_id = intval($_GET['id']);

// Check if book is currently borrowed
$check = $conn->prepare("SELECT * FROM borrowed_books WHERE book_id = ?");
$check->bind_param("i", $book_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $_SESSION['message'] = "Cannot delete this book. It is currently borrowed.";
    header("Location: admin_page.php?page=books");
    exit();
}

// Delete the book
$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Book deleted successfully!";
} else {
    $_SESSION['message'] = "Error deleting book.";
}

header("Location: admin_page.php?page=books");
exit();
