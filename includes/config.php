<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pofinfraa_db');

// Site configuration
define('SITE_NAME', 'POFINFRAA');
define('SITE_URL', 'http://localhost:8000');
define('UPLOAD_PATH', '../uploads/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
