-- =====================================================
-- BAT-Man Auth - Test-Datenbank Setup
-- =====================================================
-- Nur für lokale Entwicklung!
-- Wird später durch Gruppe 2's DB ersetzt.
-- =====================================================

-- Datenbank erstellen (falls nicht vorhanden)
CREATE DATABASE IF NOT EXISTS `batman_auth_test` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `batman_auth_test`;

-- User-Tabelle erstellen
-- Struktur basierend auf Projektplan (Absprache mit Gruppe 2)
CREATE TABLE IF NOT EXISTS `users` (
    `UserID` INT AUTO_INCREMENT PRIMARY KEY,
    `Username` VARCHAR(50) NOT NULL UNIQUE,
    `PasswordHash` VARCHAR(255) NOT NULL,
    `Role` ENUM('Teilnehmer', 'Admin') NOT NULL DEFAULT 'Teilnehmer',
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bestätigung
SELECT 'Test-Datenbank erfolgreich erstellt!' AS Status;
