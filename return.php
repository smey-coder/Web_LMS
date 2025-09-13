<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);
    $user_email = $_SESSION['email'];

    // Check if the user really borrowed this book
    $check = $conn->query("SELECT * FROM borrowed_books WHERE book_id = $book_id AND user_email = '$user_email'");
    if ($check->num_rows > 0) {
        // Remove from borrowed_books
        $conn->query("DELETE FROM borrowed_books WHERE book_id = $book_id AND user_email = '$user_email'");

        // Update book status back to available
        $conn->query("UPDATE books SET status = 'available' WHERE id = $book_id");

        $_SESSION['message'] = "Book returned successfully!";
    } else {
        $_SESSION['message'] = "You have not borrowed this book.";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

// Redirect back to borrowed books page
header("Location: user_page.php?page=borrowed");
exit();
