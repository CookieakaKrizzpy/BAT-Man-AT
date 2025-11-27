<?php

// Grundeinstellungen, Header?
header ("Content-Type: application/json; charset=UTF-8");                                  // JSON HEADER
header ("Access-Control-Allow-Origin: *");                                                 // CORS HEADER (falls Dashboard extern gehostet wird)

// Datenbankverbindung herstellen
$db_host = "localhost";                                                                    // Datenbank Host
$db_name = "it202407";                                                                     // Datenbank Name
$db_user = "batman";                                                                       // Datenbank Benutzer
$db_password = "batman";                                                                   // Datenbank Passwort

// Verbindung aufbauen (PDO)
try
{
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";                          // Data Source Name sagt PDO welcher Treiber und welche DB genutzt werden soll

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_password, $options);                                // Neue PDO Instanz erstellen
}
catch (PDOException $e)
{
    echo json_encode(
        ["status" => "error", "message" => "Verbindungsfehler: " . $e->getMessage()]);     // Fehlermeldung als JSON ausgeben
}

// Anfragemethode prüfen 
if ($_SERVER["REQUEST_METHOD"] === "POST")                                                 // Nur POST Anfragen erlauben
{
    // Eingabedaten prüfen/bereinigen
    $username = isset($_POST["username"]) ?                                                // Benutzername aus POST Daten holen
        trim($_POST["username"]) : "";
    $input_password = isset($_POST["password"]) ?                                          // Passwort aus POST Daten holen
        trim($_POST["password"]) : "";

    // Prüfen ob Felder leer sind
    if (!empty($username) && !empty($input_password))                                      // Beide Felder müssen ausgefüllt sein
    {
        // User suchen
        $sql = "SELECT Reha_Nr, Password_Hash FROM Teilnehmer WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["username" => $username]);

        $user = $stmt->fetch();                                                            // Benutzer holen

        // Passwort prüfen
        if ($user && password_verify($input_password, $user["password"]))                  // Passwort prüfen
        {
            echo json_encode([                                                             // Erfolgreiche Anmeldung
                "status" => "success",
                "user_id" => $user["id"],
                "username" => $user["username"],
                "message" => "Login erfolgreich"
            ]);
        }
        else
        {
            echo json_encode([                                                             // Fehlgeschlagene Anmeldung
                "status" => "failure",
                "message" => "Ungültiger Benutzername oder Passwort"
            ]);
        }
    }
    else
    {
        echo json_encode([                                                                 // Fehlende Eingabefelder
            "status" => "error",
            "message" => "Bitte alle Felder ausfüllen"
        ]);
    }
}
else {
    echo json_encode(["status" => "error", "message" => "Nur POST erlaubt"]);              // Nur POST Anfragen erlaubt
}
?>
