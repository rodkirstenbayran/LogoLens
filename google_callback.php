<?php
require_once 'vendor/autoload.php';
session_start();
require_once 'helpers.php';

$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('http://localhost/logo_lens/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    header('Location: index.php');
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token['access_token']);

$oauth2 = new Google_Service_Oauth2($client);
$info = $oauth2->userinfo->get();

$email = $info->email;
$name = $info->name;
$oauth_id = $info->id;

$pdo = getDb();
$stmt = $pdo->prepare("SELECT * FROM users WHERE oauth_provider='google' AND oauth_id=?");
$stmt->execute([$oauth_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // User exists, nothing to do
} else {
    $stmt = $pdo->prepare("INSERT INTO users (email, name, oauth_provider, oauth_id, registered_via) VALUES (?, ?, 'google', ?, 'google')");
    $stmt->execute([$email, $name, $oauth_id]);
    $user_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// If 2FA not set up, generate and save TOTP secret
if (empty($user['totp_secret'])) {
    $secret = generate_totp_secret();
    $stmt = $pdo->prepare("UPDATE users SET totp_secret=? WHERE id=?");
    $stmt->execute([$secret, $user['id']]);
    $user['totp_secret'] = $secret; // Keep user object up to date
}

// Set pending session for 2FA (do NOT log in fully yet)
$_SESSION['pending_user_id'] = $user['id'];
// Unset any old user_id session
unset($_SESSION['user_id']);

// Always redirect to 2FA QR/code page
header('Location: show_qr.php');
exit;
?>
