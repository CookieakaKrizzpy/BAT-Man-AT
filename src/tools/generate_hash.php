<?php
/**
 * Password Hash Generator
 * Hilfsskript für Gruppe 2 (SQL)
 * 
 * Generiert Passwort-Hashes für die Datenbank-Befüllung
 */

// Beispiel-Passwörter
$passwords = [
    'admin123',
    'teilnehmer123',
    'test123'
];

echo "=== BAT-Man Password Hash Generator ===\n\n";
echo "Diese Hashes an Gruppe 2 (SQL) weitergeben:\n\n";

foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Passwort: {$password}\n";
    echo "Hash:     {$hash}\n\n";
}

echo "---\n";
echo "Verwendung in SQL:\n";
echo "INSERT INTO users (username, password_hash, role) VALUES\n";
echo "('admin', '[HASH_HIER_EINFÜGEN]', 'Admin'),\n";
echo "('teilnehmer1', '[HASH_HIER_EINFÜGEN]', 'Teilnehmer');\n";
