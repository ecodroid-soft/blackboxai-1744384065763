<?php
require_once 'includes/config.php';

try {
    // Create connection without database selected
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);

    // Read and execute the SQL schema
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $pdo->exec($sql);

    echo "Database initialized successfully!\n";
    echo "Tables created:\n";
    
    // List created tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table\n";
    }

} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}
