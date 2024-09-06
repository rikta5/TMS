<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
include "../includes/login_requirement.php";

$user_id = $_SESSION['user_id'];
$friend_id = isset($_POST['friend_id']) ? intval($_POST['friend_id']) : 0;

// Check if friend request already exists
$query = "
    SELECT * FROM friends
    WHERE (user_id = $user_id AND friend_id = $friend_id) 
    OR (user_id = $friend_id AND friend_id = $user_id)";
/** @noinspection PhpUndefinedVariableInspection */
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // A friend request or a friendship already exists
    header('Location: ' . BASE_URL . 'public/profile_management.php?user_id=' . $friend_id . '&error=alreadyexists');
    exit();
}

// Insert new friend request
$query = "
    INSERT INTO friends (user_id, friend_id, status) 
    VALUES ($user_id, $friend_id, 'Pending')";
if (mysqli_query($conn, $query)) {
    // Insert notification
    $notification_query = "INSERT INTO notifications (user_id, message) VALUES ($friend_id, 'You have a new friend request from $user_id.')";
    mysqli_query($conn, $notification_query);
    header('Location: ' . BASE_URL . 'public/profile_management.php?user_id=' . $friend_id . '&success=requestsent');
} else {
    // Handle error
    header('Location: ' . BASE_URL . 'public/profile_management.php?user_id=' . $friend_id . '&error=failed');
}
exit();
