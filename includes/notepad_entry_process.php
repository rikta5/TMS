<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        header('Location:' . BASE_URL . 'public/login.php');
        exit();
    }

    /** @noinspection PhpUndefinedVariableInspection */
    $entry = mysqli_real_escape_string($conn, $_POST['entry']);
    $user_id = $_SESSION['user_id'];

    // Insert notepad entry into database
    $sql = "INSERT INTO private_notepad (user_id, content) VALUES ('$user_id', '$entry')";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    mysqli_close($conn);

    // Redirect back to the dashboard
    header('Location:' . BASE_URL . 'public/dashboard.php');
    exit();
}
