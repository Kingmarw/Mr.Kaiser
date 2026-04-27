<?php
require_once '../config.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
        

        header("Location: manage_quizzes.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        die("خطأ أثناء الحذف: " . $e->getMessage());
    }
} else {
    header("Location: manage_quizzes.php");
    exit;
}