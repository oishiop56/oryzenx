<?php
/**
 * Google OAuth Callback Handler
 */
require_once '../config.php';
require_once '../includes/google-oauth.php';

$google_oauth = new GoogleOAuth();
$result = $google_oauth->handleCallback();

if (!$result['success']) {
    redirect('login.php?error=' . urlencode($result['message']));
}

$login_result = $auth->googleLogin(
    $result['google_id'],
    $result['email'],
    $result['name'],
    $result['picture']
);

if ($login_result['success']) {
    redirect('index.php?success=Google login successful');
} else {
    redirect('login.php?error=' . urlencode($login_result['message']));
}
?>
