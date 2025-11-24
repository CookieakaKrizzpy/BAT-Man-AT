# BAT-Man Authentication API Dokumentation

---

## #renew - Session vom 24.11.2025

### 🎯 Was wurde erstellt:

#### 1. **Projekt-Struktur** (Best Practice)
```
src/
├── api/login.php          ← Haupt-Endpunkt (TODO: Implementierung)
├── config/                ← DB, JWT, CORS Config (Templates fertig)
├── tools/                 ← Hash-Generator (fertig)
├── docs/examples/         ← Code-Beispiele für Gruppe 1 & 3 (fertig)
├── tests/postman/         ← API-Tests (vorbereitet)
└── dev-test/              ← Unabhängige Test-Umgebung (komplett)
```

**✅ Sicherheit:** Config & Tools durch `.htaccess` geschützt  
**✅ Unabhängigkeit:** Eigene Test-DB → kein Warten auf Gruppe 2

#### 2. **Dependencies & Setup**
- ✅ Composer installiert (lokal im Projekt)
- ✅ `firebase/php-jwt` (v6.11.1) installiert
- ✅ Autoloader generiert

#### 3. **Fertige Code-Beispiele für andere Teams**
- **C# (Gruppe 1):** `docs/examples/csharp_example.cs`
  - Login-Request mit HttpClient
  - Token-Speicherung
  - Authorization-Header für weitere Requests
  
- **JavaScript (Gruppe 3):** `docs/examples/javascript_example.js`
  - fetch() API-Calls
  - localStorage Token-Management
  - JWT-Dekodierung (Client-seitig)

#### 4. **Test-Umgebung (dev-test/)**
- ✅ SQL-Scripts für Test-Datenbank
- ✅ Test-User mit echten Hashes
- ✅ Standalone Test-Script (`test_login.php`)
- ✅ Komplett unabhängig von Gruppe 2

#### 5. **Dokumentation**
- ✅ API-Spezifikation
- ✅ Setup-Anleitungen
- ✅ Postman Test-Collection

---

### 📊 Aktueller Status: **~40% abgeschlossen**

**Was IHR noch machen müsst (Lernkurve!):**

#### ⏳ Core-Implementierung (Das Wichtigste!)
1. **`api/login.php` programmieren:**
   ```php
   // TODO 1: JSON Input lesen
   // TODO 2: DB-Verbindung herstellen  
   // TODO 3: User abfragen
   // TODO 4: Passwort prüfen (password_verify)
   // TODO 5: JWT generieren
   // TODO 6: Response senden
   ```
   **Geschätzter Aufwand:** 2-3 Stunden (mit Debugging)

2. **JWT Secret Key generieren:**
   ```php
   echo bin2hex(random_bytes(32));
   ```
   → In `config/jwt.php` eintragen

3. **Passwort-Hashes generieren & an Gruppe 2 geben:**
   ```bash
   php tools/generate_hash.php
   ```

#### ⏳ Testing & Integration
4. Test-DB aufsetzen (SQL-Scripts ausführen)
5. Login testen (Postman oder `dev-test/test_login.php`)
6. Mit Gruppe 1 & 3 abstimmen (Code-Beispiele zeigen)

---

### 💡 Einschätzung: **Mehr als ein Grundgerüst!**

**Was ihr habt:**
- ✅ **Professionelle Projektstruktur** (Production-Ready)
- ✅ **Alle Hilfsmittel** (Config, Tools, Tests)
- ✅ **Unabhängige Entwicklung** möglich
- ✅ **Best Practices** (Security, Separation of Concerns)
- ✅ **Fertige Integration-Beispiele** für andere Teams

**Was fehlt:**
- ⏳ **Die eigentliche Login-Logik** (~60 Zeilen PHP)
- ⏳ **Konfiguration anpassen** (Secret Key, DB-Credentials)
- ⏳ **Testing**

**Vergleich:**
- ❌ "Nur Grundgerüst" = leere Ordner + package.json
- ✅ **IHR HABT:** Fast Production-Ready Setup + komplette Dev-Umgebung
- 🎯 **Ihr seid bei ~40-45%** - die restlichen 60% sind "nur" die Core-Logik

---

### 🎓 Lernkurve - Was IHR selbst machen solltet:

| Was | Warum selbst machen? | Lerneffekt |
|-----|---------------------|------------|
| `login.php` implementieren | ⭐⭐⭐ Kernlogik! | Hoch |
| JWT Secret generieren | ⭐⭐ Sicherheit verstehen | Mittel |
| DB-Config anpassen | ⭐ Basics | Niedrig |
| Testen & Debuggen | ⭐⭐⭐ Fehlersuche lernen | Hoch |

**Empfehlung:** 
- **Macht selbst:** login.php Schritt für Schritt (mit TODOs als Guide)
- **Nutzt:** Die vorbereiteten Tools & Tests zum Überprüfen

---

### 📝 Diskussionspunkte für euer Team:

1. **Wollt ihr mehrere Ansätze parallel entwickeln?**
   - Ansatz 1: JWT (aktuell)
   - Ansatz 2: Session-based (Backup)
   - Ansatz 3: Minimal (ohne Composer)

2. **Wer übernimmt was?**
   - Person A: login.php implementieren
   - Person B: Testing & Postman
   - Person C: Abstimmung mit Gruppe 2 (Hashes liefern)

3. **Zeitplan:**
   - Bis wann muss login.php fertig sein?
   - Wann kann Gruppe 2 die DB bereitstellen?

---

## Basis-URL
```
POST /api/login.php
```

## Endpunkt: Login

### Request
**Methode:** `POST`  
**Content-Type:** `application/json`

**Body:**
```json
{
  "username": "string",
  "password": "string"
}
```

### Response

#### Erfolg (200 OK)
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires": "2025-11-24T15:30:00Z"
}
```

#### Fehler (401 Unauthorized)
```json
{
  "message": "Login fehlgeschlagen."
}
```

## JWT Token Claims

Der zurückgegebene Token enthält folgende Claims:

| Claim  | Typ    | Beschreibung |
|--------|--------|--------------|
| UserID | string | Benutzer-ID aus der Datenbank |
| Role   | string | "Teilnehmer" oder "Admin" |
| exp    | number | Unix-Timestamp (Ablaufdatum) |

## Logout

Es gibt **keinen** Server-seitigen Logout-Endpunkt.

**Client-seitige Implementation:**
1. Token aus lokalem Speicher löschen
2. Benutzer zur Login-Seite umleiten

## CORS

Die API erlaubt Cross-Origin Requests für:
- Gruppe 1 (GUI / C#)
- Gruppe 3 (Dashboard / PHP)
