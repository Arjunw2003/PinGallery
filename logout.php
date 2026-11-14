<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header("Location: index.php");
exit;