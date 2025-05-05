<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Prueba de Conexión</h2>";

try {
    require_once 'config.php';
    require_once 'db.php';
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✓ Conexión a la base de datos exitosa</p>";
    
    // Probar la tabla usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total de usuarios en la base de datos: " . $row['total'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Verificar la configuración de PHP
echo "<h3>Información del Sistema</h3>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<p>Servidor Web: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Ruta actual: " . __DIR__ . "</p>"; 
