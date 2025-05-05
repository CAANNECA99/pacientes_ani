<?php
require_once 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'pacientes_ani';

$conn = new mysqli($host, $user, $password, $database);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexion exitosa: " . $conn->connect_error);
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8mb4");

?>
