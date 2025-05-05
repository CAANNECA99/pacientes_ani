<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP usa contraseña vacía por defecto
define('DB_NAME', 'pacientes_ani');
define('APP_URL', 'http://localhost/pacientes_ani');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1); 
