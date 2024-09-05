<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

$view_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user_id'];
$user_id = $_SESSION['user_id'];

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

// Check if the logged-in user and the viewed user are friends
$friend_status_query = "
    SELECT status 
    FROM friends
    WHERE (user_id = $user_id AND friend_id = $view_user_id) 
    OR (user_id = $view_user_id AND friend_id = $user_id)
    LIMIT 1";
$friend_status_result = mysqli_query($conn, $friend_status_query);
$friend_status = mysqli_fetch_assoc($friend_status_result);

// Fetch task statistics
$task_query = "
    SELECT COUNT(tasks.task_id) as total_tasks, 
           SUM(CASE WHEN tasks.status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks 
    FROM task_users 
    JOIN tasks ON task_users.task_id = tasks.task_id 
    WHERE task_users.user_id = $view_user_id";
$task_result = mysqli_query($conn, $task_query);
$task_stats = mysqli_fetch_assoc($task_result);


// Fetch comments count
$comment_query = "SELECT COUNT(*) as total_comments FROM comments WHERE user_id = $view_user_id";
$comment_result = mysqli_query($conn, $comment_query);
$comment_stats = mysqli_fetch_assoc($comment_result);

// Fetch invitations
$invitation_query = "
    SELECT ti.invitation_id, ti.task_id, ti.user_id, ti.role, ti.status, t.title 
          FROM task_invitations ti 
          JOIN tasks t ON ti.task_id = t.task_id 
          WHERE ti.user_id = $view_user_id AND ti.status = 'Pending'";
$invitation_result = mysqli_query($conn, $invitation_query);

// Fetch pending friend requests
$friend_request_query = "
    SELECT u.user_id, u.username, u.profile_picture
    FROM friends f
    JOIN users u ON f.user_id = u.user_id
    WHERE f.friend_id = $view_user_id AND f.status = 'Pending'";
$friend_request_result = mysqli_query($conn, $friend_request_query);
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
                    <li><a href="<?php echo BASE_URL . 'public/add_friends.php'; ?>">Add a Friend</a></li>
                    <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log Out</a></li>
                <?php endif; ?>
            </ul>
        </nav>

    </header>

    <main class="profile-main">

        <section class="profile-info">
            <img src="../img/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
            <div class="user-details">
                <div class="first_details">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <?php if ($view_user_id == $_SESSION['user_id']): ?>
                        <a href="<?php echo BASE_URL . 'public/edit_profile.php'; ?>" class="edit-profile-button">Edit Profile</a>
                        <a href="<?php echo BASE_URL . 'public/change_password.php'; ?>" class="edit-profile-button">Change Password</a>
                    <?php else: ?>
                </div>
                <div class="friend-status">
                    <?php if ($friend_status && $friend_status['status'] === 'Accepted'): ?>
                        <p><strong>Friends</strong></p>
                    <?php else: ?>
                        <form action="<?php echo BASE_URL . 'includes/send_friend_request.php'; ?>" method="post">
                            <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($view_user_id); ?>">
                            <button type="submit" class="btn send-request-btn">Send Friend Request</button>
                        </form>
                    <?php endif; ?>
                    <?php endif; ?>
            </div>
        </section>

        <section class="activity-summary">
            <h3>Activity Summary</h3>
            <p>Tasks Assigned: <?php echo $task_stats['total_tasks']; ?></p>
            <p>Tasks Completed: <?php echo $task_stats['completed_tasks']; ?></p>
            <p>Comments Made: <?php echo $comment_stats['total_comments']; ?></p>
        </section>

        <?php if ($view_user_id == $_SESSION['user_id']): ?>
            <section class="invitations">
                <h3>Task Invitations</h3>
                <?php if (mysqli_num_rows($invitation_result) > 0): ?>
                    <ul class="invitation-list">
                        <?php while ($invitation = mysqli_fetch_assoc($invitation_result)): ?>
                            <li class="invitation-item">
                                <div class="invitation-details">
                                    <p><strong>Task:</strong> <?php echo htmlspecialchars($invitation['title']); ?></p>
                                    <p><strong>Role:</strong> <?php echo htmlspecialchars($invitation['role']); ?></p>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($invitation['status']); ?></p>
                                </div>
                                <div class="invitation-actions">
                                    <form action="<?php echo BASE_URL . 'includes/handle_invitation.php'; ?>" method="post">
                                        <input type="hidden" name="invitation_id" value="<?php echo $invitation['invitation_id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn accept-btn">Accept</button>
                                        <button type="submit" name="action" value="decline" class="btn decline-btn">Decline</button>
                                    </form>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No invitations.</p>
                <?php endif; ?>
            </section>

            <section class="friends">
                <h3>Friend Requests</h3>
                <?php if (mysqli_num_rows($friend_request_result) > 0): ?>
                    <ul class="friends-list">
                        <?php while ($request = mysqli_fetch_assoc($friend_request_result)): ?>
                            <li class="friend-item">
                                <img src="../img/<?php echo htmlspecialchars($request['profile_picture']); ?>" alt="Profile Picture" class="profile-pic-small">
                                <div class="friend-details">
                                    <p><strong>Sender:</strong> <?php echo htmlspecialchars($request['username']); ?></p>
                                </div>
                                <div class="friend-actions">
                                    <form action="<?php echo BASE_URL . 'includes/handle_friend_request.php'; ?>" method="post">
                                        <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($request['friend_id'] ?? $request['user_id']); ?>">
                                        <button type="submit" name="action" value="accept" class="btn accept-btn">Accept</button>
                                        <button type="submit" name="action" value="decline" class="btn decline-btn">Decline</button>
                                    </form>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No friend requests.</p>
                <?php endif; ?>
            </section>



        <?php endif; ?>
    </main>
</div>
</body>
</html>
