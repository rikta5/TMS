<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header('Location:' . BASE_URL . 'public/login.php');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Notepad Entry</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/add_notepad_entry.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Add Notepad Entry</h1>
    </header>
    <main>
        <form action="../includes/notepad_entry_process.php" method="post" class="notepad-form">
            <label for="entry">New Entry:</label>
            <textarea id="entry" name="entry" rows="6" required></textarea>
            <button type="submit">Add Entry</button>
        </form>
    </main>
</div>
</body>
</html>
