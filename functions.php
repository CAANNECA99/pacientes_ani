<?php
require_once 'config.php';

/**
 * Función para limpiar datos de entrada
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Función para verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Función para verificar si el usuario es administrador
 */
function isAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Función para registrar actividad del usuario
 */
function logActivity($conn, $usuario_id, $accion) {
    $fecha = date('Y-m-d H:i:s');
    $sql = "INSERT INTO actividad_log (usuario_id, accion, fecha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $usuario_id, $accion, $fecha);
    $stmt->execute();
}

/**
 * Función para mostrar mensajes de alerta
 */
function showAlert($mensaje, $tipo = 'success') {
    return "<div class='alert alert-{$tipo} alert-dismissible fade show' role='alert'>
                {$mensaje}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
}

/**
 * Función para validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Función para validar contraseña
 */
function isValidPassword($password) {
    // Mínimo 8 caracteres, al menos una letra y un número
    return strlen($password) >= 8 && 
           preg_match('/[A-Za-z]/', $password) && 
           preg_match('/[0-9]/', $password);
} 
