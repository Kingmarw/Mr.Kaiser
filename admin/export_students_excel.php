<?php
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$search = $_GET['search'] ?? '';
$grade_filter = $_GET['grade'] ?? '';

$where = " WHERE role = 'student' ";
$params = [];

if (!empty($search)) {
    $where .= " AND (full_name LIKE ? OR phone LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($grade_filter)) {
    $where .= " AND student_grade = ? ";
    $params[] = $grade_filter;
}

$query = "SELECT full_name, phone, balance, student_grade FROM users " . $where . " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];

$filename = "students_" . date("Y-m-d_H-i") . ".csv";

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// UTF-8 BOM عشان العربي يطلع صح في Excel
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");

// الهيدر
fputcsv($output, ['الاسم', 'رقم الهاتف', 'الرصيد', 'الصف']);

foreach ($students as $s) {
    fputcsv($output, [
        $s['full_name'],
        $s['phone'],
        number_format((float)$s['balance'], 2),
        $grades[$s['student_grade']] ?? 'غير محدد'
    ]);
}

fclose($output);
exit;
