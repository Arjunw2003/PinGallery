<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '') {
    echo json_encode(['status' => 'error', 'message' => 'Title is required.']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a valid image files.']);
    exit;
}

// File size validation (5 MB) - Syntax fixed here (*)
$maxSize = 5 * 1024 * 1024; 
if ($_FILES['image']['size'] > $maxSize) {
    echo json_encode(['status' => 'error', 'message' => 'File size must not exceed 5 MB.']);
    exit;
}

$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$origName = $_FILES['image']['name'];
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

// Safe extension & image check (bina finfo_open ke)
if (!in_array($ext, $allowedExt, true) || getimagesize($_FILES['image']['tmp_name']) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, GIF and WEBP images are allowed.']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$newFileName = uniqid('img_', true) . '.' . $ext;
$targetDiskPath = $uploadDir . $newFileName;
$relativePath = 'uploads/' . $newFileName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetDiskPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save uploaded file.']);
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO images (user_id, title, description, file_path) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isss", $userId, $title, $description, $relativePath);

if (mysqli_stmt_execute($stmt)) {
    $newId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'success',
        'message' => 'Image uploaded successfully!',
        'image' => [
            'id' => $newId,
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'description' => nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')),
            'raw_description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'file_path' => htmlspecialchars($relativePath, ENT_QUOTES, 'UTF-8')
        ]
    ]);
} else {
    @unlink($targetDiskPath);
    mysqli_stmt_close($stmt);
    echo json_encode(['status' => 'error', 'message' => 'Database error while saving image.']);
}
?>