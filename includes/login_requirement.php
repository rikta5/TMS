<?php
if (!isset($_SESSION['user_id'])) {
header('Location:' . BASE_URL . 'public/login.php');
exit();
}