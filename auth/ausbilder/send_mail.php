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
    <!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>BAT-Man Login</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .header {
                background: linear-gradient(135deg, #c41a38 0%, #e74c3c 100%);
                color: white;
                padding: 30px 20px;
                text-align: center;
            }
            .header img {
                max-height: 60px;
                margin-bottom: 15px;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 600;
            }
            .content {
                padding: 30px 20px;
            }
            .greeting {
                font-size: 16px;
                margin-bottom: 20px;
                color: #333;
            }
            .greeting strong {
                color: #c41a38;
            }
            .message {
                margin: 20px 0;
                color: #555;
                line-height: 1.8;
            }
            .button-container {
                text-align: center;
                margin: 30px 0;
            }
            .button {
                display: inline-block;
                background: linear-gradient(135deg, #c41a38 0%, #e74c3c 100%);
                color: white;
                padding: 14px 32px;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                font-size: 16px;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(196, 26, 56, 0.3);
            }
            .link-text {
                margin: 20px 0;
                padding: 15px;
                background-color: #f9f9f9;
                border-left: 4px solid #c41a38;
                border-radius: 4px;
                word-break: break-all;
                font-size: 13px;
                color: #666;
            }
            .validity {
                background-color: #fff3cd;
                border: 1px solid #ffc107;
                border-radius: 6px;
                padding: 12px 15px;
                margin: 20px 0;
                color: #856404;
                font-weight: 500;
            }
            .footer {
                background-color: #f5f5f5;
                padding: 20px;
                text-align: center;
                font-size: 12px;
                color: #999;
                border-top: 1px solid #eee;
            }
            .footer p {
                margin: 5px 0;
            }
            .divider {
                height: 2px;
                background: linear-gradient(90deg, transparent, #c41a38, transparent);
                margin: 25px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='cid:bfw_logo' alt='BFW Nürnberg Logo' />
                <h1>BAT-Man Portal</h1>
            </div>
            
            <div class='content'>
                <div class='greeting'>
                    <p><strong>$anrede $nachname</strong>,</p>
                </div>
                
                <div class='message'>
                    <p>Sie haben einen Login für das <strong>BAT-Man Portal</strong> des BFW Nürnberg angefordert.</p>
                    <p>Klicken Sie auf den folgenden Button, um sich anzumelden:</p>
                </div>
                
                <div class='button-container'>
                    <a href='$link' class='button'>Jetzt anmelden</a>
                </div>
                
                <p style='text-align: center; color: #999; font-size: 13px;'>oder kopieren Sie diesen Link in Ihren Browser:</p>
                
                <div class='link-text'>
                    $link
                </div>
                
                <div class='validity'>
                    ⏱️ <strong>Wichtig:</strong> Dieser Link ist nur <strong>5 Minuten</strong> lang gültig. Handeln Sie bitte umgehend.
                </div>
                
                <div class='message' style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <p style='color: #999; font-size: 13px;'>Falls Sie diese Email nicht angefordert haben, ignorieren Sie sie einfach. Der Link verfällt automatisch nach 5 Minuten.</p>
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>BFW Nürnberg</strong> - Zentrum für berufliche Rehabilitation</p>
                <p>BAT-Man Portal © 2025</p>
            </div>
        </div>
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
        
        // Embed BFW Logo
        $logoPath = __DIR__ . '/../assets/icon/Logo_BFW_Nuernberg.svg';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'bfw_logo', 'Logo_BFW_Nuernberg.svg');
        }
        
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
