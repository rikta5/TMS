<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

$user_id = $_SESSION['user_id'];

// Fetch unread notifications count
$notification_query = "
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE user_id = $user_id AND is_read = 0
";
/** @noinspection PhpUndefinedVariableInspection */
$notification_result = mysqli_query($conn, $notification_query);
$notification_data = mysqli_fetch_assoc($notification_result);
$unread_notifications = $notification_data['unread_count'];

// Fetch assigned tasks that are not completed
$tasks_query = "SELECT * FROM tasks WHERE task_id IN (
    SELECT task_id FROM task_users WHERE user_id = $user_id
) AND status != 'Completed'";
$tasks_result = mysqli_query($conn, $tasks_query);
$tasks = mysqli_fetch_all($tasks_result, MYSQLI_ASSOC);

// Fetch assigned tasks that are completed
$finished_tasks_query = "SELECT * FROM tasks WHERE task_id IN (
    SELECT task_id FROM task_users WHERE user_id = $user_id
) AND status = 'Completed'";
$finished_tasks_result = mysqli_query($conn, $finished_tasks_query);
$finished_tasks = mysqli_fetch_all($finished_tasks_result, MYSQLI_ASSOC);

// Fetch projects that are not completed
$projects_query = "
    SELECT p.project_id, p.project_name
    FROM projects p
    JOIN project_tasks pt ON pt.project_id = p.project_id
    JOIN tasks t ON t.task_id = pt.task_id
    WHERE pt.task_id IN (
        SELECT task_id FROM task_users WHERE user_id = $user_id
    )
    GROUP BY p.project_id, p.project_name
    HAVING COUNT(CASE WHEN t.status = 'Completed' THEN 1 END) < COUNT(t.task_id)
";
$projects_result = mysqli_query($conn, $projects_query);
$projects = mysqli_fetch_all($projects_result, MYSQLI_ASSOC);

// Fetch projects that are completed
$finished_projects_query = "
    SELECT p.project_id, p.project_name
    FROM projects p
    JOIN project_tasks pt ON pt.project_id = p.project_id
    JOIN tasks t ON t.task_id = pt.task_id
    GROUP BY p.project_id, p.project_name
    HAVING COUNT(t.task_id) = COUNT(CASE WHEN t.status = 'Completed' THEN 1 END)
";
$finished_projects_result = mysqli_query($conn, $finished_projects_query);
$finished_projects = mysqli_fetch_all($finished_projects_result, MYSQLI_ASSOC);

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

// Fetch friends list
$friends_query = "
    SELECT DISTINCT u.user_id, u.username
    FROM users u
    JOIN friends f ON (u.user_id = f.friend_id OR u.user_id = f.user_id)
    WHERE (f.user_id = $user_id OR f.friend_id = $user_id)
    AND f.status = 'Accepted'
    AND u.user_id != $user_id
";
$friends_result = mysqli_query($conn, $friends_query);
$friends = mysqli_fetch_all($friends_result, MYSQLI_ASSOC);
