<?php
// Sabhi PHP errors aur exceptions ko catch karke JSON me convert karne ke liye handlers
set_exception_handler(function($e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => "PHP Error: $message in $file line $line"]);
    exit;
});

session_start();
header('Content-Type: application/json');

// Database config file include karein
$db_file = __DIR__ . '/../config/db.php';
if (!file_exists($db_file)) {
    echo json_encode(['status' => 'error', 'message' => 'Database config file nahi mili! Path check karein: ' . $db_file]);
    exit;
}
require_once $db_file;

// Check connection
if (!isset($conn) || !$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? true : false;

if ($username === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
    exit;
}

// Prepare statement
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Prepare Error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check user and verify password
if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    exit;
}

// Login successful
session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['username'];

echo json_encode(['status' => 'success', 'message' => 'Login successful! Redirecting...']);
exit;
?>