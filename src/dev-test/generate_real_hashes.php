<?php
/**
 * Generiert ECHTE Passwort-Hashes
 * Führe dieses Script aus und ersetze die Hashes in test_users.sql
 */

echo "=== Passwort-Hash Generator ===\n\n";

$passwords = [
    'admin123'       => 'Admin',
    'teilnehmer123'  => 'Teilnehmer',
    'test123'        => 'Teilnehmer'
];

foreach ($passwords as $password => $role) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Passwort: {$password} (Role: {$role})\n";
    echo "Hash: {$hash}\n\n";
}

echo "---\n";
echo "Kopiere diese Hashes in test_users.sql!\n";
echo "\nBEISPIEL:\n";
echo "('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'Admin'),\n";
