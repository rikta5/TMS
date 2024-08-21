<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if (isset($_POST['change_password_submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ensure session ID is set
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['password'])) {
        header('Location: ' . BASE_URL . 'public/change_password.php?error=sessionerror');
        exit();
    }

    // Check for empty fields
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header('Location: ' . BASE_URL . 'public/change_password.php?error=emptyfields');
        exit();
    }

    // Verify current password
    if (!password_verify(hash('sha256', $current_password), $_SESSION['password'])) {
        header('Location: ' . BASE_URL . 'public/change_password.php?error=invalidpassword');
        exit();
    }

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        header('Location: ' . BASE_URL . 'public/change_password.php?error=passwordsdontmatch');
        exit();
    }

    // Hash new password
    $hashed_password = hash('sha256', $new_password);

    // Update password in database
    $sql = 'UPDATE users SET password = ? WHERE id = ?';
    /** @noinspection PhpUndefinedVariableInspection */
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('Location: ' . BASE_URL . 'public/change_password.php?error=stmtfailed');
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, 'si', $hashed_password, $_SESSION['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Clear session and redirect
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'public/change_password.php?success=passwordchanged');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . 'public/profile_management.php');
    exit();
}
