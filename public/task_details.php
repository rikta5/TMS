<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
$user_id = $_SESSION['user_id'];

// Fetch task details
$task_query = "
SELECT tasks.*, task_users.role, task_users.status
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

// Fetch users for invitation
$users_query = "SELECT * FROM users WHERE user_id != $user_id";
$users = mysqli_query($conn, $users_query);

// Fetch task users
$task_users_query = "SELECT task_users.*, users.username FROM task_users JOIN users ON task_users.user_id = users.user_id WHERE task_users.task_id = $task_id";
$task_users = mysqli_query($conn, $task_users_query);

// Get the role of the current user for this task
$user_role_query = "SELECT role FROM task_users WHERE task_id = $task_id AND user_id = $user_id";
$user_role_result = mysqli_query($conn, $user_role_query);
$user_role = mysqli_fetch_assoc($user_role_result)['role'];

// Handle user actions (invite, change role, activate/deactivate)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['invite_user'])) {
        if ($task['role'] == 'Owner' || $task['role'] == 'Contributor') {
            $invite_user_id = intval($_POST['invite_user_id']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);
            $sql = "INSERT INTO task_users (task_id, user_id, role, status) VALUES ($task_id, $invite_user_id, '$role', 'active')";
            mysqli_query($conn, $sql);
            $message = "User invited successfully.";
        } else {
            $message = "You do not have permission to invite users.";
        }
    } elseif (isset($_POST['change_role'])) {
        if ($task['role'] == 'Owner') {
            $change_user_id = intval($_POST['change_user_id']);
            $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
            $sql = "UPDATE task_users SET role = '$new_role' WHERE task_id = $task_id AND user_id = $change_user_id";
            mysqli_query($conn, $sql);
            $message = "Role updated successfully.";
        } else {
            $message = "You do not have permission to change roles.";
        }
    } elseif (isset($_POST['activate_deactivate'])) {
        if ($task['role'] == 'Owner') {
            $user_id_to_change = intval($_POST['user_id_to_change']);
            $new_status = mysqli_real_escape_string($conn, $_POST['status']);
            $sql = "UPDATE task_users SET status = '$new_status' WHERE task_id = $task_id AND user_id = $user_id_to_change";
            mysqli_query($conn, $sql);
            $message = "Status updated successfully.";
        } else {
            $message = "You do not have permission to change status.";
        }
    } elseif (isset($_POST['mark_finished'])) {
        if ($task['role'] == 'Owner') {
            $sql = "UPDATE tasks SET status = 'Completed' WHERE task_id = $task_id";
            mysqli_query($conn, $sql);
            $message = "Task marked as finished.";
        } else {
            $message = "Only the owner can mark this task as finished.";
        }
    }
}
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
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($user_role == 'Owner' || $user_role == 'Contributor'): ?>
            <section>
                <h2>Invite User</h2>
                <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                    <label for="invite_user_id">User:</label>
                    <select id="invite_user_id" name="invite_user_id" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="Owner">Owner</option>
                        <option value="Contributor">Contributor</option>
                        <option value="Viewer">Viewer</option>
                    </select>
                    <button type="submit" name="invite_user">Invite User</button>
                </form>
            </section>
        <?php endif; ?>

        <?php if ($user_role == 'Owner'): ?>
            <section>
                <h2>Manage Users</h2>
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
                            <td>
                                <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post" style="display:inline;">
                                    <input type="hidden" name="change_user_id" value="<?php echo $task_user['user_id']; ?>">
                                    <select name="new_role" required>
                                        <option value="Owner" <?php if ($task_user['role'] == 'Owner') echo 'selected'; ?>>Owner</option>
                                        <option value="Contributor" <?php if ($task_user['role'] == 'Contributor') echo 'selected'; ?>>Contributor</option>
                                        <option value="Viewer" <?php if ($task_user['role'] == 'Viewer') echo 'selected'; ?>>Viewer</option>
                                    </select>
                                    <button type="submit" name="change_role">Change Role</button>
                                </form>
                            </td>
                            <td>
                                <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post" style="display:inline;">
                                    <input type="hidden" name="user_id_to_change" value="<?php echo $task_user['user_id']; ?>">
                                    <select name="status" required>
                                        <option value="active" <?php if ($task_user['status'] == 'active') echo 'selected'; ?>>Active</option>
                                        <option value="inactive" <?php if ($task_user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                                    </select>
                                    <button type="submit" name="activate_deactivate">Change Status</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section>
                <form action="task_details.php?task_id=<?php echo $task_id; ?>" method="post">
                    <button type="submit" name="mark_finished" <?php if ($task['status'] == 'Completed') echo 'disabled'; ?>>Mark as Finished</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
