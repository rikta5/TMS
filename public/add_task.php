<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

// Fetch categories for the dropdown
$category_query = "SELECT * FROM task_categories WHERE user_id = {$_SESSION['user_id']}";
/** @noinspection PhpUndefinedVariableInspection */
$categories = mysqli_query($conn, $category_query);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Task</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/add_task.css">
</head>
<body>
<div class="container">
    <header class="header">
        <h1>Create a New Task</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <form action="/includes/add_task_process.php" method="post" class="task-form">
            <div class="form-group">
                <label for="title">Task Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category_id">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date:</label>
                <input type="date" id="due_date" name="due_date">
            </div>
            <div class="form-group">
                <label for="priority">Priority:</label>
                <select id="priority" name="priority">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="create_task">Create Task</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
