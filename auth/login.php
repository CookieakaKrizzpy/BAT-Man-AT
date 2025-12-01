<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Datenbankverbindung herstellen
$db_host = "192.168.9.123";
$db_name = "it202407";
$db_user = "batman";
$db_password = "batman";

// Verbindung aufbauen (PDO)
try
{
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_password, $options);
}
catch (PDOException $e)
{
    echo json_encode(
        ["status" => "error", "message" => "Verbindungsfehler: " . $e->getMessage()]);
    exit();
}

// Anfragemethode prüfen 
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(
        ["status" => "error", "message" => "Nur POST Anfragen erlaubt"]);
    exit();
}

$login_type = isset($_POST["login_type"]) ? $_POST["login_type"] : "Teilnehmer";

// Einbinden der entsprechenden Authentifizierungslogik basierend auf der Rolle
if ($login_type === "Ausbilder") {
    require "ausbilder/authenticate.php";
} else {
    require "teilnehmer/authenticate.php";
}

?>
