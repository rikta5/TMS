<?php
session_start();
include '../includes/config.php';
require '../includes/db.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Features</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/features.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <img src="../img/logo_img.webp" alt="Logo image" class="logo">
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>public/main_page.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>public/about_us.php">About Us</a></li>
                <li><a href="<?php echo BASE_URL; ?>public/contact.php">Contact</a></li>
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
        <section class="features-section">
            <h1>Features</h1>
            <p>Discover the powerful features that make our Task Management App the perfect tool for organizing your life.</p>

            <div class="feature">
                <h2>Task Management</h2>
                <p>Efficiently manage your tasks with our intuitive task management system. Create, edit, and track tasks with ease.</p>
                <img src="/img/default_image.webp" alt="Task Management">
            </div>

            <div class="feature">
                <h2>Calendar Integration</h2>
                <p>Stay on top of your schedule with our integrated calendar. View, add, and edit events seamlessly.</p>
                <img src="/img/calendar_img.png" alt="Calendar Integration">
            </div>

            <div class="feature">
                <h2>Customizable Notifications</h2>
                <p>Receive timely notifications and reminders based on your preferences. Customize your alerts to stay organized.</p>
                <img src="/img/default_image.webp" alt="Customizable Notifications">
            </div>

            <div class="feature">
                <h2>Collaborative Tools</h2>
                <p>Work together with your team using our collaborative tools. Share tasks, assign responsibilities, and track progress.</p>
                <img src="/img/default_image.webp" alt="Collaborative Tools">
            </div>
        </section>
    </main>
</div>

</body>
</html>
