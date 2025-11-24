<?php
/**
 * Database Configuration
 * PDO Connection Setup
 */

// TODO: Anpassen an eure Datenbank (Gruppe 2)
define('DB_HOST', 'localhost');
define('DB_NAME', 'batman_auth');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDbConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // Produktiv: Keine Details ausgeben!
        error_log("Database Connection Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Database connection failed']);
        exit;
    }
}
