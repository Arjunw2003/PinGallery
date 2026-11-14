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
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($imageId <= 0 || $title === '') {
    echo json_encode(['status' => 'error', 'message' => 'Valid image ID and title are required.']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE images SET title = ?, description = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $imageId, $userId);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) {
    mysqli_stmt_close($stmt);
    echo json_encode([
        'status' => 'success',
        'message' => 'Image details updated!',
        'image' => [
            'id' => $imageId,
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'description' => nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')),
            'raw_description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8')
        ]
    ]);
} else {
    mysqli_stmt_close($stmt);
    echo json_encode(['status' => 'error', 'message' => 'Failed to update image.']);
}