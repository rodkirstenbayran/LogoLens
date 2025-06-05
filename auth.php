<?php
session_start();
require_once 'helpers.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set pending login session until 2FA is complete
        $_SESSION['pending_user_id'] = $user['id'];
        // Redirect to 2FA page
        header('Location: show_qr.php');
        exit;
    } else {
        header("Location: index.php?error=Invalid+login+credentials.");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}