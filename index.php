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
    <style>
        body { font-family: Arial; margin: 2em; background: #f8f8ff; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 8px; padding: 2em; box-shadow: 0 4px 16px #ccc; }
        input, textarea { width: 100%; margin-bottom: 1em; padding: 0.5em; }
        .btn { padding: 0.5em 2em; background: #3498db; color: #fff; border: none; border-radius: 4px; }
        .btn:hover { background: #217dbb; }
        .msg { margin: 1em 0; color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>LogoLens: Secure Logo Metadata Platform</h2>
    <?php if ($user): ?>
        <p>Welcome, <?=htmlspecialchars($user['email'])?></p>
        <?php if (isset($reminder)): ?>
            <div class="error"><?= $reminder ?></div>
        <?php endif; ?>
        <a href="upload_logo_form.php" class="btn">Upload Logo Metadata</a>
        <a href="view_logos.php" class="btn">View My Logos</a>
        <a href="change_password.php" class="btn">Change Password</a>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <h3>Login</h3>
        <?php if ($error): ?>
            <p style="color:red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="auth.php" method="POST">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <h3>OAuth Login (Placeholder)</h3>
        <p>
            <a href="google_login.php" class="btn">Login with Google</a>
            <a href="#" onclick="alert('OAuth login not implemented in demo.'); return false;" class="btn">Login with Facebook</a>
        </p>
    <?php endif; ?>
</div>
</body>
</html>