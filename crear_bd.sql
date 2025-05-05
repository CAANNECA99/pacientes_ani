-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS pacientes_ani;
USE pacientes_ani;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar usuario administrador por defecto
-- Contraseña: Cn199976*
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Camilo Negrete', 'negretecamilo20@gmail.com', 'Cn199976*', 'admin');
