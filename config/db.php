<?php
$host = 'localhost';
$user = 'root';
$password = 'Dream@2003';
$dbname = 'pinterest_gallery';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    // Agar connection fail ho toh ab yeh bhi JSON me error dega bina page crash kiye
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
?>