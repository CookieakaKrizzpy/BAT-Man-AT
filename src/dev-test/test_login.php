<?php
/**
 * Standalone Login-Test
 * Testet Login-Logik unabhängig von der API
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/test_database.php';
require_once __DIR__ . '/../config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

echo "=== BAT-Man Auth - Login Test ===\n\n";

// Test-Credentials
$testCases = [
    ['username' => 'admin', 'password' => 'admin123', 'expected' => 'success'],
    ['username' => 'teilnehmer1', 'password' => 'teilnehmer123', 'expected' => 'success'],
    ['username' => 'admin', 'password' => 'falsches_passwort', 'expected' => 'fail'],
    ['username' => 'nicht_existent', 'password' => 'test123', 'expected' => 'fail'],
];

foreach ($testCases as $i => $test) {
    echo "Test " . ($i + 1) . ": " . $test['username'] . " / " . $test['password'] . "\n";
    
    try {
        $pdo = getTestDbConnection();
        
        // User suchen
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = :username");
        $stmt->execute(['username' => $test['username']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo "  → User nicht gefunden\n";
            if ($test['expected'] === 'fail') {
                echo "  ✓ Erwartet: FAIL, Ergebnis: FAIL\n";
            } else {
                echo "  ✗ Erwartet: SUCCESS, Ergebnis: FAIL\n";
            }
            echo "\n";
            continue;
        }
        
        // Passwort prüfen
        if (!password_verify($test['password'], $user['PasswordHash'])) {
            echo "  → Passwort falsch\n";
            if ($test['expected'] === 'fail') {
                echo "  ✓ Erwartet: FAIL, Ergebnis: FAIL\n";
            } else {
                echo "  ✗ Erwartet: SUCCESS, Ergebnis: FAIL\n";
            }
            echo "\n";
            continue;
        }
        
        // JWT generieren
        $payload = [
            'UserID' => $user['UserID'],
            'Role' => $user['Role'],
            'exp' => time() + JWT_EXPIRATION_TIME
        ];
        
        $token = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
        
        echo "  → Login erfolgreich!\n";
        echo "  → UserID: " . $user['UserID'] . "\n";
        echo "  → Role: " . $user['Role'] . "\n";
        echo "  → Token: " . substr($token, 0, 50) . "...\n";
        
        // Token dekodieren (Validierung)
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
        echo "  → Token dekodiert - UserID: " . $decoded->UserID . ", Role: " . $decoded->Role . "\n";
        
        if ($test['expected'] === 'success') {
            echo "  ✓ Erwartet: SUCCESS, Ergebnis: SUCCESS\n";
        } else {
            echo "  ✗ Erwartet: FAIL, Ergebnis: SUCCESS\n";
        }
        
    } catch (Exception $e) {
        echo "  ✗ Fehler: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Test abgeschlossen ===\n";
