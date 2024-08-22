<?php
session_start();

require '../includes/db.php';
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /** @noinspection PhpUndefinedVariableInspection */
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $task_description = mysqli_real_escape_string($conn, $_POST['description']);
    $task_due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $task_priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $user_id = $_SESSION['user_id'];

    // Validate inputs
    if (empty($title) || empty($task_description) || empty($task_due_date) || empty($task_priority) || empty($category_id)) {
        header('Location: ' . BASE_URL . 'public/add_task.php?error=emptyfields');
        exit();
    }

    $sql = "INSERT INTO tasks (title, description, due_date, priority, user_id, category_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('Location: ' . BASE_URL . 'public/add_task.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'sssisi', $title, $task_description, $task_due_date, $task_priority, $user_id, $category_id);
    mysqli_stmt_execute($stmt);

    // Get the ID of the newly created task
    $task_id = mysqli_insert_id($conn);

    $sql_task_users = "INSERT INTO task_users (task_id, user_id, role) VALUES (?, ?, 'Owner')";
    $stmt_task_users = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt_task_users, $sql_task_users)) {
        header('Location: ' . BASE_URL . 'public/add_task.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt_task_users, 'ii', $task_id, $user_id);
    mysqli_stmt_execute($stmt_task_users);

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_task_users);
    mysqli_close($conn);


    header('Location: ' . BASE_URL . 'public/dashboard.php?task=success');
    exit();

} else {
    header('Location: ' . BASE_URL . 'public/add_task.php');
    exit();
}