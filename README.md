# PHP Login

Ein leichtgewichtiges, sicheres Backend-System zur Benutzerauthentifizierung.
Dieses Projekt dient als zentraler "Türsteher" (API), der Login-Anfragen von verschiedenen Plattformen (z. B. **C# Desktop-Anwendungen** und **PHP Dashboards**) entgegennimmt, validiert und eine einheitliche JSON-Antwort zurückgibt.

## 🚀 Features

* **Zentrale Validierung:** Ein einziges Skript verwaltet den Login für alle Clients.
* **JSON API:** Gibt strukturierte Daten zurück, ideal für C# (`Newtonsoft`/`System.Text.Json`) und JavaScript.
* **Moderne Sicherheit:** Nutzt `password_hash()` (Bcrypt) und Prepared Statements gegen SQL-Injections.
* **Migrations-Helfer:** Enthält ein Skript, um bestehende Klartext-Passwörter automatisch in sichere Hashes umzuwandeln.

## 🛠️ Voraussetzungen

* Webserver (empfohlen: **XAMPP** für lokale Entwicklung)
* PHP 7.4 oder höher
* MySQL / MariaDB

## 📦 Installation & Setup

### 1. Dateien kopieren
Kopiere die Dateien in das öffentliche Verzeichnis deines Webservers (z. B. `C:\xampp\htdocs\login_api\`).

### 2. Datenbank einrichten
Führe den folgenden SQL-Code in deiner Datenbankverwaltung (z. B. **phpMyAdmin**) aus.
⚠️ **Wichtig:** Die Spalte `password` muss `VARCHAR(255)` sein, damit die Hashes nicht abgeschnitten werden.

```sql
-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS meine_login_db;
USE meine_login_db;

-- Tabelle erstellen
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Beispiel-User anlegen (Passwort 'geheim123' im Klartext -> wird später migriert)
INSERT INTO users (username, password) VALUES ('admin', 'geheim123');
```

### 3. Konfiguration anpassen
Öffne die Datei `login.php` (und `setup_passwords.php`) und passe die Datenbank-Zugangsdaten an, falls nötig:

```php
$db_host = 'localhost';
$db_name = 'meine_login_db';
$db_user = 'root';
$db_pass = ''; // Standard bei XAMPP ist leer
```

### 4. Passwörter migrieren (Einmalig)
Um alte Klartext-Passwörter in sichere Hashes umzuwandeln:
1. Öffne im Browser: `http://localhost/login_api/setup_passwords.php`
2. Das Skript verschlüsselt alle offenen Passwörter in der Datenbank.
3. **Sicherheitshinweis:** Lösche die Datei `setup_passwords.php` nach erfolgreicher Ausführung!

---

## 📡 API Dokumentation

### Endpoint
`POST /login.php`

### Request (Anfrage)
Sende die Daten als `x-www-form-urlencoded` oder `Multipart/Form-Data`.

| Parameter  | Typ    | Beschreibung            |
| :--------- | :----- | :---------------------- |
| `username` | String | Der Benutzername        |
| `password` | String | Das Passwort (Klartext) |

### Response (Antwort)
Die API antwortet immer mit einem JSON-Objekt.

#### ✅ Erfolgreicher Login
```json
{
  "status": "success",
  "user_id": 1,
  "username": "admin",
  "message": "Login erfolgreich"
}
```

#### ❌ Fehlgeschlagener Login
```json
{
  "status": "fail",
  "message": "Benutzername oder Passwort ungültig"
}
```

#### ⚠️ Server-Fehler
```json
{
  "status": "error",
  "message": "Datenbank-Verbindung fehlgeschlagen..."
}
```

---

## 💻 Integrations-Beispiele

### C# (HttpClient)
Nutze diesen Code in deiner WPF oder WinForms Anwendung:

```csharp
using System.Net.Http;
using System.Collections.Generic;

// ...

var client = new HttpClient();
var values = new Dictionary<string, string>
{
    { "username", "admin" },
    { "password", "Passwort1" }
};

var content = new FormUrlEncodedContent(values);
var response = await client.PostAsync("http://localhost/auth/login.php", content);
var jsonString = await response.Content.ReadAsStringAsync();

// jsonString jetzt parsen (z.B. mit Newtonsoft.Json) und 'status' prüfen.
```

### PHP Dashboard (cURL)
Wenn dein Dashboard auf einem anderen Server liegt oder du die API intern aufrufst:

```php
$ch = curl_init('http://localhost/login_api/login.php');
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'username' => $_POST['username'],
    'password' => $_POST['password']
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$result = json_decode($response, true);

if ($result['status'] === 'success') {
    $_SESSION['user_id'] = $result['user_id'];
    // Weiterleitung...
} else {
    $error = $result['message'];
}
```