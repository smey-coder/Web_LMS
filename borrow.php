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

    // Check if the book is available
    $check = $conn->query("SELECT * FROM books WHERE id = $book_id AND status = 'available'");
    if ($check->num_rows > 0) {
        
        // Borrow date = today
        $borrow_date = date("Y-m-d");
        // Due date = 14 days later
        $due_date = date("Y-m-d", strtotime("+14 days"));

        // Insert into borrowed_books
        $conn->query("INSERT INTO borrowed_books (user_email, book_id, borrow_date, due_date) 
                      VALUES ('$user_email', $book_id, '$borrow_date', '$due_date')");

        // Update book status
        $conn->query("UPDATE books SET status='borrowed' WHERE id = $book_id");

        $_SESSION['message'] = "Book borrowed successfully!";
    } else {
        $_SESSION['message'] = "This book is not available.";
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

header("Location: user_page.php");
exit();
