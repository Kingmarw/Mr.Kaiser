<?php


require_once 'config.php';



header('Content-Type: application/json; charset=utf-8');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صحيحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$quiz_id = (int)($_POST['quiz_id'] ?? 0);
$answers = json_decode($_POST['answers'] ?? '{}', true);

if ($quiz_id <= 0 || !is_array($answers)) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

$maxAttempts = 3;

try {

    $res_stmt = $pdo->prepare("SELECT attempts, score, status FROM quiz_results WHERE user_id = ? AND quiz_id = ?");
    $res_stmt->execute([$user_id, $quiz_id]);
    $existing = $res_stmt->fetch(PDO::FETCH_ASSOC);

    $current_attempts = $existing ? (int)$existing['attempts'] : 0;


    if ($current_attempts >= $maxAttempts) {
        echo json_encode([
            'success'  => false,
            'message'  => 'لقد استنفدت جميع المحاولات',
            'attempts' => $current_attempts,
            'status'   => $existing['status'] ?? 'failed'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }


    $q_stmt = $pdo->prepare("SELECT id, correct_option FROM quiz_questions WHERE quiz_id = ?");
    $q_stmt->execute([$quiz_id]);
    $questions = $q_stmt->fetchAll(PDO::FETCH_KEY_PAIR); 

    $total = count($questions);
    if ($total === 0) {
        echo json_encode(['success' => false, 'message' => 'لا توجد أسئلة في هذا الاختبار'], JSON_UNESCAPED_UNICODE);
        exit;
    }


    $score = 0;
    foreach ($questions as $qid => $correct) {
        if (isset($answers[(string)$qid]) && $answers[(string)$qid] === $correct) {
            $score++;
        }
    }

    $pass_mark = (int)ceil($total * 0.5);
    $new_attempts = $current_attempts + 1;

    $new_status = ($score >= $pass_mark) ? 'passed' : 'failed';


    $final_status = ($existing && ($existing['status'] ?? '') === 'passed') ? 'passed' : $new_status;


    $best_score = $existing ? max((int)$existing['score'], $score) : $score;

    if ($existing) {
        $upd = $pdo->prepare("
            UPDATE quiz_results
            SET attempts = ?, score = ?, status = ?, updated_at = NOW()
            WHERE user_id = ? AND quiz_id = ?
        ");
        $upd->execute([$new_attempts, $best_score, $final_status, $user_id, $quiz_id]);
    } else {
        $ins = $pdo->prepare("
            INSERT INTO quiz_results (user_id, quiz_id, attempts, score, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $ins->execute([$user_id, $quiz_id, $new_attempts, $best_score, $final_status]);
    }

    echo json_encode([
        'success'  => true,
        'score'    => $best_score,
        'total'    => $total,
        'attempts' => $new_attempts,
        'status'   => $final_status
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات'], JSON_UNESCAPED_UNICODE);
    exit;
}