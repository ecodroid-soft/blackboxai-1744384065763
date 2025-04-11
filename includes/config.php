<?php
// Database configuration - Using MySQL
define('DB_HOST', '127.0.0.1'); // Using IP instead of localhost
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pofinfraa_db');

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('SLIDER_UPLOAD_PATH', UPLOAD_PATH . 'sliders/');
define('PROJECT_UPLOAD_PATH', UPLOAD_PATH . 'projects/');

// URL paths
define('SITE_NAME', 'POFINFRAA');
define('SITE_URL', 'http://localhost:8000');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('SLIDER_UPLOAD_URL', UPLOAD_URL . 'sliders/');
define('PROJECT_UPLOAD_URL', UPLOAD_URL . 'projects/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create upload directories if they don't exist
$directories = [UPLOAD_PATH, SLIDER_UPLOAD_PATH, PROJECT_UPLOAD_PATH];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}
?>
