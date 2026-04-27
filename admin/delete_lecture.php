<?php
require_once '../config.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT thumbnail FROM lectures WHERE id = ?");
        $stmt->execute([$id]);
        $lesson = $stmt->fetch();

        if ($lesson) {
            $image_name = $lesson['thumbnail'];
            $image_path = "../uploads/posters/" . $image_name;

            if ($image_name != 'default.jpg' && file_exists($image_path)) {
                unlink($image_path);
            }


            $delete_stmt = $pdo->prepare("DELETE FROM lectures WHERE id = ?");
            $delete_stmt->execute([$id]);


            header("Location: index.php?msg=deleted");
            exit();
        } else {
            die("الحصة غير موجودة.");
        }

    } catch (PDOException $e) {
        die("خطأ أثناء الحذف: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}