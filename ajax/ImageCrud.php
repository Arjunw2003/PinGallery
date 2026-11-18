
<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Login Check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

// POST Check
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';


// ================= UPLOAD IMAGE =================
if ($action == 'upload') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title == '' || !isset($_FILES['image'])) {
        echo json_encode(['status' => 'error', 'message' => 'Title and image required']);
        exit;
    }

    $file = $_FILES['image'];

    if ($file['error'] != 0 || $file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image or file size']);
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed) || getimagesize($file['tmp_name']) === false) {
        echo json_encode(['status' => 'error', 'message' => 'Only image files allowed']);
        exit;
    }

    $folder = '../uploads/';

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $fileName = uniqid('img_') . '.' . $ext;
    $path = 'uploads/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $folder . $fileName)) {

        $stmt = mysqli_prepare($conn,
            "INSERT INTO images (user_id, title, description, file_path) VALUES (?, ?, ?, ?)");

        mysqli_stmt_bind_param($stmt, "isss", $userId, $title, $description, $path);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
        } else {
            unlink($folder . $fileName);
            echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    }
}


// ================= DELETE IMAGE =================
elseif ($action == 'delete') {

    $imageId = (int)($_POST['image_id'] ?? 0);

    $stmt = mysqli_prepare($conn,
        "SELECT file_path FROM images WHERE id = ? AND user_id = ?");

    mysqli_stmt_bind_param($stmt, "ii", $imageId, $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        exit;
    }

    $stmt = mysqli_prepare($conn,
        "DELETE FROM images WHERE id = ? AND user_id = ?");

    mysqli_stmt_bind_param($stmt, "ii", $imageId, $userId);

    if (mysqli_stmt_execute($stmt)) {

        $file = '../' . $row['file_path'];

        if (is_file($file)) {
            unlink($file);
        }

        echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully']);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }

    mysqli_stmt_close($stmt);
}


// ================= UPDATE IMAGE =================
elseif ($action == 'update') {

    $imageId = (int)($_POST['image_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($imageId <= 0 || $title == '') {
        echo json_encode(['status' => 'error', 'message' => 'Enter valid title and image ID']);
        exit;
    }

    $stmt = mysqli_prepare($conn,
        "UPDATE images SET title = ?, description = ? WHERE id = ? AND user_id = ?");

    mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $imageId, $userId);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) {
        echo json_encode(['status' => 'success', 'message' => 'Image updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }

    mysqli_stmt_close($stmt);
}

else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
