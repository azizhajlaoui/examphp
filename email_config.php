<?php
// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'azizhajlaoui2@gmail.com');
define('SMTP_PASSWORD', 'ogpzgwkpwsgzajxv');
define('SMTP_FROM_EMAIL', 'azizhajlaoui2@gmail.com');
define('SMTP_FROM_NAME', 'examphp');

// Enable TLS
define('SMTP_SECURE', 'tls');

// Additional PHP mail settings
ini_set("SMTP", SMTP_HOST);
ini_set("smtp_port", SMTP_PORT);
ini_set("sendmail_from", SMTP_FROM_EMAIL);
ini_set("smtp_ssl", "tls");
?> 