<?php
require_once 'config.php';

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            );
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            // If MySQL connection fails, fallback to SQLite
            try {
                $sqlite_file = __DIR__ . '/../database/pofinfraa.db';
                if (!file_exists($sqlite_file)) {
                    touch($sqlite_file);
                    chmod($sqlite_file, 0666);
                }
                $this->conn = new PDO("sqlite:" . $sqlite_file);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->initializeSQLiteTables();
            } catch(PDOException $e2) {
                die("Database connection failed: " . $e2->getMessage());
            }
        }
    }

    private function initializeSQLiteTables() {
        // Create admin_users table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create company_info table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS company_info (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            address TEXT NOT NULL,
            about_text TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create projects table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            image_url TEXT NOT NULL,
            category TEXT NOT NULL,
            completion_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create services table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            icon TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create slider_images table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS slider_images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            image_url TEXT NOT NULL,
            active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Insert default admin user if not exists
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $this->conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
        }

        // Insert default company info if not exists
        $stmt = $this->conn->query("SELECT COUNT(*) FROM company_info");
        if ($stmt->fetchColumn() == 0) {
            $stmt = $this->conn->prepare("INSERT INTO company_info (company_name, email, phone, address, about_text) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                'POFINFRAA',
                'info@pofinfraa.com',
                '+91 84597-00000',
                'B-290, Street Number-1, Chattarpur Enclave Phase 2, New Delhi - 110074',
                'POFINFRAA has been at the forefront of infrastructure development, delivering excellence in construction and project management.'
            ]);
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Delete image helper function
function delete_image($filename, $upload_path) {
    $file_path = $upload_path . $filename;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
?>
