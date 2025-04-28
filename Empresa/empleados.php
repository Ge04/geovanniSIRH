<?php
include 'conexion.php';

// Función para obtener todos los empleados
function obtenerEmpleados($pdo) {
    $sql = "SELECT e.*, d.nombre as nombre_departamento 
            FROM Empleados e 
            LEFT JOIN Departamentos d ON e.id_departamento = d.id_departamento 
            ORDER BY e.apellidos, e.nombre";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener departamentos
function obtenerDepartamentos($pdo) {
    $sql = "SELECT * FROM Departamentos ORDER BY nombre";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener empleado por ID
function obtenerEmpleado($pdo, $id) {
    try {
        $sql = "SELECT e.*, d.nombre as nombre_departamento 
                FROM Empleados e 
                LEFT JOIN Departamentos d ON e.id_departamento = d.id_departamento 
                WHERE e.id_empleado = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

// Función para actualizar empleado
function actualizarEmpleado($pdo, $datos) {
    try {
        $sql = "UPDATE Empleados SET 
                nombre = :nombre,
                apellidos = :apellidos,
                email = :email,
                telefono = :telefono,
                fecha_contratacion = :fecha_contratacion,
                id_departamento = :id_departamento
                WHERE id_empleado = :id_empleado";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($datos);
    } catch (PDOException $e) {
        return false;
    }
}

// Función para agregar empleado
function agregarEmpleado($pdo, $datos) {
    try {
        $sql = "INSERT INTO Empleados (nombre, apellidos, email, telefono, fecha_contratacion, id_departamento) 
                VALUES (:nombre, :apellidos, :email, :telefono, :fecha_contratacion, :id_departamento)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Función para eliminar empleado
function eliminarEmpleado($pdo, $id) {
    try {
        $sql = "DELETE FROM Empleados WHERE id_empleado = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $datos = [
                    'nombre' => $_POST['nombre'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'fecha_contratacion' => $_POST['fecha_contratacion'],
                    'id_departamento' => $_POST['id_departamento']
                ];
                if (agregarEmpleado($pdo, $datos)) {
                    $mensaje = "Empleado registrado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al registrar el empleado";
                    $tipo_mensaje = "error";
                }
                break;
            case 'actualizar':
                $datos = [
                    'id_empleado' => $_POST['id_empleado'],
                    'nombre' => $_POST['nombre'],
                    'apellidos' => $_POST['apellidos'],
                    'email' => $_POST['email'],
                    'telefono' => $_POST['telefono'],
                    'fecha_contratacion' => $_POST['fecha_contratacion'],
                    'id_departamento' => $_POST['id_departamento']
                ];
                if (actualizarEmpleado($pdo, $datos)) {
                    $mensaje = "Empleado actualizado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al actualizar el empleado";
                    $tipo_mensaje = "error";
                }
                break;
            case 'eliminar':
                if (eliminarEmpleado($pdo, $_POST['id_empleado'])) {
                    $mensaje = "Empleado eliminado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al eliminar el empleado";
                    $tipo_mensaje = "error";
                }
                break;
        }
    }
}

$empleados = obtenerEmpleados($pdo);
$departamentos = obtenerDepartamentos($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - SIRH</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-container">
        <header class="header">
            <div class="container">
                <nav class="nav-menu">
                    <h1>SIRH - Sistema de Información de Recursos Humanos</h1>
                    <div>
                        <a href="index.php">Inicio</a>
                        <a href="empleados.php">Empleados</a>
                        <a href="nominas.php">Nóminas</a>
                        <a href="permisos.php">Permisos</a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="container">
            <h2>Gestión de Empleados</h2>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="actions-container">
                <button class="button" onclick="document.getElementById('modalEmpleado').style.display='block'">
                    Agregar Empleado
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Departamento</th>
                            <th>Fecha Contratación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($empleado['apellidos'] . ', ' . $empleado['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['email']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['nombre_departamento'] ?? 'Sin departamento'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($empleado['fecha_contratacion'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <button class="button button-small" onclick="editarEmpleado(<?php echo $empleado['id_empleado']; ?>)">
                                        Editar
                                    </button>
                                    <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea eliminar este empleado?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_empleado" value="<?php echo $empleado['id_empleado']; ?>">
                                        <button type="submit" class="button button-small button-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Sistema de Información de Recursos Humanos</p>
            </div>
        </footer>
    </div>

    <div id="modalEmpleado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Empleado</h3>
                <span class="close" onclick="document.getElementById('modalEmpleado').style.display='none'">&times;</span>
            </div>
            <form method="POST" action="" class="form-container" id="formEmpleado">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id_empleado" id="id_empleado">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" name="apellidos" id="apellidos" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" required>
                </div>

                <div class="form-group">
                    <label for="fecha_contratacion">Fecha de Contratación:</label>
                    <input type="date" name="fecha_contratacion" id="fecha_contratacion" required>
                </div>

                <div class="form-group">
                    <label for="id_departamento">Departamento:</label>
                    <select name="id_departamento" id="id_departamento">
                        <option value="">Seleccione un departamento</option>
                        <?php foreach ($departamentos as $departamento): ?>
                            <option value="<?php echo $departamento['id_departamento']; ?>">
                                <?php echo htmlspecialchars($departamento['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">Guardar</button>
                    <button type="button" class="button button-secondary" onclick="document.getElementById('modalEmpleado').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar empleado -->
    <div id="modalEditarEmpleado" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Empleado</h3>
                <span class="close" onclick="document.getElementById('modalEditarEmpleado').style.display='none'">&times;</span>
            </div>
            <form method="POST" action="" class="form-container">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id_empleado" id="edit_id_empleado">
                
                <div class="form-group">
                    <label for="edit_nombre">Nombre:</label>
                    <input type="text" id="edit_nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="edit_apellidos">Apellidos:</label>
                    <input type="text" id="edit_apellidos" name="apellidos" required>
                </div>

                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="edit_telefono">Teléfono:</label>
                    <input type="tel" id="edit_telefono" name="telefono" required>
                </div>

                <div class="form-group">
                    <label for="edit_fecha_contratacion">Fecha de Contratación:</label>
                    <input type="date" id="edit_fecha_contratacion" name="fecha_contratacion" required>
                </div>

                <div class="form-group">
                    <label for="edit_id_departamento">Departamento:</label>
                    <select name="id_departamento" id="edit_id_departamento" required>
                        <option value="">Seleccione un departamento</option>
                        <?php foreach ($departamentos as $departamento): ?>
                            <option value="<?php echo $departamento['id_departamento']; ?>">
                                <?php echo htmlspecialchars($departamento['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">Guardar Cambios</button>
                    <button type="button" class="button button-secondary" onclick="document.getElementById('modalEditarEmpleado').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    window.onclick = function(event) {
        var modal = document.getElementById('modalEmpleado');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function editarEmpleado(id) {
        fetch('obtener_empleado.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id_empleado').value = data.id_empleado;
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('apellidos').value = data.apellidos;
                document.getElementById('email').value = data.email;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('fecha_contratacion').value = data.fecha_contratacion;
                document.getElementById('id_departamento').value = data.id_departamento;
                document.getElementById('formEmpleado').elements['accion'].value = 'actualizar';
                document.getElementById('modalEmpleado').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener los datos del empleado');
            });
    }
    </script>
</body>
</html> 