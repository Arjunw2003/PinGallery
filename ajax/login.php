
<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

// POST Check
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// Get Data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if ($username == '' || $password == '') {
    echo json_encode(['status' => 'error', 'message' => 'Username and password required']);
    exit;
}

// Check User
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Verify Password
if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    exit;
}

// Login Success
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful!'
]);

exit;
?>
