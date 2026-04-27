<?php
require_once 'auth_check.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $action = $_POST['action'];

    if ($amount > 0) {
        if ($action == 'add') {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        }
        
        if ($stmt->execute([$amount, $user_id])) {
            header("Location: view_student.php?id=$user_id&msg=تم تحديث الرصيد بنجاح");
            exit;
        }
    }
}
header("Location: manage_students.php");