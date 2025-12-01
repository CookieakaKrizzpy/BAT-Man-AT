<?php

// Email-Versand-Funktion einbinden
require_once "send_mail.php";

// Nachname + Vorname vom Dashboard 
$nachname = isset($_POST["nachname"]) ? trim($_POST["nachname"]) : "";
$vorname = isset($_POST["vorname"]) ? trim($_POST["vorname"]) : "";

if (empty($nachname) || empty($vorname)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Bitte Nachname und Vorname angeben"
    ]);
    exit(); 
}

// SQL Abfrage Tabelle Ausbilder (mit EMail und Geschlecht)
$sql = "SELECT Ausbilder_ID, Nachname, Vorname, EMail, Geschlecht
        FROM Ausbilder
        WHERE LOWER(Nachname) = LOWER(:nachname)
        AND LOWER(Vorname) = LOWER(:vorname)
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    "nachname" => $nachname,
    "vorname" => $vorname
]);

$user = $stmt->fetch();

if ($user) {
    // Ausbilder gefunden -> Token generieren und Email senden
    
    // Sicheren zufälligen Token generieren (32 Zeichen)
    $token = bin2hex(random_bytes(16));
    
    // Ablaufzeit berechnen (5 Minuten ab jetzt)
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    
    // Token und Ablaufzeit in Datenbank speichern
    $updateSql = "UPDATE Ausbilder 
                  SET EinmalToken = :token, Token_Expired = :expiry 
                  WHERE Ausbilder_ID = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        "token" => $token,
        "expiry" => $expiry,
        "id" => $user["Ausbilder_ID"]
    ]);
    
    // Email mit Magic Link versenden
    $emailSent = sendTokenEmail(
        $user["EMail"], 
        $token, 
        $user["Geschlecht"],
        $user["Nachname"]
    );
    
    if ($emailSent) {
        echo json_encode([
            "status" => "success",
            "message" => "Email mit Login-Link wurde versendet. Bitte prüfen Sie Ihr Postfach.",
            "source" => "ausbilder/authenticate.php"  // DEBUG: Zeigt neue Struktur
        ]);
    } else {
        echo json_encode([
            "status" => "failure",
            "message" => "Fehler beim Versenden der Email. Bitte kontaktieren Sie den Administrator."
        ]);
    }
    exit();
    
} else {
    // Ausbilder nicht gefunden
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültige Anmeldedaten, Ausbilder nicht gefunden"
    ]);
    exit();
}
?>
