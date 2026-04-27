<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
    header("Location: ../login.php");
    exit;
}
?>