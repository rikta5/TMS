<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invitation_id = intval($_POST['invitation_id']);
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    if ($action === 'accept') {
        // Update invitation status
        $update_invitation_query = "UPDATE task_invitations SET status = 'Accepted' WHERE invitation_id = $invitation_id AND user_id = $user_id";
        /** @noinspection PhpUndefinedVariableInspection */
        if (mysqli_query($conn, $update_invitation_query)) {
            // Fetch task_id and role from the invitation
            $invitation_query = "SELECT task_id, role FROM task_invitations WHERE invitation_id = $invitation_id";
            $invitation_result = mysqli_query($conn, $invitation_query);

            if ($invitation_result && mysqli_num_rows($invitation_result) > 0) {
                $invitation = mysqli_fetch_assoc($invitation_result);
                $task_id = $invitation['task_id'];
                $role = $invitation['role'];

                // Insert into task_users table
                $insert_task_user_query = "INSERT INTO task_users (task_id, user_id, role, status) VALUES ($task_id, $user_id, '$role', 'active')";
                if (mysqli_query($conn, $insert_task_user_query)) {
                    // Mark the notification as read
                    $update_notification_query = "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND message LIKE '%invited%'";
                    mysqli_query($conn, $update_notification_query);

                    header('Location: ' . BASE_URL . 'public/profile_management.php?success=invitationaccepted');
                } else {
                    header('Location: ' . BASE_URL . 'public/profile_management.php?error=insertfailed');
                }
            } else {
                header('Location: ' . BASE_URL . 'public/profile_management.php?error=invalidinvitation');
            }
        } else {
            header('Location: ' . BASE_URL . 'public/profile_management.php?error=updatefailed');
        }
    } elseif ($action === 'decline') {
        $update_invitation_query = "UPDATE task_invitations SET status = 'Declined' WHERE invitation_id = $invitation_id AND user_id = $user_id";
        /** @noinspection PhpUndefinedVariableInspection */
        if (mysqli_query($conn, $update_invitation_query)) {
            // Mark the notification as read
            $update_notification_query = "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND message LIKE '%invited to task%'";
            mysqli_query($conn, $update_notification_query);

            header('Location: ' . BASE_URL . 'public/profile_management.php?success=invitationdeclined');
        } else {
            header('Location: ' . BASE_URL . 'public/profile_management.php?error=updatefailed');
        }
    } else {
        header('Location: ' . BASE_URL . 'public/profile_management.php?error=invalidaction');
    }
    exit();
}
