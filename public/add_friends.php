<?php
session_start();
require '../includes/db.php';
include "../includes/config.php";
require '../includes/login_requirement.php';

$user_id = $_SESSION['user_id'];
/** @noinspection PhpUndefinedVariableInspection */
$search_query = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Fetch friend IDs to exclude
$friends_query = "SELECT friend_id FROM friends WHERE user_id = $user_id AND status = 'Accepted'";
$friends_result = mysqli_query($conn, $friends_query);
$friends_ids = [];
while ($row = mysqli_fetch_assoc($friends_result)) {
    $friends_ids[] = $row['friend_id'];
}

// Prepare friends_ids_list
if (count($friends_ids) > 0) {
    $friends_ids_list = implode(',', $friends_ids);
    $friends_condition = "user_id NOT IN ($friends_ids_list)";
} else {
    $friends_condition = "1"; // Always true condition, so no user is excluded
}

// Fetch users based on search
$users_query = "
SELECT * FROM users 
WHERE user_id != $user_id 
AND $friends_condition
AND username LIKE '%$search_query%'
";
$users = mysqli_query($conn, $users_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_friend'])) {
    $friend_id = intval($_POST['friend_id']);
    $status = 'Pending';

    // Check if already a friend or pending request
    $check_query = "SELECT * FROM friends WHERE (user_id = $user_id AND friend_id = $friend_id) OR (user_id = $friend_id AND friend_id = $user_id)";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO friends (user_id, friend_id, status) VALUES ($user_id, $friend_id, '$status')";
        if (mysqli_query($conn, $insert_query)) {
            // Insert notification
            $notification_query = "INSERT INTO notifications (user_id, message) VALUES ($friend_id, 'You have a new friend request from $user_id.')";
            mysqli_query($conn, $notification_query);

            $message = "Friend request sent.";
        } else {
            $message = "Error sending friend request.";
        }
    } else {
        $message = "Friend request already exists or you are already friends.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Friends</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/add_friends.css">
</head>
<body>
<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <h1>Add Friends</h1>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/dashboard.php'; ?> ">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log Out</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <section>
            <h2>Search Users</h2>
            <form action="add_friends.php" method="post">
                <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>
        </section>

        <section>
            <h2>Available Users</h2>
            <?php
            // Initialize the array for excluding IDs
            $exclude_ids = [];

            // Fetch IDs of users who are already friends
            $friends_query = "SELECT friend_id FROM friends WHERE user_id = $user_id AND status = 'Accepted'";
            $friends_result = mysqli_query($conn, $friends_query);
            while ($row = mysqli_fetch_assoc($friends_result)) {
                $exclude_ids[] = $row['friend_id'];
            }

            // Fetch IDs of users who have pending requests from the current user
            $pending_requests_query = "SELECT friend_id FROM friends WHERE user_id = $user_id AND status = 'Pending'";
            $pending_requests_result = mysqli_query($conn, $pending_requests_query);
            while ($row = mysqli_fetch_assoc($pending_requests_result)) {
                $exclude_ids[] = $row['friend_id'];
            }

            // Remove duplicate IDs from $exclude_ids
            $exclude_ids = array_unique($exclude_ids);

            // Convert $exclude_ids to a comma-separated string
            $exclude_ids_str = !empty($exclude_ids) ? implode(',', array_map('intval', $exclude_ids)) : '';

            // Escape the search query
            $search_query = mysqli_real_escape_string($conn, $search_query);

            // Fetch users based on search excluding the IDs that are already friends or have pending requests
            $users_query = "
    SELECT * FROM users 
    WHERE user_id != $user_id 
    " . ($exclude_ids_str ? "AND user_id NOT IN ($exclude_ids_str)" : "") . "
    AND username LIKE '%$search_query%'
";

            $users = mysqli_query($conn, $users_query);
            ?>

            <?php if (mysqli_num_rows($users) > 0): ?>
                <ul class="user-list">
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <li class="user-item">
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                            <form action="add_friends.php" method="post">
                                <input type="hidden" name="friend_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" name="add_friend">Add Friend</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </section>

    </main>
</div>
</body>
</html>
