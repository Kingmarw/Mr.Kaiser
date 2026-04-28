<?php
$host = ''; // Host     
$db_name = ''; // DB_NAME (You can find the SQL file inside the 'sql' folder)
$username = ''; // Username      
$password = ''; // Password

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

} catch (PDOException $e) {

    die("Database connection error.");
}


if (isset($_SESSION['user_id']) && !isset($_SESSION['user_loaded'])) {

    $stmt = $pdo->prepare("SELECT balance, avatar_url FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_balance'] = $user['balance'];
        $_SESSION['user_avatar'] = $user['avatar_url'] ?? '';
    }

    $_SESSION['user_loaded'] = true;
}


function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
