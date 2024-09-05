<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Change</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/change_password.css">
</head>
<body>
<div class="container">
    <h1>Change Password</h1>
    <form action="/includes/change_password_process.php" method="post" class="password-form">
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'emptyfields') {
                echo '<p class="error-message">Fill in all fields!</p>';
            } elseif ($_GET['error'] == 'invalidpassword') {
                echo '<p class="error-message">Invalid password!</p>';
            } elseif ($_GET['error'] == 'passwordsdontmatch') {
                echo '<p class="error-message">Passwords don\'t match!</p>';
            } elseif ($_GET['error'] == 'stmtfailed') {
                echo '<p class="error-message">Something went wrong. Please try again!</p>';
            } elseif ($_GET['error'] == 'passwordchanged') {
                echo '<p class="success-message">Password changed successfully!</p>';
            }
        }
        ?>
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" name="change_password_submit" class="submit-btn">Change Password</button>
    </form>
    <div class="links">
        <a href="<?php echo BASE_URL . 'public/profile_management.php'; ?>">Back to Profile</a>
        <a href="<?php echo BASE_URL . 'includes/logout.php'; ?> ">Log Out</a>
    </div>
</div>
</body>
</html>
