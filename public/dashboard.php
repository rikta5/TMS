<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if(!isset($_SESSION['user_id'])) {
    header('Location:' . BASE_URL . 'public/login.php');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/general.css">
</head>
<body>
<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/main_page.php'; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL . 'public/profile_management.php'; ?>">My Profile</a></li>
            </ul>
            <a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log out</a>
        </nav>
    </header>
    <main>
        <div class="card assigned-tasks">
            <h2>Assigned Tasks</h2>
            <p>You don't have any assigned tasks.</p>
        </div>
        <div class="card projects">
            <h2>Projects</h2>
            <p>No projects available.</p>
        </div>
        <div class="card people">
            <h2>People</h2>
            <p>No people in your workspace.</p>
        </div>
        <div class="card private-notepad">
            <h2>Private Notepad</h2>
            <p>Write down anything here...</p>
        </div>
    </main>
</div>
</body>
</html>
