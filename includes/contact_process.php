<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    switch ($error) {
        case 'emptyfields':
            echo '<p class="error">Please fill in all fields.</p>';
            break;
        case 'stmtfailed':
            echo '<p class="error">Something went wrong. Please try again.</p>';
            break;
    }
}
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo '<p class="success">Your message has been sent successfully!</p>';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        header('Location: ' . BASE_URL . 'public/contact.php?error=emptyfields');
        exit();
    }

    // Prepare and bind
    $sql = 'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)';
    /** @noinspection PhpUndefinedVariableInspection */
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('Location: ' . BASE_URL . 'public/contact.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $subject, $message);
    mysqli_stmt_execute($stmt);

    // Optionally, send an email notification to the admin or user here

    header('Location: ' . BASE_URL . 'public/contact.php?message=success');
    exit();
} else {
    header('Location: ' . BASE_URL . 'public/contact.php');
    exit();
}
