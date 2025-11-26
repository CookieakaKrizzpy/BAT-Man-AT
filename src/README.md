# BAT-Man Authentication Service (src/)

Diese Ordnerstruktur enthält den kompletten Authentication Service.

## 📁 Struktur

```
src/
├── api/
│   └── login.php              ← Einziger öffentlicher Endpunkt
│
├── config/
│   ├── database.php           ← DB-Verbindung (PDO)
│   ├── jwt.php                ← JWT-Konfiguration
│   ├── cors.php               ← CORS-Headers
│   └── .htaccess              ← Zugriff verweigert
│
├── tools/
│   ├── generate_hash.php      ← Passwort-Hashes generieren
│   └── .htaccess              ← Zugriff verweigert
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
├── .gitignore
├── composer.json
└── README.md                  ← Diese Datei
```

##  Setup

### 1. Composer installieren
```bash
cd src
composer install
```

### 2. Datenbank konfigurieren
Datei `config/database.php` anpassen:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

### 3. JWT Secret ändern
Datei `config/jwt.php`:
- `JWT_SECRET_KEY` durch zufälligen String ersetzen

### 4. Passwort-Hashes generieren
```bash
php tools/generate_hash.php
```
→ Ausgabe an Gruppe 2 (SQL) weitergeben

### 5. Tests durchführen
- Postman Collection importieren: `tests/postman/auth_tests.json`
- Tests ausführen
- Token auf [jwt.io](https://jwt.io) validieren

## 📖 Verwendung

### Für Gruppe 1 (GUI / C#)
Siehe `docs/examples/csharp_example.cs`

### Für Gruppe 3 (Dashboard / PHP)
Siehe `docs/examples/javascript_example.js`

## 🔒 Sicherheit

- ✅ Config-Ordner durch `.htaccess` geschützt
- ✅ Tools-Ordner durch `.htaccess` geschützt
- ✅ Passwörter mit `password_hash()` / `password_verify()`
- ✅ JWT-Signierung
- ✅ CORS konfiguriert

## ⚠️ Wichtig

**In Produktion:**
1. `JWT_SECRET_KEY` ändern!
2. CORS auf spezifische Origins beschränken
3. HTTPS verwenden
4. Error-Reporting deaktivieren
