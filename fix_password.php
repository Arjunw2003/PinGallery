<?php
require_once 'config/db.php';

$username = 'admin';
$password = 'password123';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

// Check karein ki user hai ya nahi
$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($check, "s", $username);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);

if (mysqli_num_rows($res) > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "ss", $new_hash, $username);
    mysqli_stmt_execute($stmt);
    echo "<h3 style='color:green;'>Success! Admin password updated successfully to 'password123'.</h3>";
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $username, $new_hash);
    mysqli_stmt_execute($stmt);
    echo "<h3 style='color:green;'>Success! Admin user created with password 'password123'.</h3>";
}

echo "<p>Ab aap <a href='index.php'>Login Page</a> par jakar login kar sakte hain.</p>";
?>