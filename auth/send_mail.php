<?php

/**
 * Sendet eine Email mit Magic Link Token an den Ausbilder
 * Nutzt das Ubuntu sendmail System
 * 
 * @param string $to Email-Adresse des Empfängers
 * @param string $token Der generierte Token
 * @param string $geschlecht Anrede (Herr/Frau)
 * @param string $nachname Nachname des Ausbilders
 * @return bool True wenn erfolgreich, False bei Fehler
 */
function sendTokenEmail($to, $token, $geschlecht, $nachname) {
    // TODO: Dashboard-URL eintragen
    $dashboard_url = "PLATZHALTER_DASHBOARD_URL";
    $link = $dashboard_url . "?token=" . urlencode($token);
    
    // Anrede formatieren
    $anrede = ($geschlecht == "Herr") ? "Sehr geehrter Herr" : "Sehr geehrte Frau";
    
    // Email-Betreff
    $subject = "Ihr Login-Link für BAT-Man";
    
    // Email-Inhalt (HTML)
    $message = "
    <html>
    <head>
        <title>BAT-Man Login</title>
    </head>
    <body>
        <h2>$anrede $nachname,</h2>
        <p>Sie haben einen Login für BAT-Man angefordert.</p>
        <p>Klicken Sie auf den folgenden Link, um sich anzumelden:</p>
        <p><a href='$link' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Jetzt anmelden</a></p>
        <p>Oder kopieren Sie diesen Link in Ihren Browser:</p>
        <p>$link</p>
        <p><strong>Dieser Link ist 5 Minuten gültig.</strong></p>
        <hr>
        <p style='color: gray; font-size: 12px;'>Falls Sie diese Email nicht angefordert haben, ignorieren Sie sie einfach.</p>
    </body>
    </html>
    ";
    
    // Headers für HTML-Email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@bat-man.de" . "\r\n";
    
    // DEBUG MODUS FÜR LOKALES TESTEN (Windows)
    // Auf dem Linux-Server diese Zeilen auskommentieren und mail() aktivieren
    $debug_file = __DIR__ . "/debug_email.html";
    file_put_contents($debug_file, $message);
    return true; // Simuliert erfolgreichen Versand
    
    // PRODUKTIV-MODUS (auf Linux-Server aktivieren)
    // Kommentiere die obigen 3 Zeilen aus und aktiviere diese Zeile:
    // return mail($to, $subject, $message, $headers);
}

?>
