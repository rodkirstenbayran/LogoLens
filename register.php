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
<div class="container">
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
        <button type="submit" name="register" class="btn">Register</button>
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
    <a href="index.php" class="btn">Back to Home</a>
    <?php endif; ?>
</div>
</body>
</html>