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
$imageId = (int)($_POST['image_id'] ?? 0);

if ($imageId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid image ID.']);
    exit;
}

$sel = mysqli_prepare($conn, "SELECT file_path FROM images WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($sel, "ii", $imageId, $userId);
mysqli_stmt_execute($sel);
$res = mysqli_stmt_get_result($sel);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($sel);

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Image not found.']);
    exit;
}

$del = mysqli_prepare($conn, "DELETE FROM images WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($del, "ii", $imageId, $userId);

if (mysqli_stmt_execute($del)) {
    mysqli_stmt_close($del);
    $diskPath = __DIR__ . '/../' . $row['file_path'];
    if (is_file($diskPath)) {
        @unlink($diskPath);
    }
    echo json_encode(['status' => 'success', 'message' => 'Image deleted.']);
} else {
    mysqli_stmt_close($del);
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete image.']);
}