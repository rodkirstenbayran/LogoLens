<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'helpers.php';

if (!isset($_SESSION['pending_user_id'])) {
    header('Location: index.php');
    exit;
}

$pdo = getDb();
$stmt = $pdo->prepare("SELECT email, totp_secret FROM users WHERE id=?");
$stmt->execute([$_SESSION['pending_user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

if (empty($user['totp_secret'])) {
    // Just in case!
    $totp = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    $newSecret = $totp->generateSecret();
    $update = $pdo->prepare("UPDATE users SET totp_secret=? WHERE id=?");
    $update->execute([$newSecret, $_SESSION['pending_user_id']]);
    $user['totp_secret'] = $newSecret;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = $_POST['code'];
    $totp = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    if ($totp->checkCode($user['totp_secret'], $code)) {
        $_SESSION['user_id'] = $_SESSION['pending_user_id'];
        unset($_SESSION['pending_user_id']);
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid 2FA code. Please try again.";
    }
}

// Generate QR code URL
$qrUrl = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate(
    $user['email'],
    $user['totp_secret'],
    'LogoLensApp'
);
// ...rest of HTML
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set up Two-Factor Authentication</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 350px; margin: 40px auto; padding: 25px; border: 1px solid #d0d0d0; border-radius: 8px; }
        .error { color: #b00; margin-bottom: 10px; }
        .success { color: #080; }
        label { display: block; margin: 15px 0 6px 0; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 8px 18px; }
        img { display: block; margin: 15px auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Two-Factor Authentication Setup</h2>
        <p>1. Scan this QR code with Google Authenticator or enter this code manually:</p>
        <div style="text-align:center;">
            <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR Code">
        </div>
        <p style="text-align:center;"><b><?= htmlspecialchars($user['totp_secret']) ?></b></p>
        <hr>
        <form method="POST">
            <label for="code">2. Enter the 6-digit code from your Authenticator app:</label>
            <input type="text" id="code" name="code" pattern="\d{6}" maxlength="6" required autocomplete="one-time-code" placeholder="123456">
            <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <button type="submit">Verify &amp; Continue</button>
        </form>
    </div>
</body>
</html>