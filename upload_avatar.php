<?php
require_once 'config.php';


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "غير مصرح لك"]);
    exit;
}

if (!isset($_FILES['avatar'])) {
    echo json_encode(["success" => false, "error" => "لم يتم إرسال أي ملف"]);
    exit;
}

$file = $_FILES['avatar']['tmp_name'];

if (!getimagesize($file)) {
    echo json_encode(["success" => false, "error" => "الملف ليس صورة صالحة"]);
    exit;
}

if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
    echo json_encode(["success" => false, "error" => "حجم الصورة أكبر من 2 ميجابايت"]);
    exit;
}

$cloud_name = "deltlycbz";
$api_key    = "357368565114475"; 
$api_secret = "3ZZkH1zYPrYlrc1NMPHuWU-ikEA"; // يفضل نقلها لملف config أو .env مستقبلاً

$timestamp = time();
$params = [
    "timestamp" => $timestamp,
    "folder" => "students/avatars"
];

ksort($params);
$signature_string = urldecode(http_build_query($params));
$signature = sha1($signature_string . $api_secret);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = [
    "file" => new CURLFile($file),
    "api_key" => $api_key,
    "timestamp" => $timestamp,
    "signature" => $signature,
    "folder" => "students/avatars"
];

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (!isset($result['secure_url'])) {
    echo json_encode(["success" => false, "error" => "فشل الرفع إلى سيرفر الصور"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
    $stmt->execute([$result['secure_url'], $_SESSION['user_id']]);
    $_SESSION['user_avatar'] = $result['secure_url'];

    echo json_encode([
        "success" => true,
        "url" => $result['secure_url']
    ]);
    exit; // مهم جداً عشان مفيش أي حاجة تتطبع بعد الـ JSON
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "فشل تحديث قاعدة البيانات"]);
    exit;
}