<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $profile_picture = $_FILES['profile_picture'];

    // Update profile picture if uploaded
    if ($profile_picture['size'] > 0) {
        $target_dir = "../img/";
        $target_file = $target_dir . basename($profile_picture["name"]);
        if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
            $update_query = "UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE user_id = ?";
            /** @noinspection PhpUndefinedVariableInspection */
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $update_query)) {
                mysqli_stmt_bind_param($stmt, 'sssi', $username, $email, $target_file, $user_id);
                mysqli_stmt_execute($stmt);
                $_SESSION['username'] = $username;
                header('Location:' . BASE_URL . 'public/profile_management.php?update=success');
                exit();
            } else {
                die('Failed to prepare the SQL statement.');
            }
        } else {
            die('Failed to upload file.');
        }
    } else {
        $update_query = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        /** @noinspection PhpUndefinedVariableInspection */
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $update_query)) {
            mysqli_stmt_bind_param($stmt, 'ssi', $username, $email, $user_id);
            mysqli_stmt_execute($stmt);
            $_SESSION['username'] = $username;
            header('Location:' . BASE_URL . 'public/profile_management.php?update=success');
            exit();
        } else {
            die('Failed to prepare the SQL statement.');
        }
    }
}
