<?php

// Einstellungen & Datenbank-Verbindung
$db_host = "localhost";
$db_name = "it202407";
$db_user = "batman";
$db_password = "batman";

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";                                   // Der DSN (Data Source Name) definiert Typ, Host und Datenbankname.
    
    $pdo = new PDO($dsn, $db_user, $db_password, [                                                  // ERRMODE_EXCEPTION: Damit bei Fehlern (z.B. falscher Tabellenname) eine Meldung kommt
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

} catch (PDOException $e) {
    die("FEHLER: Konnte keine Verbindung zur Datenbank herstellen. <br>" . $e->getMessage());       // Falls Verbindung nicht klappt, Abbruch
}

echo "<h1>Starte Passwort-Migration...</h1>";                                                       // Kurze Ausgabe für den Benutzer im Browser
echo "<p>Datenbank verbunden. Suche nach Benutzern...</p>";

// Alle Benutzer laden
$sql = "SELECT id, username, password FROM users";
$stmt = $pdo->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);                                                         // Alle Benutzer aus der Datenbank holen

foreach ($users as $user) {                                                                         // Schleife durch alle Benutzer
    
    $id = $user['id'];                                                                              // Speichern in Variablen, damit Code lesbarer ist
    $username = $user['username'];
    $current_password = $user['password'];

    $info = password_get_info($current_password);                                                   // Analyse des aktuellen Passworts

    if ($info['algo'] == 0) {
        
        $new_hash = password_hash($current_password, PASSWORD_DEFAULT);                             // Verschlüsseln (Hashing)     

        $updateSql = "UPDATE users SET password = :hash WHERE id = :id";                            // Update in der Datenbank
        $updateStmt = $pdo->prepare($updateSql);
        
        $updateStmt->execute([                                                                      // Asuführung Update/Übergabe der Werte
            'hash' => $new_hash,
            'id' => $id
        ]);

        echo "<div style='color: green; margin-bottom: 5px;'>";                                     // Erfolgsmeldung
        echo "✅ User <b>'$username'</b> (ID: $id): Passwort war Klartext -> Wurde erfolgreich verschlüsselt.";
        echo "</div>";

    } else {
        //Nichts tun (User ist schon sicher)        
        echo "<div style='color: gray; margin-bottom: 5px;'>";
        echo "ℹ️ User <b>'$username'</b> (ID: $id): Ist bereits sicher verschlüsselt. Keine Änderung nötig.";
        echo "</div>";
    }
}

//Abschluss
echo "<hr>";
echo "<h3>Vorgang abgeschlossen!</h3>";
echo "<p style='color: red; font-weight: bold;'>WICHTIG: Bitte lösche die Datei ('setup_passwords.php'), damit niemand sie versehentlich erneut aufruft oder Passwörter überschreibt!</p>";
?>