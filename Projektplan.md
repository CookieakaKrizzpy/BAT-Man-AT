# Projektarbeit: BAT-Man
## Authentifizierungs-Dienst (Standalone)

Wir stellen einen zentralen, "standalone" Authentifizierungs-Dienst bereit. Unsere API prüft die Identität von vorhandenen Mitgliedern und stellt eine digitale Identität (ein JWT-Token) aus. Dieses Token enthält die UserID und Role, die Gruppe 1 (GUI) und Gruppe 3 (Dashboard) zur Durchsetzung ihrer spezifischen Anwendungslogik verwenden.

## 1: API-Spezifikation & Abstimmung

### 1.1. Abstimmung (Analyse)
•	Mit Gruppe 2 (SQL) sprechen: Wir klären die exakte User-Tabellenstruktur (wir brauchen Lesezugriff auf UserID, Username, PasswordHash, Role).
•	Abstimmung zur Datenbank-Befüllung:
o	Da Gruppe 2 die "vorhandenen Mitglieder" einträgt, liefern wir ihnen die Passwort-Hashes.
o	Aktion: Wir erstellen ein PHP-Hilfsskript (generate_hash.php), das password_hash('admin123', PASSWORD_DEFAULT) ausführt, und geben die Hashes an Gruppe 2 weiter.
•	Mit Gruppe 1 (GUI) & 3 (Dashboard) sprechen: Wir kommunizieren den Plan: "Wir nutzen JWTs (Tokens). Ihr bekommt beim Login einmalig einen Token-String. Ihr müsst diesen String speichern und bei Anfragen mitschicken. Logout bedeutet einfach, dass ihr diesen String löscht. Ist das umsetzbar?"

