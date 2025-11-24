# Dev-Test Umgebung

Dieser Ordner enthält eine **lokale Test-Datenbank** für unabhängige Entwicklung.

## 🎯 Zweck

Entwicklung und Testing **ohne Abhängigkeit von Gruppe 2**, bis alle Projekte zusammengeführt werden.

## 📦 Inhalt

- `setup_test_db.sql` - Erstellt Test-Datenbank + User-Tabelle
- `test_users.sql` - Befüllt DB mit Test-Usern
- `test_database.php` - Test-Config (überschreibt production config)
- `test_login.php` - Standalone Test-Script

## 🚀 Setup

### 1. Datenbank erstellen (in phpMyAdmin oder MySQL CLI)

```bash
# In XAMPP MySQL Console oder phpMyAdmin SQL-Tab:
```

Dann `setup_test_db.sql` ausführen.

### 2. Test-User eintragen

`test_users.sql` ausführen.

### 3. Testen

```powershell
php test_login.php
```

## ⚠️ Wichtig

**Diese Dateien sind NUR für Entwicklung!**

Wenn Gruppe 2 fertig ist:
1. Diese Test-DB löschen
2. Production Config (`config/database.php`) nutzen
3. `dev-test/` Ordner kann gelöscht werden
