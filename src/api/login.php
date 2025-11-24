<?php
/**
 * BAT-Man Authentication Service
 * Login Endpoint
 * 
 * POST /api/login.php
 * Body: { "username": "string", "password": "string" }
 */

// CORS Headers einbinden
require_once __DIR__ . '/../config/cors.php';

// Nur POST-Requests erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    exit;
}

// TODO: Implementierung folgt
// 1. JSON Input lesen
// 2. Database Connection (PDO)
// 3. User abfragen
// 4. password_verify()
// 5. JWT generieren
// 6. Response senden

echo json_encode([
    'message' => 'Login endpoint - Implementation pending'
]);
