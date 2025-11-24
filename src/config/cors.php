<?php
/**
 * CORS Configuration
 * Ermöglicht Cross-Origin Requests von Gruppe 3 (Dashboard)
 */

// WICHTIG: In Produktion nur spezifische Origins erlauben!
// Beispiel: header('Access-Control-Allow-Origin: https://dashboard.example.com');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Preflight-Request (OPTIONS) abfangen
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
