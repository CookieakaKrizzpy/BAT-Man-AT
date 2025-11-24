# BAT-Man-AT (Authentication Team)

Zentraler Authentifizierungs-Dienst für das BAT-Man Projekt. Stellt JWT-basierte Login-Funktionalität für Gruppe 1 (GUI) und Gruppe 3 (Dashboard) bereit.

---

## Was wurde bereits erledigt

### 1. Projekt-Setup und Struktur
- Vollständige Ordnerstruktur nach Best Practices erstellt
- Separation of Concerns: `api/`, `config/`, `tools/`, `docs/`, `tests/`, `dev-test/`
- Security: Config und Tools durch `.htaccess` geschützt
- `.gitignore` konfiguriert (vendor/, logs, etc.)

### 2. Dependencies und Tools
- Composer lokal im Projekt installiert
  - **Warum Composer?** Wir benötigen eine professionelle JWT-Bibliothek (`firebase/php-jwt`), die korrekte Token-Signierung und -Validierung garantiert. JWT manuell zu implementieren wäre fehleranfällig und unsicher. Composer ist der Standard-Paketmanager für PHP und ermöglicht es uns, diese Bibliothek sicher zu installieren und zu verwalten.
  - **Alternativ:** JWT selbst implementieren (nicht empfohlen - Sicherheitsrisiko) oder Sessions statt JWT verwenden (funktioniert nicht gut mit C# in Gruppe 1)
- `firebase/php-jwt` (v6.11.1) installiert und getestet
  - Industry-Standard Bibliothek für JWT in PHP
  - Wird von Google Firebase verwendet
  - Kompatibel mit C# JWT-Bibliotheken (Gruppe 1) und JavaScript jwt-decode (Gruppe 3)
- Autoloader generiert und funktionsfähig

### 3. Konfigurationsdateien (Templates)
- `src/config/database.php` - PDO Datenbankverbindung (muss noch angepasst werden)
- `src/config/jwt.php` - JWT Secret und Ablaufzeit (Secret muss noch generiert werden)
- `src/config/cors.php` - CORS Headers für Cross-Origin Requests (fertig)

### 4. API-Endpunkt
- `src/api/login.php` - Grundgerüst mit TODOs erstellt
- POST-Request Validierung vorhanden
- Struktur für Fehlerbehandlung vorbereitet

### 5. Code-Beispiele für andere Teams
- `src/docs/examples/csharp_example.cs` - Vollständiges Beispiel für Gruppe 1 (GUI/C#)
  - Login mit HttpClient
  - Token-Speicherung
  - Authorization Header
- `src/docs/examples/javascript_example.js` - Vollständiges Beispiel für Gruppe 3 (Dashboard)
  - fetch() API-Calls
  - localStorage Integration
  - JWT-Dekodierung
  - Logout-Implementierung

### 6. Testing-Umgebung
- Postman Collection erstellt (`src/tests/postman/auth_tests.json`)
- Test-Credentials dokumentiert (`src/tests/test_credentials.md`)
- Unabhängige Dev-Test-Umgebung (`src/dev-test/`)
  - SQL-Scripts für Test-Datenbank
  - Standalone Test-Login-Script
  - Test-DB-Connection-Checker

### 7. Tools
- `src/tools/generate_hash.php` - Passwort-Hash-Generator (fertig, muss ausgeführt werden)

### 8. Dokumentation
- `src/README.md` - Technische Dokumentation mit Setup-Anleitung
- `src/docs/api_documentation.md` - API-Spezifikation mit Beispielen
- `Projektplan.md` - Vollständiger Projektplan (vorhanden)

**Aktueller Fortschritt: ca. 40%**

---

## Was noch gemacht werden muss

### TODO 1: JWT Secret Key generieren (5 Minuten)
**Priorität: HOCH**

```bash
# In PowerShell ausführen:
cd src
& "C:\xampp\php\php.exe" -r "echo bin2hex(random_bytes(32));"
```

**Dann:**
1. Den generierten String kopieren
2. In `src/config/jwt.php` öffnen
3. `JWT_SECRET_KEY` durch den generierten String ersetzen
4. Datei speichern

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 2: Passwort-Hashes generieren und an Gruppe 2 liefern (10 Minuten)
**Priorität: HOCH**

```bash
# In PowerShell ausführen:
cd src
& "C:\xampp\php\php.exe" tools/generate_hash.php
```

**Dann:**
1. Ausgabe kopieren (Passwörter + Hashes)
2. An Gruppe 2 (SQL) weitergeben
3. Gruppe 2 soll die Hashes in die User-Tabelle eintragen

**Benötigte Tabellen-Struktur (mit Gruppe 2 abstimmen):**
```sql
CREATE TABLE users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('Teilnehmer', 'Admin') NOT NULL
);
```

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 3: Datenbank-Konfiguration anpassen (5 Minuten)
**Priorität: HOCH**

**Datei:** `src/config/database.php`

**Anpassen:**
1. `DB_HOST` - Server-Adresse (vermutlich 'localhost')
2. `DB_NAME` - Datenbank-Name (mit Gruppe 2 klären)
3. `DB_USER` - Datenbank-User (mit Gruppe 2 klären)
4. `DB_PASS` - Datenbank-Passwort (mit Gruppe 2 klären)

**Abhängigkeit:** Gruppe 2 muss Datenbank bereitstellen

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Warte auf Gruppe 2

---

### TODO 4: Login-Endpunkt implementieren (2-3 Stunden)
**Priorität: KRITISCH - KERNFUNKTIONALITÄT**

**Datei:** `src/api/login.php`

**Zu implementieren:**

1. JSON-Input lesen
```php
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? null;
$password = $input['password'] ?? null;
```

2. Input validieren
```php
if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['message' => 'Username und Passwort erforderlich']);
    exit;
}
```

3. Datenbankverbindung herstellen
```php
require_once __DIR__ . '/../config/database.php';
$pdo = getDbConnection();
```

4. User aus Datenbank laden
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE Username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
```

5. Passwort prüfen
```php
if (!$user || !password_verify($password, $user['PasswordHash'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Login fehlgeschlagen']);
    exit;
}
```

6. JWT generieren
```php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/jwt.php';
use Firebase\JWT\JWT;

$payload = [
    'UserID' => $user['UserID'],
    'Role' => $user['Role'],
    'exp' => time() + JWT_EXPIRATION_TIME
];

$token = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
```

7. Response senden
```php
echo json_encode([
    'token' => $token,
    'expires' => date('c', time() + JWT_EXPIRATION_TIME)
]);
```

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 5: Test-Datenbank aufsetzen (Optional, 15 Minuten)
**Priorität: MITTEL - Für unabhängiges Testen**

Nur notwendig wenn unabhängig von Gruppe 2 getestet werden soll.

**Schritte:**
1. XAMPP starten, phpMyAdmin öffnen
2. SQL-Tab öffnen
3. `src/dev-test/setup_test_db.sql` kopieren und ausführen
4. `src/dev-test/generate_real_hashes.php` ausführen (echte Hashes generieren)
5. `src/dev-test/test_users.sql` mit echten Hashes aktualisieren und ausführen

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 6: Login testen (30 Minuten)
**Priorität: HOCH**

**Option A: Postman (empfohlen)**
1. Postman öffnen
2. `src/tests/postman/auth_tests.json` importieren
3. Tests nacheinander ausführen
4. Erfolgreichen Token auf https://jwt.io validieren

**Option B: Dev-Test-Script**
```bash
cd src
& "C:\xampp\php\php.exe" dev-test/test_login.php
```

**Erwartetes Ergebnis:**
- Test 1 (admin/admin123): Login erfolgreich, Token wird generiert
- Test 2 (falsches Passwort): 401 Unauthorized
- Test 3 (nicht existierender User): 401 Unauthorized

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 7: Integration mit Gruppe 1 & 3 testen (1 Stunde)
**Priorität: HOCH**

**Mit Gruppe 1 (GUI/C#):**
1. Code-Beispiel zeigen: `src/docs/examples/csharp_example.cs`
2. Login-Endpunkt-URL mitteilen
3. Gemeinsam ersten Request testen
4. Token-Dekodierung testen (UserID & Role auslesen)

**Mit Gruppe 3 (Dashboard/PHP):**
1. Code-Beispiel zeigen: `src/docs/examples/javascript_example.js`
2. Login-Endpunkt-URL mitteilen
3. Gemeinsam ersten Request testen
4. CORS-Funktionalität prüfen

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

### TODO 8: Finale Anpassungen (30 Minuten)
**Priorität: NIEDRIG - Vor Produktiv-Einsatz**

1. In `src/config/cors.php`:
   - `Access-Control-Allow-Origin: *` durch spezifische URLs ersetzen
   
2. In `src/api/login.php`:
   - `ini_set('display_errors', 1)` entfernen
   - Error-Reporting für Produktion anpassen

3. In `src/config/jwt.php`:
   - JWT_EXPIRATION_TIME anpassen falls nötig (aktuell 600 Sekunden = 10 Minuten)

**Verantwortlich:** [Name eintragen]  
**Status:** [ ] Nicht begonnen

---

## Projekt-Struktur

```
BAT-Man-AT/
├── README.md                  (diese Datei)
├── Projektplan.md             (vollständiger Plan)
│
├── api/                       (alt, kann gelöscht werden)
│   └── auth/
│       └── login.php
│
└── src/                       (HAUPT-ARBEITSVERZEICHNIS)
    ├── api/
    │   └── login.php          (TODO: implementieren)
    │
    ├── config/
    │   ├── database.php       (TODO: anpassen)
    │   ├── jwt.php            (TODO: Secret generieren)
    │   └── cors.php           (fertig)
    │
    ├── tools/
    │   └── generate_hash.php  (TODO: ausführen)
    │
    ├── docs/
    │   ├── examples/
    │   │   ├── csharp_example.cs
    │   │   └── javascript_example.js
    │   └── api_documentation.md
    │
    ├── tests/
    │   ├── postman/
    │   │   └── auth_tests.json
    │   └── test_credentials.md
    │
    ├── dev-test/              (optional: für unabhängiges Testen)
    │   ├── setup_test_db.sql
    │   ├── test_users.sql
    │   └── test_login.php
    │
    ├── vendor/                (Composer Dependencies)
    ├── composer.json
    └── README.md
```

---

## Schnellstart für Team-Mitglieder

### Entwicklungsumgebung einrichten
1. Repository klonen / pullen
2. XAMPP starten (Apache + MySQL)
3. Im src/ Ordner arbeiten

### Sofort loslegen mit Entwicklung
1. TODO 1-3 abarbeiten (Konfiguration)
2. TODO 4 implementieren (login.php)
3. TODO 6 testen

### Bei Problemen
- `src/README.md` - Technische Details
- `src/docs/api_documentation.md` - API-Spezifikation
- `Projektplan.md` - Gesamtkonzept

---

## Kontakt zu anderen Gruppen

**Gruppe 2 (SQL):**
- Passwort-Hashes liefern (TODO 2)
- User-Tabellenstruktur abstimmen
- DB-Zugangsdaten erhalten

**Gruppe 1 (GUI/C#):**
- Login-Endpunkt-URL mitteilen
- Code-Beispiel zeigen (csharp_example.cs)

**Gruppe 3 (Dashboard/PHP):**
- Login-Endpunkt-URL mitteilen
- Code-Beispiel zeigen (javascript_example.js)
- CORS-Funktionalität testen

---

## Wichtige Befehle

```powershell
# Composer Dependencies installieren (falls vendor/ fehlt)
& "C:\xampp\php\php.exe" composer.phar install

# JWT Secret generieren
& "C:\xampp\php\php.exe" -r "echo bin2hex(random_bytes(32));"

# Passwort-Hashes generieren
& "C:\xampp\php\php.exe" tools/generate_hash.php

# Test-Login ausführen
& "C:\xampp\php\php.exe" dev-test/test_login.php
```
