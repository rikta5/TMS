<?php
session_start();

require '../includes/db.php';
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs for security
    /** @noinspection PhpUndefinedVariableInspection */
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = hash('sha256', $password);
    // Check if username and password are correct
    $sql = "SELECT u.user_id, u.username FROM users u WHERE username='$username' AND password_hash='$hashed_password'";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $count = mysqli_num_rows($result);

    if ($count == 1) { // If result matched $username and $password, table row must be 1 row

        // Set session variables
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $hashed_password;
        mysqli_close($conn);
        sleep(1);
        header('Location:' . BASE_URL. 'public/dashboard.php'); // Redirect to dashboard page
    } else {
        $_SESSION['wrong_input'] = true;
        header('Location:' . BASE_URL . 'public/login.php');
        exit;
    }
}