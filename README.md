# BAT-Man Authentication Backend (AT)

Ein sicheres, modernes Backend-System zur Benutzerauthentifizierung für das BAT-Man-Projekt.
Dieses System verarbeitet Login-Anfragen von verschiedenen Clients (C# Desktop-GUI, Web-Dashboard) und bietet unterschiedliche Authentifizierungsmethoden für **Teilnehmer** und **Ausbilder**.

---

## 🚀 Features

### Dual Authentication System
- **Teilnehmer-Login:** Klassische Authentifizierung mit Reha-Nr + Passwort
- **Ausbilder-Login:** Moderne Magic Link Authentifizierung via Email (passwortlos)

### Sicherheit
- ✅ **Password Hashing:** Bcrypt-Hashes über `password_hash()`
- ✅ **Prepared Statements:** Schutz gegen SQL-Injections
- ✅ **Session-Tokens:** 30-Minuten-Tokens für persistente Logins
- ✅ **Time-Limited Magic Links:** 5-Minuten-Gültigkeit für Ausbilder-Login
- ✅ **Single-Use Tokens:** EinmalTokens werden nach Verwendung gelöscht

### Organisierte Struktur
```
auth/
├── login.php                  # Zentraler Einstiegspunkt
├── ausbilder/                # Ausbilder-spezifische Logik
│   ├── authenticate.php
│   ├── verify_token.php
│   └── send_mail.php
└── teilnehmer/               # Teilnehmer-spezifische Logik
    ├── authenticate.php
    ├── validate_token.php
    └── logout.php
```

---

## 🛠️ Voraussetzungen

- **Webserver:** Apache/Nginx (XAMPP für lokale Entwicklung)
- **PHP:** 7.4 oder höher
- **MySQL/MariaDB:** 5.7 oder höher
- **Sendmail:** Für Email-Versand (nur auf Produktionsserver)

---

## 📦 Installation & Setup

### 1. Datenbank einrichten

Das SQL-Team muss folgende Spalten zu den Tabellen hinzufügen:

#### Ausbilder-Tabelle
```sql
ALTER TABLE Ausbilder
ADD COLUMN EMail VARCHAR(255),
ADD COLUMN EinmalToken VARCHAR(255),
ADD COLUMN Token_Expired DATETIME,
ADD COLUMN Token VARCHAR(255),
ADD COLUMN Geschlecht VARCHAR(10);
```

#### Teilnehmer-Tabelle
```sql
ALTER TABLE Teilnehmer
ADD COLUMN Token VARCHAR(255),
ADD COLUMN Token_Expired DATETIME;

```

### 2. Konfiguration anpassen

In den Dateien anpassen:
- `auth/login.php` (Zeile 7)
- `auth/ausbilder/verify_token.php` (Zeile 7)
- `auth/teilnehmer/validate_token.php` (Zeile 7)
- `auth/teilnehmer/logout.php` (Zeile 7)

```php
$db_host = "Server-IP"; 
$db_name = "it202407";
$db_user = "batman";
$db_password = "batman";
```

### 3. Dashboard-URL konfigurieren

In `auth/ausbilder/send_mail.php` (Zeile 15):
```php
$dashboard_url = "http://your-dashboard-url.com/verify";
```

### 4. Email-Versand aktivieren (Produktionsserver)

In `auth/ausbilder/send_mail.php` (Zeilen 49-57):
- Debug-Modus auskommentieren (Zeilen 51-53)
- Produktiv-Modus aktivieren (Zeile 57)

---

## 📡 API Dokumentation

### Teilnehmer-Login (Passwort-basiert)

#### 1. Login
```http
POST /auth/login.php

Body (x-www-form-urlencoded):
login_type: Teilnehmer
reha_nr: 90104
passwort: GeheimesPasswort

Response (Erfolg):
{
  "status": "success",
  "role": "Teilnehmer",
  "user_id": 90104,
  "token": "64-stelliger-session-token",
  "expires_at": "2025-12-01 12:00:00",
  "message": "Login als Teilnehmer erfolgreich"
}
```

#### 2. Token validieren (Optional)
```http
POST /auth/teilnehmer/validate_token.php

Body:
token: abc123...

Response:
{
  "status": "success",
  "user_id": 90104,
  "message": "Token gültig"
}
```

#### 3. Logout
```http
POST /auth/teilnehmer/logout.php

Body:
token: abc123...

Response:
{
  "status": "success",
  "message": "Logout erfolgreich"
}
```

---

### Ausbilder-Login (Magic Link)

#### 1. Magic Link anfordern
```http
POST /auth/login.php

Body:
login_type: Ausbilder
nachname: Mustermann
vorname: Max

Response:
{
  "status": "success",
  "message": "Email mit Login-Link wurde versendet..."
}
```

#### 2. Token verifizieren
```http
POST /auth/ausbilder/verify_token.php

Body:
token: abc123...

Response:
{
  "status": "success",
  "role": "Ausbilder",
  "user_id": 2,
  "name": "Mustermann Max",
  "token": "langlebiger-session-token",
  "message": "Login erfolgreich"
}
```

---

## 🔐 Sicherheitshinweise

### Passwort-Migration
Falls alte Klartext-Passwörter in der DB sind:
```sql
-- Passwort hashen (einmalig)
UPDATE Teilnehmer 
SET Password_Hash = PASSWORD('neues-passwort') 
WHERE Teilnehmer_ID = 12345;
```

### Hybrid-Modus (Nur für Tests!)
In `auth/teilnehmer/authenticate.php` (Zeilen 51-54) ist aktuell ein **Fallback für Klartext-Passwörter** aktiv:
```php
// TODO: In Produktion entfernen!
else if ($passwort === $user["Password_Hash"]) {
    $passwordValid = true;
}
```

---

## 📁 Ordnerstruktur

```
BAT-Man-AT/
├── auth/
│   ├── login.php              # Zentraler Einstieg
│   ├── ausbilder/
│   │   ├── authenticate.php   # Magic Link generieren
│   │   ├── verify_token.php   # Magic Link verifizieren
│   │   └── send_mail.php      # Email-Versand
│   └── teilnehmer/
│       ├── authenticate.php   # Login mit Passwort
│       ├── validate_token.php # Session-Token prüfen
│       └── logout.php         # Logout
├── restore/                   # Backup alter Dateien
└── README.md                  # Diese Datei
```

---

## 🧪 Testing

### Lokales Testen mit Postman

**Teilnehmer-Login:**
```
POST http://localhost:8000/auth/login.php

Body:
- login_type: Teilnehmer
- reha_nr: 90104
- passwort: test
```

**Ausbilder-Login:**
```
POST http://localhost:8000/auth/login.php

Body:
- login_type: Ausbilder
- nachname: Mustermann
- vorname: Max
```

### Debug-Email prüfen
Lokal wird die Email in eine Datei geschrieben:
```
auth/ausbilder/debug_email.html
```

---

## 📝 Datenbank-Schema

### Ausbilder-Tabelle
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| Ausbilder_ID | INT | Primary Key |
| Nachname | VARCHAR | Nachname |
| Vorname | VARCHAR | Vorname |
| EMail | VARCHAR(255) | Email-Adresse |
| Geschlecht | VARCHAR(10) | "Herr" oder "Frau" |
| EinmalToken | VARCHAR(255) | Magic Link Token (5 Min) |
| Token_Expired | DATETIME | Ablaufzeit EinmalToken |
| Token | VARCHAR(255) | Session-Token (langlebig) |

### Teilnehmer-Tabelle
| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| Teilnehmer_ID | INT | Primary Key (Login-Nr) |
| Password_Hash | VARCHAR(255) | Bcrypt-Hash |
| Token | VARCHAR(255) | Session-Token (30 Min) |
| Token_Expired | DATETIME | Ablaufzeit Session-Token |

---

## 🆘 Troubleshooting

### Problem: "Column not found"
**Lösung:** SQL-Team muss fehlende Spalten hinzufügen (siehe Installation)

### Problem: Email wird nicht versendet
**Lokal (Windows):** Normal - Debug-Modus schreibt in `debug_email.html`  
**Server (Linux):** Sendmail konfigurieren

### Problem: "Token ist abgelaufen"
**Teilnehmer:** Token-Gültigkeit: 30 Minuten  
**Ausbilder (EinmalToken):** 5 Minuten - neuen Link anfordern

### Problem: Spaltennamen passen nicht
**Lösung:** Datenbank-Schema prüfen:
- Ausbilder: `Ausbilder_ID`, `Nachname`, `Vorname`
- Teilnehmer: `Teilnehmer_ID`, `Password_Hash`

---

## 👥 Team-Kontakte

- **AT-Team:** Authentication Backend
- **GUI-Team:** C# Desktop Client
- **Dashboard-Team:** Web-Frontend
- **SQL-Team:** Datenbank-Schema

---

## 📜 Lizenz

Internes BfW-Projekt - Alle Rechte vorbehalten