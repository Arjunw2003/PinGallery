
<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';

// POST Check
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit;
}

// Get Data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] == '1';

// Validation
if ($username == '' || $password == '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Username and password required'
    ]);
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
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid username or password'
    ]);
    exit;
}

// Login Success
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

// Remember Me
if ($rememberMe) {

    // Generate Secure Token
    $token = bin2hex(random_bytes(32));

    // Hash Token
    $tokenHash = hash('sha256', $token);

    // Expiry: 30 Days
    $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));

    // Save Token in Database
    $update = mysqli_prepare(
        $conn,
        "UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $update,
        "ssi",
        $tokenHash,
        $expiry,
        $user['id']
    );

    mysqli_stmt_execute($update);
    mysqli_stmt_close($update);

    // Save Original Token in Browser Cookie
    setcookie('remember_token', $token, [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

} else {

    // Remove Old Remember Me Token
    $update = mysqli_prepare(
        $conn,
        "UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?"
    );

    mysqli_stmt_bind_param($update, "i", $user['id']);
    mysqli_stmt_execute($update);
    mysqli_stmt_close($update);

    // Remove Old Cookie
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful!'
]);

exit;
?>
