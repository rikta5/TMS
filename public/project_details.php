<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);
} else {
    die("No project selected.");
}

$query = "SELECT p.project_name, p.description, p.created_at, u.username AS owner, p.user_id AS owner_id
          FROM projects p
          JOIN users u ON p.user_id = u.user_id
          WHERE p.project_id = $project_id";

/** @noinspection PhpUndefinedVariableInspection */
$project_result = mysqli_query($conn, $query);

if (mysqli_num_rows($project_result) > 0) {
    $project = mysqli_fetch_assoc($project_result);
} else {
    die("Project not found.");
}

$task_query = "
    SELECT t.task_id, t.title, t.description, t.due_date, t.priority, t.status
    FROM tasks t
    JOIN task_users tu ON tu.task_id = t.task_id
    WHERE tu.user_id = " . intval($_SESSION['user_id']) . " 
    AND t.status != 'Completed' 
    AND t.task_id NOT IN (
        SELECT pt.task_id 
        FROM project_tasks pt 
        WHERE pt.project_id = $project_id
    )";

$task_result = mysqli_query($conn, $task_query);

$project_tasks_query = "
    SELECT t.task_id, t.title, t.description, t.due_date, t.priority, t.status
    FROM project_tasks pt
    JOIN tasks t ON pt.task_id = t.task_id
    WHERE pt.project_id = $project_id";

$project_tasks_result = mysqli_query($conn, $project_tasks_query);

$users_query = "
    SELECT u.username, tu.role, tu.status
    FROM task_users tu
    JOIN users u ON tu.user_id = u.user_id
    WHERE tu.task_id IN (
        SELECT task_id FROM project_tasks WHERE project_id = $project_id
    )
    UNION 
    SELECT u.username, 'Owner' AS role, 'Active' AS status
    FROM users u
    WHERE u.user_id = " . intval($project['owner_id']);

$users_result = mysqli_query($conn, $users_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_id = intval($_POST['task_id']);
    $insert_query = "INSERT INTO project_tasks (project_id, task_id) VALUES ($project_id, $task_id)";
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Task added to project successfully!');</script>";
        header("Location: project_details.php?project_id=" . $project_id);
    } else {
        echo "<script>alert('Error adding task: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['project_name']); ?> - Project Details</title>
    <link rel="stylesheet" href="/css/project_details.css">
</head>
<body>

<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?> ">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <h2>Project Details</h2>
        <p><?php echo htmlspecialchars($project['description']); ?></p>
        <p><strong>Created by:</strong> <?php echo htmlspecialchars($project['owner']); ?></p>
        <p><strong>Created at:</strong> <?php echo htmlspecialchars($project['created_at']); ?></p>

        <h2>Add Task to Project</h2>
        <form method="POST">
            <select name="task_id" required>
                <option value="">Select a task...</option>
                <?php
                if (mysqli_num_rows($task_result) > 0) {
                    while ($task = mysqli_fetch_assoc($task_result)) {
                        echo "<option value='" . htmlspecialchars($task['task_id']) . "'>" . htmlspecialchars($task['title']) . "</option>";
                    }
                } else {
                    echo "No available tasks to add to this project.";
                }
                ?>
            </select>
            <button type="submit" name="add_task" class="add-task-button">Add Task</button>
        </form>

        <h2>Tasks in this Project</h2>
        <table class="tasks-table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Priority</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (mysqli_num_rows($project_tasks_result) > 0) {
                while ($project_task = mysqli_fetch_assoc($project_tasks_result)) {
                    echo "<tr>";
                    echo "<td><a href='task_details.php?task_id=" . htmlspecialchars($project_task['task_id']) . "'>" . htmlspecialchars($project_task['title']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($project_task['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($project_task['due_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($project_task['priority']) . "</td>";
                    echo "<td>" . htmlspecialchars($project_task['status']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No tasks are part of this project.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <h2>Collaborators</h2>
        <table class="collaborators-table">
            <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (mysqli_num_rows($users_result) > 0) {
                while ($user = mysqli_fetch_assoc($users_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['status']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No collaborators found.</td></tr>";
            }
            ?>
            </tbody>
        </table>

    </main>
</div>
</body>
</html>
