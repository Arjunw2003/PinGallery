
<?php
$host = 'localhost';
$user = 'root';
$password = 'Dream@2003';
$dbname = 'pinterest_gallery';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
?>