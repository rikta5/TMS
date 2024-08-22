<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
/** @noinspection PhpUndefinedVariableInspection */
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $query)) {
    header('Location: ../public/dashboard.php?error=sqlerror');
    exit();
} else {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/edit_profile.css">
</head>
<body>
<div class="container">
    <header class="profile-header">
        <h1>Edit Profile</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="profile-main">
        <form action="/includes/edit_profile_process.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>
            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </main>
</div>
</body>
</html>
