<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /** @noinspection PhpUndefinedVariableInspection */
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $color_code = mysqli_real_escape_string($conn, $_POST['color_code']);
    $user_id = $_SESSION['user_id'];

    // Insert category data into database
    $sql = "INSERT INTO task_categories (user_id, category_name, color_code) VALUES ('$user_id', '$category_name', '$color_code')";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) <= 0) {
        die('Failed to insert category data.');
    }

    mysqli_close($conn);
    header('Location:' . BASE_URL . 'public/dashboard.php'); // Redirect to dashboard page
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Category</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/add_category.css">
</head>
<body>
<div class="container">
    <header class="header">
        <h1>Add New Task Category</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <form action="add_category.php" method="post" class="category-form">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="color_code">Color Code:</label>
                <input type="color" id="color_code" name="color_code" value="#FFFFFF">
            </div>
            <div class="form-group">
                <button type="submit">Add Category</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
