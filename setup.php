<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Configuración Inicial del Sistema</h2>";

try {
    // 1. Verificar conexión MySQL
    $conn = new mysqli('localhost', 'root', '');
    echo "<p style='color: green;'>✓ Conexión MySQL exitosa</p>";

    // 2. Crear base de datos si no existe
    $conn->query("CREATE DATABASE IF NOT EXISTS pacientes_ani");
    $conn->select_db('pacientes_ani');
    echo "<p style='color: green;'>✓ Base de datos creada/seleccionada</p>";

    // 3. Crear tabla de usuarios si no existe
    $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'usuario') DEFAULT 'usuario',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->query($sql_usuarios);
    echo "<p style='color: green;'>✓ Tabla usuarios creada/verificada</p>";

    // 4. Crear tabla de actividad_log si no existe
    $sql_log = "CREATE TABLE IF NOT EXISTS actividad_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        accion VARCHAR(255) NOT NULL,
        fecha DATETIME NOT NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->query($sql_log);
    echo "<p style='color: green;'>✓ Tabla actividad_log creada/verificada</p>";

    // 5. Verificar si existe el usuario administrador
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        // Crear usuario administrador por defecto
        $admin_password = password_hash('Admin123', PASSWORD_DEFAULT);
        $sql_admin = "INSERT INTO usuarios (nombre, email, password, rol) 
                      VALUES ('Administrador', 'negretecamilo20@gmail.com', ?, 'admin')";
        $stmt = $conn->prepare($sql_admin);
        $stmt->bind_param("s", $admin_password);
        $stmt->execute();
        echo "<p style='color: green;'>✓ Usuario administrador creado</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Usuario administrador ya existe</p>";
    }

    echo "<div style='margin-top: 20px; padding: 10px; background-color: #e9ecef; border-radius: 5px;'>";
    echo "<h3>Credenciales de Administrador:</h3>";
    echo "<p>Email: negretecamilo20@gmail.com<br>Contraseña: Cn199976></p>";
    echo "</div>";

    echo "<div style='margin-top: 20px;'>";
    echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?> 
