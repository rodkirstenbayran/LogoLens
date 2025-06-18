<?php
session_start();
require_once 'helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$pdo = getDb();
$stmt = $pdo->prepare("SELECT * FROM logos WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Logos</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .logo-img { width: 80px; height: auto; border-radius: 5px; box-shadow: 0 1px 4px #c3d0fc; }
    </style>
</head>
<body>
<div class="navbar">
            <a href="view_logos.php">
                <img src="cover/logo.png" alt="Logo">
            </a>
                <div class="dropdown">
                    <button>User</button>
                    <div class="dropdown-content">
                        <a href="change_password.php">Change Password</a>
                         <a href="logout.php">Logout</a>
                    </div>
                </div>
        </div>
            <a href="upload_logo_form.php">
            <img src="cover/upload.png" alt="Upload" class="upload_btn">
            </a>
<div class="container">
    <h3>My Logo Metadata</h3>
    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Logo Image</th>
            <th>Description</th>
            <th>Timestamp</th>
            <th>Integrity</th>
        </tr>
        <?php foreach ($logos as $logo): 
            $data = decrypt_metadata($logo['encrypted_metadata'], $logo['metadata_iv'], $logo['metadata_hmac']);
        ?>
            <tr>
                <td><?= $logo['id'] ?></td>
                <td>
                    <?php if (!empty($logo['image_path']) && file_exists($logo['image_path'])): ?>
                        <img src="<?= htmlspecialchars($logo['image_path']) ?>" class="logo-img" alt="Logo">
                    <?php else: ?>
                        <span style="color: #aaa;">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= $data ? htmlspecialchars($data['description']) : '<span style="color:red;">Corrupt</span>' ?></td>
                <td><?= $data ? htmlspecialchars($data['timestamp']) : '' ?></td>
                <td><?= $data ? 'OK' : '<span style="color:red;">Tampered</span>' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>