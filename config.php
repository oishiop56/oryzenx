<?php
/**
 * Domain Marketplace Platform - Configuration
 * Core PHP + MySQL Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'oryzenx_marketplace');

// Site Configuration
define('SITE_URL', 'http://localhost/oryzenx/');
define('SITE_NAME', 'OryZenX Domain Marketplace');
define('SITE_EMAIL', 'support@oryzenx.com');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', array('cost' => 12));

// Payment Configuration
define('BTC_WALLET', 'TLKZgeHU45vMuZcHeEHQ95GZQ2UhB3cfxV');
define('USDT_WALLET', '0x79395cbf73a98c48bfa53480d16cd5b428b5aff9');

// Google OAuth
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', SITE_URL . 'auth/google-callback.php');

// Upload Paths
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/oryzenx/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');
define('SCREENSHOT_DIR', UPLOAD_DIR . 'screenshots/');
define('SCREENSHOT_URL', UPLOAD_URL . 'screenshots/');

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Timezone
date_default_timezone_set('UTC');

// Start Session
if (!isset($_SESSION)) {
    session_start();
}

// Include required files
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
?>
