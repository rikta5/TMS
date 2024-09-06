<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/register.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <img src="/img/logo_img.webp" alt="Logo image" class="logo">
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/main_page.php'; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL . 'public/about_us.php'; ?>">About Us</a></li>
                <li><a href="<?php echo BASE_URL . 'public/features.php'; ?>">Features</a></li>
            </ul>
        </nav>
    </header>
    <main class="register-container">
        <h1>Register a new account</h1>
        <form action="/includes/registration_process.php" method="post" class="register-form">
            <?php
            if (isset($_SESSION['unique_check'])) {
                echo '<p class="error-message">Username or email already exists</p>';
                unset($_SESSION['unique_check']); // Unset the session variable
            }
            if (isset($_SESSION['password_check'])) {
                echo '<p class="error-message">Passwords do not match</p>';
                unset($_SESSION['password_check']); // Unset the session variable
            }
            ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required aria-required="true">
            </div>
            <input type="submit" value="Register" class="submit-button">
        </form>
        <p class="login-link">Already have an account? <a href="<?php echo BASE_URL . 'public/login.php'; ?>">Log In</a></p>
    </main>
</div>
</body>
</html>
