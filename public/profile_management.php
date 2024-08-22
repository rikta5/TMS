<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

$view_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user_id'];

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = $view_user_id";
/** @noinspection PhpUndefinedVariableInspection */
$user_result = mysqli_query($conn, $query);

if (mysqli_num_rows($user_result) > 0) {
    $user = mysqli_fetch_assoc($user_result);
} else {
    // Handle the case where the user is not found
    header('Location:' . BASE_URL . 'public/dashboard.php?error=usernotfound');
    exit();
}

// Fetch task statistics
$task_query = "SELECT COUNT(*) as total_tasks, 
                      SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks 
               FROM task_users 
               WHERE user_id = $view_user_id";
$task_result = mysqli_query($conn, $task_query);
$task_stats = mysqli_fetch_assoc($task_result);

// Fetch comments count
$comment_query = "SELECT COUNT(*) as total_comments FROM comments WHERE user_id = $view_user_id";
$comment_result = mysqli_query($conn, $comment_query);
$comment_stats = mysqli_fetch_assoc($comment_result);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo ($view_user_id == $_SESSION['user_id']) ? 'My Profile' : htmlspecialchars($user['username']) . "'s Profile"; ?></title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
<div class="container">
    <header class="profile-header">
        <h1><?php echo ($view_user_id == $_SESSION['user_id']) ? 'Hello, ' . $user['username'] . '!' : htmlspecialchars($user['username']) . "'s Profile"; ?></h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <?php if ($view_user_id == $_SESSION['user_id']): ?>
                    <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Log Out</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="profile-main">
        <section class="profile-info">
            <img src="../img/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
            <div class="user-details">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <?php if ($view_user_id == $_SESSION['user_id']): ?>
                    <a href="<?php echo BASE_URL . 'public/edit_profile.php'; ?>" class="edit-profile-button">Edit Profile</a>
                    <a href="<?php echo BASE_URL . 'public/change_password.php'; ?>" class="edit-profile-button">Change Password</a>
                <?php endif; ?>
            </div>
        </section>
        <section class="activity-summary">
            <h3>Activity Summary</h3>
            <p>Tasks Assigned: <?php echo $task_stats['total_tasks']; ?></p>
            <p>Tasks Completed: <?php echo $task_stats['completed_tasks']; ?></p>
            <p>Comments Made: <?php echo $comment_stats['total_comments']; ?></p>
        </section>
    </main>
</div>
</body>
</html>
