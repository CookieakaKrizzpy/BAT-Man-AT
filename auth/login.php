<?php

header ("Content-Type: application/json; charset=UTF-8");                                  // JSON HEADER
header ("Access-Control-Allow-Origin: *");                                                 // CORS HEADER (falls Dashboard extern ge

// Datenbankverbindung herstellen
$db_host = "localhost";                                                                    // Datenbank Host
$db_name = "it202407";                                                                     // Datenbank Name
$db_user = "batman";                                                                       // Datenbank Benutzer
$db_password = "batman";                                                                   // Datenbank Passwort

// Verbindung aufbauen (PDO)
try
{
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";                          // Data Source Name sagt PDO welcher Treiber und welche DB genutzt werden soll

    $options = [
        PDO::ATTR_ERRMODE => PDO:ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO:FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_password, $options);                                // Neue PDO Instanz erstellen
}
catch (PDOException $e)
{
    echo json_encode(
        ["status" => "error", "message" => "Verbindungsfehler: " . $e->getMessage()]);     // Fehlermeldung als JSON ausgeben
}

// Anfragemethode prüfen 
if ($_SERVER["REQUEST_METHOD"] === "POST") {                                              // Nur POST Anfragen erlauben
    echo json_encode(
        ["status" => "error", "message" => "Nur POST Anfragen erlaubt"]);
    exit();
}

$login_type = isset($_POST["login_type"]) ? $_POST["login_type"] : "Teilnehmer"

if ($login_type == "Ausbilder") {
    require "auth_ausbilder.php";
}
else {
    require "auth_teilnehmer.php";
}
?>
