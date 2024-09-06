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
    <title>Login</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/login.css">
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
    <main class="login-container">
        <h1>Login into your account</h1>
        <form action="/includes/login_process.php" method="post" class="login-form">
            <?php
            if (isset($_SESSION['wrong_input'])) {
                echo '<p class="error-message">Your username or password is invalid</p>';
                unset($_SESSION['wrong_input']); // Unset the session variable
            }
            ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required aria-required="true">
            </div>
            <input type="submit" value="Login" class="submit-button">
        </form>
        <p class="register-link">Don't have an account? <a href="<?php echo BASE_URL . 'public/register.php'; ?>">Register</a></p>
    </main>
</div>
</body>
</html>
