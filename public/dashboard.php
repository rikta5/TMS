<?php
include '../includes/dashboard_logic.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/general.css">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
<div class="container">
    <header>
        <img src="/img/logo_img.webp" alt="Logo image">
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL . 'public/main_page.php'; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL . 'public/profile_management.php'; ?>">My Profile</a></li>
                <li><a href="<?php echo BASE_URL . 'public/add_task.php'; ?>">New Task</a></li>
                <li><a href="<?php echo BASE_URL . 'public/add_category.php'; ?>">New Category</a></li>
            </ul>
            <a href="<?php echo BASE_URL . 'includes/logout.php'; ?>" class="link-button">Log Out</a>
        </nav>
    </header>
    <main>
        <div class="card assigned-tasks">
            <h2>Assigned Tasks</h2>
            <?php if (empty($tasks)): ?>
                <p>You don't have any assigned tasks.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($tasks as $task): ?>
                        <li>
                            <a href="<?php echo BASE_URL . 'public/task_details.php?task_id=' . intval($task['task_id']); ?>">
                                <?php echo htmlspecialchars($task['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card finished-tasks">
            <h2>Finished Tasks</h2>
            <?php if (empty($finished_tasks)): ?>
                <p>No finished tasks.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($finished_tasks as $task): ?>
                        <li>
                            <a href="<?php echo BASE_URL . 'public/task_details.php?task_id=' . intval($task['task_id']); ?>">
                                <?php echo htmlspecialchars($task['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card projects">
            <h2>Projects</h2>
            <?php if (empty($projects)): ?>
                <p>No projects available.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li>
                            <a href="<?php echo BASE_URL . 'public/project_details.php?project_id=' . $project['project_id']; ?>">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card finished-projects">
            <h2>Finished Projects</h2>
            <?php if (empty($finished_projects)): ?>
                <p>No finished projects.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($finished_projects as $project): ?>
                        <li>
                            <a href="<?php echo BASE_URL . 'public/project_details.php?project_id=' . $project['project_id']; ?>">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card people">
            <h2>People</h2>
            <?php if (empty($people)): ?>
                <p>No people in your workspace.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($people as $person): ?>
                        <li>
                            <a href="<?php echo BASE_URL . 'public/profile_management.php?user_id=' . intval($person['user_id']); ?>">
                                <?php echo htmlspecialchars($person['username']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card private-notepad">
            <h2>Private Notepad</h2>
            <a href="<?php echo BASE_URL . 'public/add_notepad_entry.php'; ?>" class="btn">New Entry</a>
            <?php if (empty($notepad_entries)): ?>
                <p>No entries in your notepad.</p>
            <?php else: ?>
                <div class="notepad-entries">
                    <?php foreach ($notepad_entries as $entry): ?>
                        <div class="note-card">
                            <p><?php echo htmlspecialchars($entry['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>