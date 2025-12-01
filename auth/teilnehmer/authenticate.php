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

// Reha_Nr muss numerisch sein
if (!is_numeric($reha_nr)) {
    echo json_encode([
        "status" => "failure",
        "message" => "REHA-Nr. muss eine Zahl sein."
    ]);
    exit();
}

// Als Integer behandeln
$reha_nr = (int)$reha_nr;

$sql = "SELECT Teilnehmer_ID, Password_Hash, Erstanmeldung
        FROM Teilnehmer
        WHERE Teilnehmer_ID = :reha_nr
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(["reha_nr" => $reha_nr]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültige Anmeldedaten, Teilnehmer nicht gefunden"
    ]);
    exit();
}

// Passwort prüfen: Erst Hash, dann Klartext (für Test-Daten)
$passwordValid = false;

// 1. Versuch: Hash-Verifikation (sicher)
if (password_verify($passwort, $user["Password_Hash"])) {
    $passwordValid = true;
}
// 2. Versuch: Klartext-Vergleich (nur für Testsystem!)
// TODO: In Produktion entfernen!
else if ($passwort === $user["Password_Hash"]) {
    $passwordValid = true;
}

if ($passwordValid) {
    
    // Prüfen ob Erstanmeldung (Passwort muss geändert werden)
    if ($user["Erstanmeldung"] == 1) {
        echo json_encode([
            "status" => "password_change_required",
            "user_id" => $user["Teilnehmer_ID"],
            "message" => "Bitte vergeben Sie ein neues Passwort"
        ]);
        exit();
    }
    
    // Normaler Login (Erstanmeldung = 0)
    // Session-Token generieren (30 Minuten gültig)
    $sessionToken = bin2hex(random_bytes(32)); // 64 Zeichen
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    // Token in Datenbank speichern
    $updateSql = "UPDATE Teilnehmer 
                  SET Token = :token, Token_Expired = :expiry 
                  WHERE Teilnehmer_ID = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        "token" => $sessionToken,
        "expiry" => $expiresAt,
        "id" => $user["Teilnehmer_ID"]
    ]);
    
    echo json_encode([
        "status" => "success",
        "role" => "Teilnehmer",
        "user_id" => $user["Teilnehmer_ID"],
        "token" => $sessionToken,
        "expires_at" => $expiresAt,
        "message" => "Login als Teilnehmer erfolgreich",
        "source" => "teilnehmer/authenticate.php"  // DEBUG: Zeigt neue Struktur
    ]);

} else {
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültige Anmeldedaten, Teilnehmer nicht gefunden"
    ]);
}
?>
