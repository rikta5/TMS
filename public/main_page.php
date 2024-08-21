<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Management App</title>
    <link rel="stylesheet" href="/css/main_page.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <img src="/img/logo_img.webp" alt="Logo image" class="logo">
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'public/about_us.php'; ?>">About Us</a></li>
                <li><a href="#">Features</a></li>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="' . BASE_URL . 'includes/logout.php">Log Out</a></li>';
                } else {
                    echo '<li><a href="' . BASE_URL . 'public/login.php">Log In</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="intro">
            <h1>Organize Your Life with Ease: Innovative App for Smart Planning!</h1>
            <p>Welcome to a world where planning becomes a pleasure! Our app provides you with a powerful tool for efficiently managing your time and events.</p>
            <div class="cta-buttons">
                <a href="<?php echo BASE_URL . 'public/login.php'; ?>" class="cta-button primary">Get Started</a>
                <a href="<?php echo BASE_URL . 'public/about_us.php'; ?>" class="cta-button secondary">Learn More</a>
            </div>
        </section>

        <section class="calendar-section">
            <div id='calendar'></div>
        </section>
    </main>
</div>

<script src="/js/index.global.min.js"></script>
<script src='/js/main_page.js'></script>

</body>
</html>
