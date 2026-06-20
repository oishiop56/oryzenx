<?php
/**
 * Helper Functions
 * Utility functions for the application
 */

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function formatPrice($price, $currency = 'USD') {
    $symbols = array(
        'USD' => '$',
        'BTC' => '฿',
        'USDT' => 'USDT'
    );
    $symbol = isset($symbols[$currency]) ? $symbols[$currency] : '$';
    return $symbol . number_format($price, 2);
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (is_string($date)) {
        return date($format, strtotime($date));
    }
    return date($format, $date);
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y >= 1) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    if ($diff->m >= 1) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    if ($diff->d >= 1) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
    if ($diff->h >= 1) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    if ($diff->i >= 1) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');

    return 'just now';
}

function getStatusBadge($status) {
    $badges = array(
        'available' => '<span class="badge badge-success">Available</span>',
        'sold' => '<span class="badge badge-danger">Sold</span>',
        'pending' => '<span class="badge badge-warning">Pending</span>',
        'active' => '<span class="badge badge-success">Active</span>',
        'inactive' => '<span class="badge badge-secondary">Inactive</span>',
        'suspended' => '<span class="badge badge-danger">Suspended</span>'
    );
    return isset($badges[$status]) ? $badges[$status] : $status;
}

function getPaymentStatusBadge($status) {
    $badges = array(
        'pending' => '<span class="badge badge-warning">Pending</span>',
        'approved' => '<span class="badge badge-success">Approved</span>',
        'rejected' => '<span class="badge badge-danger">Rejected</span>'
    );
    return isset($badges[$status]) ? $badges[$status] : $status;
}

function generateTransactionId() {
    return 'TXN' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
}

function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">" . "\r\n";

    return mail($to, $subject, $message, $headers);
}

function sendAdminNotification($subject, $message) {
    return sendEmail(SITE_EMAIL, $subject, $message);
}

function createUploadDir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function uploadFile($file, $destination) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return array('success' => false, 'message' => 'No file uploaded');
    }

    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return array('success' => false, 'message' => 'File size too large (max 5MB)');
    }

    $allowed_types = array('image/jpeg', 'image/png', 'image/jpg');
    if (!in_array($file['type'], $allowed_types)) {
        return array('success' => false, 'message' => 'Invalid file type');
    }

    createUploadDir(dirname($destination));

    $filename = uniqid('upload_') . '_' . basename($file['name']);
    $full_path = dirname($destination) . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        return array('success' => true, 'filename' => $filename, 'path' => $full_path);
    }

    return array('success' => false, 'message' => 'File upload failed');
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function requireLogin() {
    global $auth;
    if (!$auth->isLoggedIn()) {
        redirect(SITE_URL . 'public/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireAdmin() {
    global $auth;
    requireLogin();
    if (!$auth->isAdmin()) {
        redirect(SITE_URL . 'public/?error=Access denied');
    }
}

function getSetting($key, $default = '') {
    global $db;
    $setting = $db->fetch('SELECT setting_value FROM settings WHERE setting_key = ?', array($key));
    return $setting ? $setting['setting_value'] : $default;
}

function updateSetting($key, $value) {
    global $db;
    $existing = $db->fetch('SELECT id FROM settings WHERE setting_key = ?', array($key));

    if ($existing) {
        return $db->query('UPDATE settings SET setting_value = ? WHERE setting_key = ?', array($value, $key));
    } else {
        return $db->query('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', array($key, $value));
    }
}

function getPaginationLinks($current_page, $total_pages, $base_url) {
    $links = '';
    
    if ($current_page > 1) {
        $links .= '<a href="' . $base_url . '?page=1" class="pagination-link">First</a>';
        $links .= '<a href="' . $base_url . '?page=' . ($current_page - 1) . '" class="pagination-link">Previous</a>';
    }
    
    for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
        if ($i == $current_page) {
            $links .= '<span class="pagination-link active">' . $i . '</span>';
        } else {
            $links .= '<a href="' . $base_url . '?page=' . $i . '" class="pagination-link">' . $i . '</a>';
        }
    }
    
    if ($current_page < $total_pages) {
        $links .= '<a href="' . $base_url . '?page=' . ($current_page + 1) . '" class="pagination-link">Next</a>';
        $links .= '<a href="' . $base_url . '?page=' . $total_pages . '" class="pagination-link">Last</a>';
    }
    
    return $links;
}
?>
