<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch task details
$task_query = "
SELECT tasks.*, task_users.role AS user_role, task_users.status AS user_status, tasks.status AS task_status
FROM tasks
LEFT JOIN task_users ON tasks.task_id = task_users.task_id
WHERE tasks.task_id = $task_id AND task_users.user_id = $user_id
";
/** @noinspection PhpUndefinedVariableInspection */
$task_result = mysqli_query($conn, $task_query);
$task = mysqli_fetch_assoc($task_result);

if (!$task) {
    die('Task not found or you do not have permission to manage this task.');
}

// Fetch friends who are not already invited or assigned to the task
$friends_query = "
SELECT u.user_id, u.username 
FROM users u
JOIN friends f ON u.user_id = f.friend_id
WHERE f.user_id = $user_id
AND f.status = 'Accepted'
AND u.user_id NOT IN (
    SELECT user_id FROM task_users WHERE task_id = $task_id
)
AND u.user_id NOT IN (
    SELECT user_id FROM task_invitations WHERE task_id = $task_id AND status = 'Pending'
)
";
$friends = mysqli_query($conn, $friends_query);


// Fetch task users
$task_users_query = "SELECT task_users.*, users.username FROM task_users JOIN users ON task_users.user_id = users.user_id WHERE task_users.task_id = $task_id";
$task_users = mysqli_query($conn, $task_users_query);

// Get the role of the current user for this task
$user_role_query = "SELECT role FROM task_users WHERE task_id = $task_id AND user_id = $user_id";
$user_role_result = mysqli_query($conn, $user_role_query);
$user_role = mysqli_fetch_assoc($user_role_result)['role'];

// Handle user actions (invite, change role, activate/deactivate)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['invite_user']) && $task['task_status'] != 'Completed') {
        if ($task['user_role'] == 'Owner' || $task['user_role'] == 'Contributor') {
            $invite_user_id = intval($_POST['invite_user_id']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);

            $sql = "INSERT INTO task_invitations (task_id, user_id, role, status) VALUES ($task_id, $invite_user_id, '$role', 'Pending')";
            if (mysqli_query($conn, $sql)) {
                // Create a specific notification message
                $notification_message = "You have been invited to join task ID $task_id as a $role. Please check your invitations.";

                // Insert notification into notifications table
                $insert_notification_query = "INSERT INTO notifications (user_id, message, is_read) VALUES ($invite_user_id, '$notification_message', 0)";
                mysqli_query($conn, $insert_notification_query);

                $message = "User invited successfully. They must accept the invite.";
            } else {
                $message = "Error inviting user.";
            }
        }
    } elseif (isset($_POST['change_role']) && $task['task_status'] != 'Completed') {
        if ($task['user_role'] == 'Owner') {
            $change_user_id = intval($_POST['change_user_id']);
            $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

            $sql = "UPDATE task_users SET role = '$new_role' WHERE task_id = $task_id AND user_id = $change_user_id";
            mysqli_query($conn, $sql);
            $message = "Role updated successfully.";
        }
    } elseif (isset($_POST['activate_deactivate']) && $task['task_status'] != 'Completed') {
        $user_id_to_change = intval($_POST['user_id_to_change']);
        $new_status = mysqli_real_escape_string($conn, $_POST['status']);
        $sql = "UPDATE task_users SET status = '$new_status' WHERE task_id = $task_id AND user_id = $user_id_to_change";
        mysqli_query($conn, $sql);
        $message = "Status updated successfully.";
    } elseif (isset($_POST['mark_finished']) && $task['task_status'] != 'Completed') {
        $sql = "UPDATE tasks SET status = 'Completed' WHERE task_id = $task_id";
        mysqli_query($conn, $sql);
        $message = "Task marked as finished.";
    }
}
function getPriorityText($priority) {
    switch ($priority) {
        case 0:
            return 'Low';
        case 1:
            return 'Medium';
        case 2:
            return 'High';
        default:
            return 'Unknown';
    }
}

$task['priority_text'] = getPriorityText($task['priority']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manage Task</title>
    <link rel="stylesheet" href="/css/manage_task.css">
</head>
<body>
<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <h1>Manage Task: <?php echo htmlspecialchars($task['title']); ?></h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?> ">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <section>
            <h2>Task Details</h2>
            <p><strong>Title:</strong> <?php echo htmlspecialchars($task['title']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
            <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
            <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority_text']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($task['task_status']); ?></p>
        </section>

        <?php if ($user_role == 'Owner' || $user_role == 'Contributor'): ?>
            <section>
                <h2>Invite User</h2>
                <?php if ($task['task_status'] != 'Completed'): ?>
                    <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                        <label for="invite_user_id">User:</label>
                        <select id="invite_user_id" name="invite_user_id" required>
                            <?php foreach ($friends as $friend): ?>
                                <option value="<?php echo $friend['user_id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <?php if ($user_role == 'Owner'): ?>
                                <option value="Owner">Owner</option>
                            <?php endif; ?>
                            <option value="Contributor">Contributor</option>
                            <option value="Viewer">Viewer</option>
                        </select>
                        <button type="submit" name="invite_user">Invite User</button>
                    </form>
                <?php else: ?>
                    <p>Task is completed. You cannot invite new users.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <section>
            <h2>Collaborators</h2>
            <table>
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($task_users as $task_user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task_user['username']); ?></td>
                        <td><?php echo htmlspecialchars($task_user['role']); ?></td>
                        <td><?php echo htmlspecialchars($task_user['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <?php if ($user_role == 'Owner'): ?>
            <section>
                <h2>Manage Users</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <?php if ($task['task_status'] != 'Completed' && $user_role == 'Owner'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($task_users as $task_user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task_user['username']); ?></td>
                            <td><?php echo htmlspecialchars($task_user['role']); ?></td>
                            <td><?php echo htmlspecialchars($task_user['status']); ?></td>
                            <?php if ($task['task_status'] != 'Completed'): ?>
                                <td>
                                    <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                                        <input type="hidden" name="change_user_id" value="<?php echo $task_user['user_id']; ?>">
                                        <select name="new_role">
                                            <option value="Contributor" <?php if ($task_user['role'] == 'Contributor') echo 'selected'; ?>>Contributor</option>
                                            <option value="Viewer" <?php if ($task_user['role'] == 'Viewer') echo 'selected'; ?>>Viewer</option>
                                            <option value="Owner" <?php if ($task_user['role'] == 'Owner') echo 'selected'; ?>>Owner</option>
                                        </select>
                                        <button type="submit" name="change_role">Change Role</button>
                                    </form>

                                    <!-- Form to change status -->
                                    <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                                        <input type="hidden" name="user_id_to_change" value="<?php echo $task_user['user_id']; ?>">
                                        <select name="status">
                                            <option value="active" <?php if ($task_user['status'] == 'active') echo 'selected'; ?>>Active</option>
                                            <option value="inactive" <?php if ($task_user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                                        </select>
                                        <button type="submit" name="activate_deactivate">Change Status</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>


            <section>
                <?php if ($task['task_status'] !== 'Completed') : ?>
                <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                    <button type="submit" name="mark_finished">Mark as Finished</button>
                </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
