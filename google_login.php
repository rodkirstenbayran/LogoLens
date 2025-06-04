<?php
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('346286691518-1idir0681o5bskm6ih966qhebgscigbr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/logo_lens/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;