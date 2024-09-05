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
    <title>Contact Us</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/contact.css">
</head>
<body>
<div class="container">
    <header class="main-header">
        <img src="../img/logo_img.webp" alt="Logo image" class="logo">
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>public/main_page.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>public/about_us.php">About Us</a></li>
                <li><a href="<?php echo BASE_URL; ?>public/features.php">Features</a></li>
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
        <section class="contact-section">
            <h1>Contact Us</h1>
            <p>If you have any questions or feedback, feel free to reach out to us using the form below or through our contact details provided.</p>

            <div class="contact-form">
                <form action="/includes/contact_process.php" method="post">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>

                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" required>

                    <label for="message">Message</label>
                    <textarea name="message" id="message" rows="5" required></textarea>

                    <input type="submit" value="Send Message">
                </form>
            </div>

            <div class="contact-details">
                <h2>Our Contact Details</h2>
                <p><strong>Email:</strong> support@mail.com</p>
                <p><strong>Phone:</strong> 033/123-456</p>
                <p><strong>Address:</strong> Maršala Tita 38b, Sarajevo</p>
            </div>
        </section>
    </main>
</div>

</body>
</html>
