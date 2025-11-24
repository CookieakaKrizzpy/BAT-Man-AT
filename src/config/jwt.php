<?php
/**
 * JWT Configuration
 * Secret Key & Token Settings
 */

// WICHTIG: Diesen Key in Produktion durch einen sicheren, zufälligen String ersetzen!
// Generierung z.B. mit: bin2hex(random_bytes(32))
define('JWT_SECRET_KEY', 'CHANGE_THIS_IN_PRODUCTION_TO_RANDOM_STRING');

// Token-Gültigkeitsdauer (in Sekunden)
define('JWT_EXPIRATION_TIME', 600); // 10 Minuten

// Issuer (Optional)
define('JWT_ISSUER', 'BAT-Man-Auth-Service');
