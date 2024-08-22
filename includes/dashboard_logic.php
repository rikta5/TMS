<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header('Location:' . BASE_URL . 'public/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch assigned tasks that are not completed
$tasks_query = "SELECT * FROM tasks WHERE task_id IN (
    SELECT task_id FROM task_users WHERE user_id = $user_id
) AND status != 'Completed'";
/** @noinspection PhpUndefinedVariableInspection */
$tasks_result = mysqli_query($conn, $tasks_query);
$tasks = mysqli_fetch_all($tasks_result, MYSQLI_ASSOC);

// Fetch assigned tasks that are completed
$finished_tasks_query = "SELECT * FROM tasks WHERE task_id IN (
    SELECT task_id FROM task_users WHERE user_id = $user_id
) AND status = 'Completed'";
$finished_tasks_result = mysqli_query($conn, $finished_tasks_query);
$finished_tasks = mysqli_fetch_all($finished_tasks_result, MYSQLI_ASSOC);

// Fetch projects with completed tasks
$projects_query = "
    SELECT p.project_id, p.project_name
    FROM projects p
    JOIN tasks t ON t.task_id IN (
        SELECT task_id FROM task_users WHERE user_id = $user_id
    )
    WHERE p.project_id IN (
        SELECT DISTINCT project_id FROM tasks WHERE task_id IN (
            SELECT task_id FROM task_users WHERE user_id = $user_id
        )
    )
    GROUP BY p.project_id, p.project_name
    HAVING COUNT(t.task_id) = SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END)
";
$projects_result = mysqli_query($conn, $projects_query);
$projects = mysqli_fetch_all($projects_result, MYSQLI_ASSOC);

// Fetch unique people in workspace
$people_query = "
    SELECT DISTINCT u.*
    FROM users u
    JOIN task_users tu ON u.user_id = tu.user_id
    WHERE tu.task_id IN (
        SELECT task_id FROM task_users WHERE user_id = $user_id
    )
    AND u.user_id != $user_id
";
$people_result = mysqli_query($conn, $people_query);
$people = mysqli_fetch_all($people_result, MYSQLI_ASSOC);



// Fetch notepad entries
$notepad_query = "SELECT * FROM private_notepad WHERE user_id = $user_id";
$notepad_entries = mysqli_query($conn, $notepad_query);


