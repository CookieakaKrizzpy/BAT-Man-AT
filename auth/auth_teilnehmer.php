<?php

$reha_nr = isset($_POST["reha_nr"]) ? trim($_POST["reha_nr"]) : "";
$passwort = isset($_POST["passwort"]) ? trim($_POST["passwort"]) : "";

//Prüfung ob REHA-Nr. und Passwort gesetzt sind
if (empty($reha_nr) || empty($passwort)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Bitte REHA-Nr. und Passwort angeben."
    ]);
    exit();
}
if (!preg_match('/^\d{6}$/', $reha_nr)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Format ungültig."
    ]);
    exit();
}
$sql = "SELECT id, Password_Hash
        FROM Teilnehmer
        WHERE Reha_Nr = :reha_nr
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt -> execute(["reha_nr" => $reha_nr]);
$user = $stmt->fetch();

if ($user && password_verify($passwort, $user["Password_Hash"])) {

    echo json_encode([
        "status" => "success",
        "role" => "Teilnehmer",
        "user_id" => $user["id"],
        "message" => "Login als Teilnehmer erfolgreich"
    ]);

} else {
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültige Anmeldedaten, Teilnehmer nicht gefunden"
    ]);
}
?>