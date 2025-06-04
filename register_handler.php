<?php
session_start();
require_once 'helpers.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$email || !$password || !$confirm) {
        header("Location: register.php?error=Please+fill+in+all+fields.");
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Invalid+email+address.");
        exit;
    } elseif ($password !== $confirm) {
        header("Location: register.php?error=Passwords+do+not+match.");
        exit;
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: register.php?error=Email+already+registered.");
            exit;
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            if ($stmt->execute([$email, $password_hash])) {
                header("Location: register.php?success=Registration+successful.+Please+login.");
                exit;
            } else {
                header("Location: register.php?error=Registration+failed.+Please+try+again.");
                exit;
            }
        }
    }
} else {
    header("Location: register.php");
    exit;
}