### 1.2. Spezifikation festlegen (Design)
•	Technologie-Stack: PHP.
•	Server-Umgebung: XAMPP.
•	Architektur: Standalone-Dienst.
•	Authentifizierungsmethode: Token-basiert (JWT). (Wir akzeptieren, dass wir hierfür Composer und eine Bibliothek benötigen – das ist die minimale Komplexität, die wir brauchen, damit es für C# und PHP funktioniert).

#### 1.2.1. JWT-Payload-Spezifikation
Der Token-Inhalt wird auf das absolute Minimum reduziert:

| Claim (Key) | Beispiel-Wert | Erklärung & Wichtigkeit |
|-------------|---------------|-------------------------|
| UserID      | "42"          | (Kritisch) Die ID des Benutzers. Wird von Gruppe 1 (GUI) für WHERE-Bedingungen genutzt. |
| Role        | "Teilnehmer"  | (Kritisch) Die Rolle ("Teilnehmer" oder "Admin"). Wird von Gruppe 1 & 3 zur Zugriffssteuerung genutzt. |
| exp         | (time() + 600)| (Standard) Unix-Zeitstempel, wann das Token abläuft (z.B. in 10 Minuten). |

### 1.3. Endpunkt-Definition (Die API-Routen) 
Wir stellen nur EINEN Endpunkt bereit. 

#### Endpunkt 1: Login
•	Aktion: Authentifiziert einen Benutzer und stellt ein Token aus.
•	HTTP-Methode: POST
•	Route: (z.B.) /api/auth/login.php
•	Input (Request Body): { "username": "string", "password": "string" }
•	Output (Success Response - 200 OK): (Wir geben das Token direkt zurück)
```json
{ "token": "ey...[das_JWT_token]...", "expires": "..." }
```
•	Output (Failure Response - 401 Unauthorized):
```json
{ "message": "Login fehlgeschlagen." }
```

### 1.4. Klärung des Logout-Prozesses 
•	Es gibt KEINEN Server-seitigen Logout-Endpunkt.
•	Definition: "Logout" ist ein rein Client-seitiger Prozess.
•	Aufgabe für Gruppe 1 & 3: Wenn der Benutzer auf "Logout" klickt, müssen sie lediglich das von uns erhaltene JWT-Token aus ihrem lokalen Speicher löschen (z.B. aus localStorage oder einer C#-Variablen) und den Benutzer zur Login-Seite umleiten.
•	Vorteil: Dies ist extrem einfach für alle Teams und vermeidet jegliche Komplexität (wie "Blacklists").

## 2: Das Grundgerüst (Backend-Entwicklung)
•	Projekt aufsetzen: Wir richten XAMPP ein und erstellen unseren Projektordner.
•	Abhängigkeiten verwalten (Composer):
o	Wir akzeptieren diesen einen Komplexitätsschritt: Wir installieren Composer und führen composer require firebase/php-jwt aus. Das ist notwendig.
•	Datenbankanbindung (PDO): Wir implementieren den MySQL-Zugriff via PDO (in Absprache mit Gruppe 2).
•	Sicherheit implementieren (Unser Kern-Job):
o	Token-Generierung (JWT): In login.php, nach erfolgreicher password_verify()-Prüfung, nutzen wir die firebase/php-jwt-Bibliothek, um das Token mit den minimalen Claims (UserID, Role, exp) zu erstellen.
o	Passwort-Hashing (Prüfung): Wir nutzen die eingebaute PHP-Funktion password_verify().

## 3: Der interne Test (Unit- & API-Tests)
•	Vorbereitung: Sicherstellen, dass Gruppe 2 die DB mit unseren Hashes befüllt hat.
•	API testen (Postman):
o	Test 1 (Login): Sende POST an login.php. Prüfen: Kommt ein Token-String zurück?
o	Test 2 (Login-Fehler): Sende POST mit falschem Passwort. Prüfen: Kommt der 401-Fehler?
o	Test 3 (JWT-Inhalt): Kopiere das Token auf jwt.io und prüfe den "Payload": Sind UserID, Role und exp korrekt?

## 4: Die Integration (Anbindung der Clients)
•	CORS konfigurieren: Wir fügen die PHP header()-Befehle am Anfang unserer login.php ein, damit Gruppe 3 (Dashboard/Web) darauf zugreifen kann.
•	Beispiele liefern (Anleitungen für die Clients):
o	Für Gruppe 1 (GUI / C#): Ein C#-Beispiel, das zeigt, wie man:
1.	Einen HttpClient-Request an login.php sendet.
2.	Den zurückgegebenen Token-String speichert.
3.	Diesen String bei zukünftigen Anfragen (z.B. an die Datenbank-API von Gruppe 1) in den Authorization: Bearer ...-Header packt.
4.	(Optional) Eine Bibliothek wie Jwt.Net nutzt, um lokal das Ablaufdatum (exp) zu prüfen.
o	Für Gruppe 3 (Dashboard / PHP): Ein JavaScript-Beispiel, das zeigt, wie man:
1.	Einen fetch-Request an login.php sendet.
2.	Den Token-String im localStorage speichert.
3.	Diesen String bei zukünftigen Anfragen (z.B. an die Dashboard-API von Gruppe 3) mitschickt.
4.	(Optional) Eine Bibliothek wie jwt-decode nutzt, um lokal die Role und das exp-Datum zu prüfen.

Dieser Plan nutzt die korrekte Technologie (JWT), damit alle Teams (C# und PHP) einfach damit arbeiten können.

---

## Was wir bereitstellen

Wir Gruppe (Auth) stellen einen einzigen API-Endpunkt bereit:
•	Endpunkt: POST https://eures.europa.eu/index_en/api/auth/login.php
•	Input (JSON): { "username": "string", "password": "string" }
•	Output bei Erfolg (JSON): { "token": "[ein_langer_jwt_token_string]" }
•	Output bei Fehler (JSON): { "message": "Login fehlgeschlagen." }

Der zurückgegebene token (ein JWT) wird folgende, minimal notwendige Informationen ("Claims") enthalten, die von den Clients gelesen werden müssen:
1.	UserID: Die ID des Benutzers aus der Datenbank.
2.	Role: Die Rolle (z.B. "Teilnehmer" oder "Admin/Lehrgangsleitung").
3.	exp: Ein Ablaufdatum (z.B. in 10 Minuten).

### Definition des "Logout"-Prozesses
Um den Dienst schlank zu halten, gibt es keinen logout.php-Endpunkt.
•	Logout ist ein rein Client-seitiger Prozess.
•	Gruppe (GUI) und Gruppe (Dashboard) sind dafür verantwortlich, eine "Logout"-Funktion zu implementieren, die das JWT-Token einfach aus ihrem lokalen Speicher löscht.

---

## Erwartungen & Aufgaben

Basierend auf diesem Plan ergeben sich folgende klare Verantwortlichkeiten für jedes Team:

### Unsere Aufgabe (Gruppe 4 - Auth):
1.	Wir bauen und hosten den login.php-Endpunkt.
2.	Wir prüfen die Passwörter sicher (mit password_verify()).
3.	Wir generieren und signieren das JWT-Token mit den Claims (UserID, Role, exp).

### Aufgabe für Gruppe 1 (GUI / C#):
1.	Ihr ruft unseren login.php-Endpunkt auf und speichert den token-String.
2.	Ihr implementiert die "Logout"-Funktion (Token löschen).
3.	Kritisch: Ihr müsst das Token nach dem Login dekodieren und den Zugriff verweigern, wenn die Role nicht "Teilnehmer" ist.
4.	Kritisch: Ihr müsst die UserID aus dem Token lesen und bei allen Datenbank-Aktionen (Erstellen/Löschen/Bearbeiten von Aktivitäten) als WHERE-Bedingung nutzen, um sicherzustellen, dass Teilnehmer nur ihre eigenen Daten bearbeiten.

### Aufgabe für Gruppe 3 (Dashboard / PHP):
1.	Ihr ruft unseren login.php-Endpunkt auf (z.B. mit fetch und credentials: 'include') und speichert den token-String (z.B. im localStorage).
2.	Ihr implementiert die "Logout"-Funktion (Token aus localStorage löschen).
3.	Kritisch: Ihr müsst das Token nach dem Login dekodieren (z.B. mit jwt-decode) und den Zugriff auf das Dashboard verweigern, wenn die Role nicht "Admin" ist.

### Aufgabe für Gruppe 2 (SQL):
1.	Ihr erstellt die User-Tabelle (mit UserID, Username, PasswordHash, Role).
2.	Da es keine Registrierungsfunktion gibt, müsst ihr die "vorhandenen Mitglieder" manuell anlegen.
3.	Kritisch: Wir (Gruppe 4) liefern euch die PasswordHash-Strings (die wir mit password_hash() generieren), die ihr bitte in die Datenbank eintragt. Dies garantiert 100%ige Kompatibilität mit unserem Login-Skript.

---

# Project Work: BAT-Man
## Authentication Service (Standalone)

We provide a central, "standalone" authentication service. Our API verifies the identity of existing members and issues a digital identity (a JWT token). This token contains the UserID and Role, which Group 1 (GUI) and Group 3 (Dashboard) use to enforce their specific application logic.

## 1: API Specification & Coordination

### 1.1. Coordination (Analysis)
•	Talk to Group 2 (SQL): We clarify the exact user table structure (we need read access to UserID, Username, PasswordHash, Role).
•	Coordination for database population:
o	Since Group 2 enters the "existing members," we provide them with the password hashes.
o	Action: We create a PHP helper script (generate_hash.php) that executes password_hash('admin123', PASSWORD_DEFAULT) and pass the hashes to Group 2.
•	Talk to Group 1 (GUI) & 3 (Dashboard): We communicate the plan: "We use JWTs (tokens). You get a token string once during login. You must store this string and send it with requests. Logout simply means you delete this string. Is this feasible?"

### 1.2. Specification Definition (Design)
•	Technology stack: PHP.
•	Server environment: XAMPP.
•	Architecture: Standalone service.
•	Authentication method: Token-based (JWT). (We accept that we need Composer and a library for this – that's the minimal complexity we need for it to work with both C# and PHP).

#### 1.2.1. JWT Payload Specification
The token content is reduced to the absolute minimum:

| Claim (Key) | Example Value | Explanation & Importance |
|-------------|---------------|--------------------------|
| UserID      | "42"          | (Critical) The user's ID. Used by Group 1 (GUI) for WHERE conditions. |
| Role        | "Participant" | (Critical) The role ("Participant" or "Admin"). Used by Group 1 & 3 for access control. |
| exp         | (time() + 600)| (Standard) Unix timestamp when the token expires (e.g., in 10 minutes). |

### 1.3. Endpoint Definition (The API Routes) 
We provide only ONE endpoint. 

#### Endpoint 1: Login
•	Action: Authenticates a user and issues a token.
•	HTTP Method: POST
•	Route: (e.g.) /api/auth/login.php
•	Input (Request Body): { "username": "string", "password": "string" }
•	Output (Success Response - 200 OK): (We return the token directly)
```json
{ "token": "ey...[the_JWT_token]...", "expires": "..." }
```
•	Output (Failure Response - 401 Unauthorized):
```json
{ "message": "Login failed." }
```

### 1.4. Clarification of the Logout Process 
•	There is NO server-side logout endpoint.
•	Definition: "Logout" is a purely client-side process.
•	Task for Group 1 & 3: When the user clicks "Logout," you simply need to delete the JWT token we provided from your local storage (e.g., from localStorage or a C# variable) and redirect the user to the login page.
•	Advantage: This is extremely simple for all teams and avoids any complexity (like "blacklists").

## 2: The Framework (Backend Development)
•	Set up project: We set up XAMPP and create our project folder.
•	Dependency management (Composer):
o	We accept this one complexity step: We install Composer and run composer require firebase/php-jwt. This is necessary.
•	Database connection (PDO): We implement MySQL access via PDO (in coordination with Group 2).
•	Implement security (Our core job):
o	Token generation (JWT): In login.php, after successful password_verify() verification, we use the firebase/php-jwt library to create the token with minimal claims (UserID, Role, exp).
o	Password hashing (verification): We use PHP's built-in password_verify() function.

## 3: Internal Testing (Unit & API Tests)
•	Preparation: Ensure Group 2 has populated the DB with our hashes.
•	Test API (Postman):
o	Test 1 (Login): Send POST to login.php. Check: Does a token string come back?
o	Test 2 (Login error): Send POST with wrong password. Check: Does the 401 error come back?
o	Test 3 (JWT content): Copy the token to jwt.io and check the "Payload": Are UserID, Role, and exp correct?

## 4: Integration (Client Connection)
•	Configure CORS: We add PHP header() commands at the beginning of our login.php so Group 3 (Dashboard/Web) can access it.
•	Provide examples (Instructions for clients):
o	For Group 1 (GUI / C#): A C# example showing how to:
1.	Send an HttpClient request to login.php.
2.	Store the returned token string.
3.	Pack this string in the Authorization: Bearer ... header for future requests (e.g., to Group 1's database API).
4.	(Optional) Use a library like Jwt.Net to locally check the expiration date (exp).
o	For Group 3 (Dashboard / PHP): A JavaScript example showing how to:
1.	Send a fetch request to login.php.
2.	Store the token string in localStorage.
3.	Send this string with future requests (e.g., to Group 3's dashboard API).
4.	(Optional) Use a library like jwt-decode to locally check the Role and exp date.

This plan uses the correct technology (JWT) so all teams (C# and PHP) can easily work with it.

---

## What We Provide

We (Auth Group) provide a single API endpoint:
•	Endpoint: POST https://eures.europa.eu/index_en/api/auth/login.php
•	Input (JSON): { "username": "string", "password": "string" }
•	Output on success (JSON): { "token": "[a_long_jwt_token_string]" }
•	Output on error (JSON): { "message": "Login failed." }

The returned token (a JWT) will contain the following minimal necessary information ("Claims") that must be read by the clients:
1.	UserID: The user's ID from the database.
2.	Role: The role (e.g., "Participant" or "Admin/Course Management").
3.	exp: An expiration date (e.g., in 10 minutes).

### Definition of the "Logout" Process
To keep the service lean, there is no logout.php endpoint.
•	Logout is a purely client-side process.
•	Group (GUI) and Group (Dashboard) are responsible for implementing a "Logout" function that simply deletes the JWT token from their local storage.

---

## Expectations & Tasks

Based on this plan, the following clear responsibilities arise for each team:

### Our Task (Group 4 - Auth):
1.	We build and host the login.php endpoint.
2.	We verify passwords securely (with password_verify()).
3.	We generate and sign the JWT token with the claims (UserID, Role, exp).

### Task for Group 1 (GUI / C#):
1.	You call our login.php endpoint and store the token string.
2.	You implement the "Logout" function (delete token).
3.	Critical: You must decode the token after login and deny access if the Role is not "Participant".
4.	Critical: You must read the UserID from the token and use it as a WHERE condition in all database actions (create/delete/edit activities) to ensure participants can only edit their own data.

### Task for Group 3 (Dashboard / PHP):
1.	You call our login.php endpoint (e.g., with fetch and credentials: 'include') and store the token string (e.g., in localStorage).
2.	You implement the "Logout" function (delete token from localStorage).
3.	Critical: You must decode the token after login (e.g., with jwt-decode) and deny access to the dashboard if the Role is not "Admin".

### Task for Group 2 (SQL):
1.	You create the User table (with UserID, Username, PasswordHash, Role).
2.	Since there is no registration function, you must manually create the "existing members".
3.	Critical: We (Group 4) provide you with the PasswordHash strings (which we generate with password_hash()) that you please enter into the database. This guarantees 100% compatibility with our login script.

````
