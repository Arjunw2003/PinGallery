<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $tokenHash = hash('sha256', $token);

    $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE remember_token = ? AND token_expiry > NOW() LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = (int)$row['id'];
        $_SESSION['username'] = $row['username'];
    } else {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }
    mysqli_stmt_close($stmt);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}