<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
/** @noinspection PhpUndefinedVariableInspection */
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $query)) {
    header('Location:' . BASE_URL . 'public/dashboard.php?error=sqlerror');
    exit();
} else {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}

// Fetch task statistics
$task_query = "SELECT COUNT(*) as total_tasks, SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks FROM tasks WHERE user_id = ?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $task_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task_stats = mysqli_fetch_assoc($result);

// Fetch comments count
$comment_query = "SELECT COUNT(*) as total_comments FROM comments WHERE user_id = ?";
mysqli_stmt_prepare($stmt, $comment_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$comment_stats = mysqli_fetch_assoc($result);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Profile</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
<div class="container">
    <header class="profile-header">
        <h1>Hello, <?php echo $user['username']; ?>!</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main class="profile-main">
        <section class="profile-info">
            <img src="../img/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
            <div class="user-details">
                <h2><?php echo $user['username']; ?></h2>
                <p><?php echo $user['email']; ?></p>
                <a href="<?php echo BASE_URL . 'public/edit_profile.php'; ?>" class="edit-profile-button">Edit Profile</a>
                <a href="<?php echo BASE_URL . 'public/change_password.php'; ?>" class="edit-profile-button">Change Password</a>
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
