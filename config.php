<?php
$host = '';      // Host
$db_name = ''; // DB_Name
$username = '';       // Username
$password = '';           // Password

try {

    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

} catch (PDOException $e) {

    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}


if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_balance'] = $user['balance'];
    }
}
if (isset($_SESSION['user_id']) && empty($_SESSION['user_avatar'])) {
    $stmt = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_avatar'] = $row['avatar_url'] ?? '';
}

function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>