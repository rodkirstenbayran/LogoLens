<?php
session_start();
require_once 'helpers.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$pdo = getDb();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['new_password'];
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password=?, last_password_change=NOW() WHERE id=?");
    $stmt->execute([$hash, $_SESSION['user_id']]);
    $msg = "Password changed!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="index.php">
    <link rel="stylesheet" href="style.css">
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
<div class="container_newpass">
    <h3>Change Password</h3>
    <?php if (isset($msg)): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <form method="POST">
        <input class= "new_password" type="password" name="new_password" placeholder="New Password" required>
        <button type="submit" class="btn_submit">Change</button>
    </form>
</div>
</body>
</html>