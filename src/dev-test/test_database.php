<?php
/**
 * Test-Datenbank Konfiguration
 * Nur für lokale Entwicklung!
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'batman_auth_test');  // Test-DB statt Production
define('DB_USER', 'root');
define('DB_PASS', '');

function getTestDbConnection() {
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
        die("Test-DB Verbindung fehlgeschlagen: " . $e->getMessage());
    }
}

// Testen ob DB erreichbar ist
try {
    $pdo = getTestDbConnection();
    echo "✓ Test-Datenbank erfolgreich verbunden!\n";
    
    // User zählen
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✓ Anzahl Test-User: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "✗ Fehler: " . $e->getMessage() . "\n";
    echo "\n→ Bitte zuerst setup_test_db.sql und test_users.sql ausführen!\n";
}
