<?php
include '../includes/config.php';
session_start();
session_unset();
session_destroy();
sleep(1);
header('Location:' . BASE_URL . 'public/main_page.php');
exit;
