<?php
<?php
$db_host = "192.168.9.123";
$db_name = "it202407";
$db_user = "batman";
$db_password = "batman";

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("DB-Fehler: " . $e->getMessage());
}

$reha_nr = "123456";
$passwort = "test123";
$password_hash = password_hash($passwort, PASSWORD_DEFAULT);

$sql = "INSERT INTO Teilnehmer (Reha_Nr, Password_Hash) VALUES (:reha_nr, :password_hash)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "reha_nr" => $reha_nr,
    "password_hash" => $password_hash
]);

echo "Test-User angelegt!";
?>