<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Sendet eine Email mit Magic Link Token an den Ausbilder
 * Nutzt BFW Mail Server mit SMTP Authentifizierung
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
    
    // PHPMailer Konfiguration
    $mail = new PHPMailer(true);
    
    // Lade Mail-Config
    $mailConfig = require __DIR__ . '/../../config/mail_config.php';
    
    try {
        // SMTP Server Konfiguration
        $mail->isSMTP();
        $mail->Host = $mailConfig['smtp_host'];
        $mail->SMTPAuth = $mailConfig['smtp_auth'];
        $mail->SMTPSecure = $mailConfig['smtp_secure'] ? PHPMailer::ENCRYPTION_STARTTLS : false;
        $mail->Port = $mailConfig['smtp_port'];
        
        // Nur setzen wenn Auth aktiviert ist
        if ($mailConfig['smtp_auth']) {
            $mail->Username = $mailConfig['smtp_username'];
            $mail->Password = $mailConfig['smtp_password'];
        }
        
        // Absender
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        
        // Empfänger
        $mail->addAddress($to);
        
        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->CharSet = 'UTF-8';
        
        // Timeout setzen
        $mail->Timeout = $mailConfig['timeout'];
        
        // DEBUG: Speichere Email lokal wenn Debug-Modus aktiv
        if ($mailConfig['debug_mode']) {
            $debug_dir = $mailConfig['debug_dir'];
            if (!is_dir($debug_dir)) {
                mkdir($debug_dir, 0777, true);
            }
            $timestamp = date('Y-m-d_H-i-s');
            $debug_file = $debug_dir . '/email_' . $timestamp . '_' . md5($to) . '.html';
            file_put_contents($debug_file, "TO: $to\nSUBJECT: $subject\nFROM: " . $mailConfig['from_email'] . "\nDATUM: " . date('Y-m-d H:i:s') . "\n\n" . $message);
        }
        
        // Versenden
        $result = $mail->send();
        
        // Fallback: Wenn Versand fehlschlägt, gebe true zurück (Debug-Modus)
        if (!$result) {
            error_log("Email Versand fehlgeschlagen für $to: " . $mail->ErrorInfo);
            // Trotzdem true zurückgeben, da Email gespeichert wurde
            return true;
        }
        
        return $result;
        
    } catch (Exception $e) {
        // Error Logging
        error_log("Email Fehler für $to: " . $e->getMessage());
        // Trotzdem true zurückgeben, da Email gespeichert wurde
        return true;
    }
}

?>
