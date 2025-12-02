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
$user_id = isset($_POST["user_id"]) ? (int)$_POST["user_id"] : 0;
$old_password = isset($_POST["old_password"]) ? trim($_POST["old_password"]) : "";
$new_password = isset($_POST["new_password"]) ? trim($_POST["new_password"]) : "";

// Validierung
if (empty($user_id) || empty($old_password) || empty($new_password)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Alle Felder erforderlich"
    ]);
    exit();
}

// Passwort-Stärke prüfen
if (strlen($new_password) < 8) {
    echo json_encode([
        "status" => "failure",
        "message" => "Neues Passwort muss mindestens 8 Zeichen lang sein"
    ]);
    exit();
}

// User + aktuelles Passwort holen
$checkSql = "SELECT Teilnehmer_ID, Password_Hash 
             FROM Teilnehmer 
             WHERE Teilnehmer_ID = :user_id";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["user_id" => $user_id]);
$user = $checkStmt->fetch();

if (!$user) {
    echo json_encode([
        "status" => "failure",
        "message" => "Teilnehmer nicht gefunden"
    ]);
    exit();
}

// Altes Passwort prüfen
if (!password_verify($old_password, $user["Password_Hash"])) {
    echo json_encode([
        "status" => "failure",
        "message" => "Aktuelles Passwort ist falsch"
    ]);
    exit();
}

// Neues Passwort hashen
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// In Datenbank speichern
$updateSql = "UPDATE Teilnehmer 
              SET Password_Hash = :password_hash 
              WHERE Teilnehmer_ID = :user_id";
$updateStmt = $pdo->prepare($updateSql);
$updateStmt->execute([
    "password_hash" => $password_hash,
    "user_id" => $user_id
]);

echo json_encode([
    "status" => "success",
    "message" => "Passwort erfolgreich geändert"
]);

?>