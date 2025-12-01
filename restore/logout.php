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

// Token aus POST holen
$token = isset($_POST["token"]) ? trim($_POST["token"]) : "";

if (empty($token)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Kein Token angegeben"
    ]);
    exit();
}

// Token löschen
$sql = "UPDATE Teilnehmer 
        SET Token = NULL, Token_Expired = NULL 
        WHERE Token = :token";

$stmt = $pdo->prepare($sql);
$stmt->execute(["token" => $token]);

if ($stmt->rowCount() > 0) {
    echo json_encode([
        "status" => "success",
        "message" => "Logout erfolgreich"
    ]);
} else {
    echo json_encode([
        "status" => "failure",
        "message" => "Token nicht gefunden"
    ]);
}

?>
