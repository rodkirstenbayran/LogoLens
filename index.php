<?php
session_start();
require_once 'helpers.php';
$pdo = getDb();

$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Password change reminder every 30 days
    $last_change = strtotime($user['last_password_change']);
    if (time() - $last_change > 30 * 24 * 3600) {
        $reminder = "Please change your password for security!";
    }
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>LogoLens Project</title>
</head>
<body>
    <?php if ($user): ?>
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
    <?php else: ?>
        <div class= "auth_wrapper">
        <div class="container_log">
            <h2>Welcome to Logolens</h2>
            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="auth.php" method="POST">
                <input type="email" name="email" placeholder="Email" required /><br>
                <input type="password" name="password" placeholder="Password" required /><br>
                <button type="submit" name="login" class="btn_submit">Login</button><br>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <h3>OAuth Login (Placeholder)</h3>
            <p>
                <a href="google_login.php" class="btn_auth">Login with Google</a>
                <a href="#" onclick="alert('OAuth login not implemented in demo.'); return false;" class="btn_auth">Login with Facebook</a>
            </p>
        </div>
        <div class="display_logo">
                <img src="cover/logo_logolens.png" alt="LogoLens Logo" class="logo_img">
        </div>
        </div
    <?php endif; ?>
</body>
</html>