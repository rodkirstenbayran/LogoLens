<?php
session_start();
require_once 'helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];

    // Handle file upload
    if (!isset($_FILES['source_file']) || $_FILES['source_file']['error'] !== UPLOAD_ERR_OK) {
        die('Error uploading file.');
    }
    $file = $_FILES['source_file'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        die('Invalid file type.');
    }
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        die('File too large.');
    }

    // Create uploads directory if not exists
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $safeFileName = uniqid('logo_') . '.' . $ext;
    $targetPath = $uploadDir . $safeFileName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        die('Failed to move uploaded file.');
    }

    // Store relative path for web access
    $imagePath = 'uploads/' . $safeFileName;

    $metadata = [
        'description' => $description,
        'timestamp' => date('Y-m-d H:i:s'),
        'source' => $imagePath,
    ];
    $enc = encrypt_metadata($metadata);

    $pdo = getDb();
    $stmt = $pdo->prepare("INSERT INTO logos (user_id, encrypted_metadata, metadata_iv, metadata_hmac, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $enc['encrypted'], $enc['iv'], $enc['hmac'], $imagePath]);
    $msg = "Logo metadata and image uploaded securely!";
    header('Location: view_logos.php?msg=' . urlencode($msg));
    exit;
}
?>