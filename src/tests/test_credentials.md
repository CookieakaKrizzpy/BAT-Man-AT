# Test-Credentials

## Test-Benutzer

Nach Absprache mit Gruppe 2 (SQL):

| Username     | Passwort       | Role        |
|--------------|----------------|-------------|
| admin        | admin123       | Admin       |
| teilnehmer1  | teilnehmer123  | Teilnehmer  |
| test         | test123        | Teilnehmer  |

## Verwendung

1. **Hashes generieren:**
   ```bash
   php tools/generate_hash.php
   ```

2. **An Gruppe 2 weitergeben:**
   - Die generierten Hashes kopieren
   - Gruppe 2 trägt sie in die Datenbank ein

3. **Testen:**
   - Postman Collection importieren (`postman/auth_tests.json`)
   - Tests durchführen
   - Token auf jwt.io validieren

## Erwartete JWT Claims

```json
{
  "UserID": "1",
  "Role": "Admin",
  "exp": 1732460400
}
```
