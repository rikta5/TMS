<?php

session_start();

require '../includes/db.php';
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs for security
    /** @noinspection PhpUndefinedVariableInspection */
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $hashed_password = hash('sha256', $password);
    // Check if passwords match
    if ($password == $confirm_password) {
        // Insert user data into database
        $sql_check_email = "SELECT * FROM users WHERE email='$email'"; //email check
        $result_check_email = mysqli_query($conn, $sql_check_email);

        if(mysqli_num_rows($result_check_email) > 0) {
            // Email already exists
            $_SESSION['unique_check'] = true;
            header('Location:' . BASE_URL . 'public/register.php');
            exit;
        }

        // Check if username is already registered
        $sql_check_username = "SELECT * FROM users WHERE username='$username'"; //username check
        $result_check_username = mysqli_query($conn, $sql_check_username);

        if(mysqli_num_rows($result_check_username) > 0) {
            // Username already exists
            $_SESSION['unique_check'] = true;
            header('Location:' . BASE_URL . 'public/register.php');
            exit;
        }

        $sql = "INSERT INTO users (username, email, password_hash) VALUES ('$username', '$email', '$hashed_password')";
        mysqli_query($conn, $sql);

        // Set session variables
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $hashed_password;
        mysqli_close($conn);
        sleep(1);
        header('Location:' . BASE_URL . 'public/dashboard.php'); // Redirect to dashboard page
    } else {
        $_SESSION['password_check'] = true;
        sleep(1);
        header('Location:' . BASE_URL . 'public/register.php');

    }
}
