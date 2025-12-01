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

// Token in Teilnehmer-Tabelle suchen
$sql = "SELECT Teilnehmer_ID, Token, Token_Expired
        FROM Teilnehmer
        WHERE Token = :token
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(["token" => $token]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültiger Token"
    ]);
    exit();
}

// Token-Ablaufzeit prüfen
$now = date('Y-m-d H:i:s');
if ($user["Token_Expired"] < $now) {
    echo json_encode([
        "status" => "failure",
        "message" => "Token ist abgelaufen. Bitte melden Sie sich erneut an."
    ]);
    exit();
}

// Token ist gültig
echo json_encode([
    "status" => "success",
    "user_id" => $user["Teilnehmer_ID"],
    "message" => "Token gültig"
]);

?>
