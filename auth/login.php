<?php

// Grundeinstellungen, Header?

header ("Content-Type: application/json; charset=UTF-8");                                  // JSON HEADER
header ("Access-Control_Allow-Origin: *");                                                 // CORS HEADER (falls Dashboard extern gehostet wird)

// Datenbankverbindung herstellen

$db_host = "localhost";                                                                    // Datenbank Host
$db_name = "TEST";                                                                         // Datenbank Name
$db_user = "TEST";                                                                         // Datenbank Benutzer
$db_password = "";                                                                         // Datenbank Passwort

// Verbindung aufbauen (PDO)

try
{
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";                          // Data Source Name sagt PDO welcher Treiber und welche DB genutzt werden soll

    $options = [
        PDO:ATTR_ERRMODE => PDO:ERRMODE_EXCEPTION,
        PDO:ATTR_DEFAULT_FETCH_MODE => PDO:FETCH_ASSOC,
        PDO:ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_password, $options);                                // Neue PDO Instanz erstellen
}
// Anfragemethode prüfen 

// Eingabedaten prüfen/bereinigen

// User suchen 

// Passwort prüfen

