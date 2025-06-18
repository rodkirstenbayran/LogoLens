<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - LogoLens</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth_wrapper">    
<div class="container_log">
    <h2>Register</h2>
    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
        <a href="index.php" class="btn">Go to Login</a>
    <?php else: ?>
    <form method="post" action="register_handler.php">
        <input type="email" name="email" placeholder="Email" required /><br>
        <input type="password" name="password" placeholder="Password" required /><br>
        <input type="password" name="confirm" placeholder="Confirm Password" required /><br>
        <button type="submit" name="register" class="btn_submit">Register</button>
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
    <?php endif; ?>
</div>
<div class="display_logo">
    <img src="cover/logo_logolens.png" alt="LogoLens Logo" class="logo_img">
</div>
</div>
</body>
</html>