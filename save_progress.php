<?php
require_once 'config.php';


header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة']);
    exit;
}

$user_id    = (int)$_SESSION['user_id'];
$lecture_id = (int)($_POST['lecture_id'] ?? 0);
$video_done = isset($_POST['video_done']) ? (int)(bool)$_POST['video_done'] : 0;
$pdf_done   = isset($_POST['pdf_done'])   ? (int)(bool)$_POST['pdf_done']   : 0;

if ($lecture_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'معرف المحاضرة غير صحيح']);
    exit;
}


$check = $pdo->prepare("SELECT id FROM purchases WHERE user_id = ? AND lecture_id = ?");
$check->execute([$user_id, $lecture_id]);
$purchased = $check->fetch();


$lec = $pdo->prepare("SELECT price FROM lectures WHERE id = ?");
$lec->execute([$lecture_id]);
$lecture = $lec->fetch();

if (!$purchased && $lecture && $lecture['price'] > 0) {
    echo json_encode(['success' => false, 'message' => 'لم يتم شراء هذه الحصة']);
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO lecture_progress (user_id, lecture_id, video_done, pdf_done)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            video_done = VALUES(video_done),
            pdf_done   = VALUES(pdf_done)
    ");
    $stmt->execute([$user_id, $lecture_id, $video_done, $pdf_done]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
}