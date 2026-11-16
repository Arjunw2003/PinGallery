<?php
require_once 'config/db.php';

$username = 'admin';
$password = 'arjun123';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($check, "s", $username);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);

if (mysqli_num_rows($res) > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "ss", $new_hash, $username);
    mysqli_stmt_execute($stmt);
    echo "<h3 style='color:green;'>Success! Admin password updated successfully to 'arjun123'.</h3>";
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $username, $new_hash);
    mysqli_stmt_execute($stmt);
    echo "<h3 style='color:green;'>Success! Admin user created with password 'arjun123'.</h3>";
}

echo "<p><a href='index.php'>Login Page</a></p>";
?>