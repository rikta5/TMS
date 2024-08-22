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
    <title>About Us</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/about_us.css">
</head>
<body>
<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/main_page.php'; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL . 'public/features.php'; ?>">Features</a></li>
                <li><a href="<?php echo BASE_URL . 'public/contact.php'; ?>">Contact</a></li>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="' . BASE_URL . 'includes/logout.php" class="link-button">Log Out</a></li>';
                } else {
                    echo '<li><a href="' . BASE_URL . 'public/login.php" class="link-button">Log In</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <section class="about-section">
            <h1>About Us</h1>
            <p>Welcome to our Task Management App! We are dedicated to helping you organize and manage your tasks efficiently. Our team is passionate about productivity and aims to provide tools that make planning and scheduling easy and effective.</p>
            <h2>Our Mission</h2>
            <p>Our mission is to empower individuals and teams to achieve their goals through innovative and intuitive task management solutions. We believe that with the right tools, anyone can optimize their time and achieve their full potential.</p>
            <h2>Our Team</h2>
            <div class="team-members">
                <div class="team-member">
                    <img src="/img/default-avatar-icon.jpg" alt="Jane Doe">
                    <h3>Jane Doe</h3>
                    <p>Lead Developer</p>
                </div>
                <div class="team-member">
                    <img src="/img/default-avatar-icon.jpg" alt="John Smith">
                    <h3>John Smith</h3>
                    <p>UX/UI Designer</p>
                </div>
                <div class="team-member">
                    <img src="/img/default-avatar-icon.jpg" alt="Alice Johnson">
                    <h3>Alice Johnson</h3>
                    <p>Project Manager</p>
                </div>
            </div>
            <h2>Contact Us</h2>
            <p>If you have any questions or feedback, feel free to <a style="text-decoration: none; color: #007bff" href="<?php echo BASE_URL . 'public/contact.php'; ?>">contact us</a>. We're always happy to hear from our users!</p>
        </section>
    </main>
</body>
</html>