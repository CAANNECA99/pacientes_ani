<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Verificar autenticación y rol de administrador
if (!isAuthenticated() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Procesar cierre de sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$mensaje = '';

// Procesar eliminación
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        logActivity($conn, $_SESSION['usuario_id'], "Eliminó el usuario ID: $id");
        $mensaje = showAlert("Usuario eliminado exitosamente");
    }
}

// Procesar creación/actualización
if (isset($_POST['guardar'])) {
    $nombre = cleanInput($_POST['nombre']);
    $email = cleanInput($_POST['email']);
    $rol = cleanInput($_POST['rol']);
    
    if (!isValidEmail($email)) {
        $mensaje = showAlert("El email no es válido", "danger");
    } elseif (isset($_POST['id'])) {
        // Actualizar
        $id = $_POST['id'];
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
        if ($stmt->execute()) {
            logActivity($conn, $_SESSION['usuario_id'], "Actualizó el usuario ID: $id");
            $mensaje = showAlert("Usuario actualizado exitosamente");
        }
    } else {
        // Crear nuevo
        if (!isValidPassword($_POST['password'])) {
            $mensaje = showAlert("La contraseña debe tener al menos 8 caracteres, una letra y un número", "danger");
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $email, $password, $rol);
            if ($stmt->execute()) {
                $nuevo_id = $conn->insert_id;
                logActivity($conn, $_SESSION['usuario_id'], "Creó un nuevo usuario ID: $nuevo_id");
                $mensaje = showAlert("Usuario creado exitosamente");
            }
        }
    }
}

// Obtener usuarios
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración de Usuarios - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Panel de Administración de Usuarios</h2>
            <p class="text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
        </div>
        <div class="col text-end">
            <a href="actividad.php" class="btn btn-info me-2">
                <i class="fas fa-history"></i> Registro de Actividad
            </a>
            <a href="?logout" class="btn btn-danger me-2">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModal">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <?php echo $mensaje; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['rol']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info editar-usuario" 
                                        data-id="<?php echo $row['id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                        data-rol="<?php echo htmlspecialchars($row['rol']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="usuarioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="usuario_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3 password-field">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" id="password">
                        <small class="text-muted">La contraseña debe tener al menos 8 caracteres, una letra y un número.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol" id="rol" required>
                            <option value="admin">Administrador</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar edición de usuario
    document.querySelectorAll('.editar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
            document.getElementById('usuario_id').value = this.dataset.id;
            document.getElementById('nombre').value = this.dataset.nombre;
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('rol').value = this.dataset.rol;
            document.querySelector('.password-field').style.display = 'none';
            modal.show();
        });
    });

    // Resetear modal al cerrar
    document.getElementById('usuarioModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('usuario_id').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('rol').value = 'usuario';
        document.querySelector('.password-field').style.display = 'block';
    });
});
</script>
</body>
</html> 
