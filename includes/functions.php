<?php
require_once 'config.php';

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/admin/index.php');
        exit();
    }
}

// Upload image
function upload_image($file, $directory = 'uploads') {
    $target_dir = "../" . $directory . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check file type
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
        return array('success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.');
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return array('success' => false, 'message' => 'File is too large. Maximum size is 5MB.');
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'filename' => $new_filename);
    } else {
        return array('success' => false, 'message' => 'Error uploading file.');
    }
}

// Format date
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Truncate text
function truncate_text($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Flash messages
function set_flash_message($type, $message) {
    $_SESSION['flash'] = array(
        'type' => $type,
        'message' => $message
    );
}

function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Generate slug
function generate_slug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text;
}
?>
