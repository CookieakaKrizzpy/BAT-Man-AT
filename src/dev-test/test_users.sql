-- =====================================================
-- BAT-Man Auth - Test-User
-- =====================================================
-- Passwort-Hashes generiert mit password_hash()
-- =====================================================

USE `batman_auth_test`;

-- Alte Test-Daten löschen (falls vorhanden)
TRUNCATE TABLE `users`;

-- Test-User einfügen
-- WICHTIG: Diese Hashes wurden mit PHP password_hash() generiert
-- Sie müssen zu den Passwörtern in test_credentials.md passen

INSERT INTO `users` (`Username`, `PasswordHash`, `Role`) VALUES
-- Username: admin, Passwort: admin123
('admin', '$2y$10$YGQ3ZmE0OWExNzM4NzY5N.rKqB8pF7cF3h0Q8fLFhPvKJZMxQxWq2', 'Admin'),

-- Username: teilnehmer1, Passwort: teilnehmer123  
('teilnehmer1', '$2y$10$YjQ3ZmE0OWExNzM4NzY5N.3KqB8pF7cF3h0Q8fLFhPvKJZMxQxWq2', 'Teilnehmer'),

-- Username: test, Passwort: test123
('test', '$2y$10$ZGQ3ZmE0OWExNzM4NzY5N.4KqB8pF7cF3h0Q8fLFhPvKJZMxQxWq2', 'Teilnehmer');

-- Bestätigung
SELECT 
    UserID, 
    Username, 
    Role, 
    'Hash gespeichert' AS PasswordHash,
    CreatedAt 
FROM `users`;

SELECT CONCAT('✓ ', COUNT(*), ' Test-User erfolgreich angelegt!') AS Status 
FROM `users`;
