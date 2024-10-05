<?php

session_start();
include "../includes/db.php";
include "../includes/config.php";
require '../includes/login_requirement.php';

$project_name = $description = $error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);

    if (empty($project_name)) {
        $error = "Project name is required.";
    } elseif (strlen($project_name) > 255) {
        $error = "Project name cannot exceed 255 characters.";
    } else {
        $user_id = $_SESSION['user_id'];
        /** @noinspection PhpUndefinedVariableInspection */
        $create_project_query = "INSERT INTO projects (user_id, project_name, description) VALUES ('" . intval($user_id) . "', '" . mysqli_real_escape_string($conn, $project_name) . "', '" . mysqli_real_escape_string($conn, $description) . "')";

        if (mysqli_query($conn, $create_project_query)) {
            header('Location:' . BASE_URL . 'public/dashboard.php');
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <link rel="stylesheet" href="/css/create_project.css">
</head>
<body>
<div class="container">
    <h1>Create A New Project</h1>

    <?php
    if (!empty($error)) {
        echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
    }
    ?>

    <form action="create_project.php" method="POST">
        <div class="form-group">
            <label for="project_name">Project Name</label>
            <input type="text" id="project_name" name="project_name" value="<?= htmlspecialchars($project_name) ?>" required maxlength="255">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($description) ?></textarea>
        </div>

        <button type="submit">Create Project</button>
    </form>

    <a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>" class="info-text">Back to Dashboard</a>
</div>
</body>
</html>