<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Datenbankverbindung
$db_host = "192.168.9.123";
$db_name = "it202407";
$db_user = "batman";
$db_password = "batman";

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_password, $options);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Datenbankverbindung fehlgeschlagen"
    ]);
    exit();
}

// Parameter aus POST holen
$reha_nr = isset($_POST["reha_nr"]) ? trim($_POST["reha_nr"]) : "";
$neues_passwort = isset($_POST["neues_passwort"]) ? trim($_POST["neues_passwort"]) : "";

// Validierung
if (empty($reha_nr) || empty($neues_passwort)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Reha-Nr und neues Passwort erforderlich"
    ]);
    exit();
}

// Reha_Nr als Integer behandeln
if (!is_numeric($reha_nr)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Reha-Nr muss eine Zahl sein"
    ]);
    exit();
}

$reha_nr = (int)$reha_nr;

// Passwort-Stärke prüfen (mindestens 8 Zeichen)
if (strlen($neues_passwort) < 8) {
    echo json_encode([
        "status" => "failure",
        "message" => "Das Passwort muss mindestens 8 Zeichen lang sein"
    ]);
    exit();
}

// Teilnehmer aus DB holen
$sql = "SELECT Teilnehmer_ID 
        FROM Teilnehmer 
        WHERE Teilnehmer_ID = :reha_nr";
$stmt = $pdo->prepare($sql);
$stmt->execute(["reha_nr" => $reha_nr]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([
        "status" => "failure",
        "message" => "Teilnehmer nicht gefunden"
    ]);
    exit();
}

// Neues Passwort hashen
$new_password_hash = password_hash($neues_passwort, PASSWORD_DEFAULT);

// In Datenbank aktualisieren und Erstanmeldung auf 0 setzen
$updateSql = "UPDATE Teilnehmer 
              SET Password_Hash = :password_hash,
                  Erstanmeldung = 0
              WHERE Teilnehmer_ID = :teilnehmer_id";
$updateStmt = $pdo->prepare($updateSql);
$updateStmt->execute([
    "password_hash" => $new_password_hash,
    "teilnehmer_id" => $user["Teilnehmer_ID"]
]);

// Erfolg
echo json_encode([
    "status" => "success",
    "message" => "Passwort erfolgreich geändert"
]);

?>
