<?php
require_once 'config.php';

function getDb() {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}

// Encrypt and HMAC metadata
function encrypt_metadata($metadata) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt(
        json_encode($metadata),
        'AES-256-CBC',
        ENCRYPTION_KEY,
        0,
        $iv
    );
    $hmac = hash_hmac('sha256', $encrypted, HMAC_KEY);
    return [
        'encrypted' => $encrypted,
        'iv' => bin2hex($iv),
        'hmac' => $hmac,
    ];
}

function decrypt_metadata($encrypted, $iv, $hmac) {
    $calc_hmac = hash_hmac('sha256', $encrypted, HMAC_KEY);
    if (!hash_equals($calc_hmac, $hmac)) return false;
    $decrypted = openssl_decrypt(
        $encrypted,
        'AES-256-CBC',
        ENCRYPTION_KEY,
        0,
        hex2bin($iv)
    );
    return json_decode($decrypted, true);
}

// Generate random base32 secret for TOTP
function generate_totp_secret($length = 16) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $chars[random_int(0, 31)];
    }
    return $secret;
}

// TOTP code (RFC 6238, Google Authenticator)
function get_totp_code($secret, $timeSlice = null) {
    if ($timeSlice === null) {
        $timeSlice = floor(time() / 30);
    }
    $secretkey = base32_decode($secret);
    $time = pack('N*', 0) . pack('N*', $timeSlice);
    $hash = hash_hmac('sha1', $time, $secretkey, true);
    $offset = ord($hash[19]) & 0xf;
    $code = (
        ((ord($hash[$offset + 0]) & 0x7f) << 24 ) |
        ((ord($hash[$offset + 1]) & 0xff) << 16 ) |
        ((ord($hash[$offset + 2]) & 0xff) << 8 ) |
        (ord($hash[$offset + 3]) & 0xff)
    ) % 1000000;
    return str_pad($code, 6, '0', STR_PAD_LEFT);
}

function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper($b32);
    $b32 = str_replace('=', '', $b32);
    $binaryString = '';
    for ($i = 0; $i < strlen($b32); $i++) {
        $currentChar = strpos($alphabet, $b32[$i]);
        $binaryString .= str_pad(decbin($currentChar), 5, '0', STR_PAD_LEFT);
    }
    $eightBits = str_split($binaryString, 8);
    $decoded = '';
    foreach ($eightBits as $bits) {
        if (strlen($bits) == 8) {
            $decoded .= chr(bindec($bits));
        }
    }
    return $decoded;
}
?>