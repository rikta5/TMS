<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $friend_id = isset($_POST['friend_id']) ? intval($_POST['friend_id']) : 0;
    $action = $_POST['action'] ?? '';

    // Check if the friend request exists and the current user is involved
    $check_query = "
        SELECT * 
        FROM friends 
        WHERE (user_id = $user_id AND friend_id = $friend_id) 
        OR (user_id = $friend_id AND friend_id = $user_id)
        AND status = 'Pending'";

    /** @noinspection PhpUndefinedVariableInspection */
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        if ($action === 'accept') {
            // Update status to 'Accepted'
            $update_query = "
                UPDATE friends 
                SET status = 'Accepted'
                WHERE (user_id = $user_id AND friend_id = $friend_id)
                OR (user_id = $friend_id AND friend_id = $user_id)";

            if (mysqli_query($conn, $update_query)) {

                $notification_query = "
                    UPDATE notifications
                    SET is_read = 1
                    WHERE user_id = $user_id AND message LIKE '%a new friend request from $friend_id%'";

                mysqli_query($conn, $notification_query);

                header('Location: ' . BASE_URL . 'public/profile_management.php?message=request_accepted');
                exit();
            } else {
                echo "Error updating friend request.";
            }
        } elseif ($action === 'decline') {
            // Update status to 'Declined'
            $update_query = "
                UPDATE friends 
                SET status = 'Declined'
                WHERE (user_id = $user_id AND friend_id = $friend_id)
                OR (user_id = $friend_id AND friend_id = $user_id)";

            if (mysqli_query($conn, $update_query)) {
                header('Location: ' . BASE_URL . 'public/profile_management.php?message=request_declined');
                exit();
            } else {
                echo "Error updating friend request.";
            }
        }
    } else {
        header('Location: ' . BASE_URL . 'public/profile_management.php?error=invalid_request');
        exit();
    }
}