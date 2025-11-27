<?php

//Nachname+Vorname vom Dashboard 
$nachname = isset($_POST["nachname"]) ? trim($_POST["nachname"]) : "";
$vorname = isset($_POST["vorname"]) ? trim($_POST["vorname"]) : "";

if (empty($nachname) || empty($vorname)) {
    echo json_encode([
        "status" => "failure",
        "message" => "Bitte Nachname und Vorname angeben"
    ]);
    exit(); 
}

//SQL Abfrage Tabelle Ausbilder
$sql = "SELECT id, Name, Vorname
        FROM Ausbilder
        WHERE LOWER(Name) = LOWER(:nachname)
        AND LOWER(Vorname) = LOWER(:vorname)
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    "nachname" => $nachname,
    "vorname" => $vorname
]);

$user = $stmt->fetch();

if ($user) {+
    echo json_encode([
        "status" => "success",
        "role" => "Ausbilder",
        "user_id" => $user["id"],
        "name" => $user["Name"] . " " . $user["Vorname"],
        "message" => "Login als Ausbilder erfolgreich"
    ]);
    exit();
} else {
    echo json_encode([
        "status" => "failure",
        "message" => "Ungültige Anmeldedaten, Ausbilder nicht gefunden"
    ]);
    exit();
}
?>