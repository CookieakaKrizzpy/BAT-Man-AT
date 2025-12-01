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
$new_password = isset($_POST["new_password"]) ? trim($_POST["new_password"]) : "";

// Validierung
if (empty($user_id) || empty($new_password)) {
    echo json_encode([
        "status" => "failure",
        "message" => "User-ID und neues Passwort erforderlich"
    ]);
    exit();
}

// Passwort-Stärke prüfen
if (strlen($new_password) < 8) {
    echo json_encode([
        "status" => "failure",
        "message" => "Passwort muss mindestens 8 Zeichen lang sein"
    ]);
    exit();
}

// Prüfen ob User existiert und Erstanmeldung = 1
$checkSql = "SELECT Teilnehmer_ID, Erstanmeldung 
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

if ($user["Erstanmeldung"] != 1) {
    echo json_encode([
        "status" => "failure",
        "message" => "Passwort-Änderung nur bei Erstanmeldung erlaubt"
    ]);
    exit();
}

// Neues Passwort hashen
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// In Datenbank speichern und Erstanmeldung auf 0 setzen
$updateSql = "UPDATE Teilnehmer 
              SET Password_Hash = :password_hash, 
                  Erstanmeldung = 0 
              WHERE Teilnehmer_ID = :user_id";
$updateStmt = $pdo->prepare($updateSql);
$updateStmt->execute([
    "password_hash" => $password_hash,
    "user_id" => $user_id
]);

// Session-Token generieren (sofortiger Login nach Passwort-Änderung)
$sessionToken = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

$tokenSql = "UPDATE Teilnehmer 
             SET Token = :token, Token_Expired = :expiry 
             WHERE Teilnehmer_ID = :user_id";
$tokenStmt = $pdo->prepare($tokenSql);
$tokenStmt->execute([
    "token" => $sessionToken,
    "expiry" => $expiresAt,
    "user_id" => $user_id
]);

// Erfolg - User ist jetzt eingeloggt
echo json_encode([
    "status" => "success",
    "role" => "Teilnehmer",
    "user_id" => $user_id,
    "token" => $sessionToken,
    "expires_at" => $expiresAt,
    "message" => "Passwort erfolgreich geändert und angemeldet"
]);

?>
