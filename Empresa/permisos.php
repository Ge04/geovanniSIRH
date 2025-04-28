<?php
include 'conexion.php';

function obtenerPermisos($pdo) {
    $sql = "SELECT p.*, e.nombre, e.apellidos 
            FROM Permisos p 
            JOIN Empleados e ON p.id_empleado = e.id_empleado 
            ORDER BY p.fecha_inicio DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerEmpleados($pdo) {
    $sql = "SELECT id_empleado, nombre, apellidos FROM Empleados ORDER BY apellidos, nombre";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerPermiso($pdo, $id) {
    try {
        $sql = "SELECT p.*, e.nombre, e.apellidos 
                FROM Permisos p 
                JOIN Empleados e ON p.id_empleado = e.id_empleado 
                WHERE p.id_permiso = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function agregarPermiso($pdo, $datos) {
    try {
        $sql = "INSERT INTO Permisos (id_empleado, tipo_permiso, fecha_inicio, fecha_fin, comentarios) 
                VALUES (:id_empleado, :tipo_permiso, :fecha_inicio, :fecha_fin, :comentarios)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function actualizarEstadoPermiso($pdo, $id, $estado) {
    try {
        $sql = "UPDATE Permisos SET estado = ? WHERE id_permiso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$estado, $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function eliminarPermiso($pdo, $id) {
    try {
        $sql = "DELETE FROM Permisos WHERE id_permiso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $datos = [
                    'id_empleado' => $_POST['id_empleado'],
                    'tipo_permiso' => $_POST['tipo_permiso'],
                    'fecha_inicio' => $_POST['fecha_inicio'],
                    'fecha_fin' => $_POST['fecha_fin'],
                    'comentarios' => $_POST['comentarios']
                ];
                if (agregarPermiso($pdo, $datos)) {
                    $mensaje = "Permiso registrado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al registrar el permiso";
                    $tipo_mensaje = "error";
                }
                break;
            case 'actualizar_estado':
                if (actualizarEstadoPermiso($pdo, $_POST['id_permiso'], $_POST['estado'])) {
                    $mensaje = "Estado actualizado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al actualizar el estado";
                    $tipo_mensaje = "error";
                }
                break;
            case 'eliminar':
                if (eliminarPermiso($pdo, $_POST['id_permiso'])) {
                    $mensaje = "Permiso eliminado exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al eliminar el permiso";
                    $tipo_mensaje = "error";
                }
                break;
        }
    }
}

$permisos = obtenerPermisos($pdo);
$empleados = obtenerEmpleados($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - SIRH</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-body {
            padding: 15px 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .modal-body p {
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .table-actions {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .button-small {
            padding: 6px 12px;
            height: 32px;
            line-height: 1;
            font-size: 0.9em;
        }

        .estado-pendiente,
        .estado-aprobado,
        .estado-rechazado {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9em;
            height: 32px;
            line-height: 1;
            text-align: center;
            min-width: 100px;
        }

        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .estado-aprobado {
            background-color: #d4edda;
            color: #155724;
        }

        .estado-rechazado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }
    </style>
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
            <h2>Gestión de Permisos y Vacaciones</h2>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="actions-container">
                <button class="button" onclick="document.getElementById('modalPermiso').style.display='block'">
                    Solicitar Permiso
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Tipo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Comentarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permisos as $permiso): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($permiso['apellidos'] . ', ' . $permiso['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($permiso['tipo_permiso']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($permiso['fecha_inicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($permiso['fecha_fin'])); ?></td>
                            <td>
                                <span class="estado-<?php echo strtolower($permiso['estado']); ?>">
                                    <?php echo htmlspecialchars($permiso['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="button button-small" onclick="verComentarios(<?php echo $permiso['id_permiso']; ?>)">
                                    Ver Comentarios
                                </button>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <?php if ($permiso['estado'] === 'Pendiente'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="accion" value="actualizar_estado">
                                        <input type="hidden" name="id_permiso" value="<?php echo $permiso['id_permiso']; ?>">
                                        <input type="hidden" name="estado" value="Aprobado">
                                        <button type="submit" class="button button-small">Aprobar</button>
                                    </form>
                                    <form method="POST" action="">
                                        <input type="hidden" name="accion" value="actualizar_estado">
                                        <input type="hidden" name="id_permiso" value="<?php echo $permiso['id_permiso']; ?>">
                                        <input type="hidden" name="estado" value="Rechazado">
                                        <button type="submit" class="button button-small button-danger">Rechazar</button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea eliminar este permiso?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_permiso" value="<?php echo $permiso['id_permiso']; ?>">
                                        <button type="submit" class="button button-small button-danger">Eliminar</button>
                                    </form>
                                    <?php endif; ?>
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

    <div id="modalPermiso" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Solicitar Permiso</h3>
                <span class="close" onclick="document.getElementById('modalPermiso').style.display='none'">&times;</span>
            </div>
            <form method="POST" action="" class="form-container">
                <input type="hidden" name="accion" value="agregar">
                
                <div class="form-group">
                    <label for="id_empleado">Empleado:</label>
                    <select name="id_empleado" id="id_empleado" required>
                        <option value="">Seleccione un empleado</option>
                        <?php foreach ($empleados as $empleado): ?>
                            <option value="<?php echo $empleado['id_empleado']; ?>">
                                <?php echo htmlspecialchars($empleado['apellidos'] . ', ' . $empleado['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_permiso">Tipo de Permiso:</label>
                    <select name="tipo_permiso" id="tipo_permiso" required>
                        <option value="Vacaciones">Vacaciones</option>
                        <option value="Permiso médico">Permiso médico</option>
                        <option value="Permiso personal">Permiso personal</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" required>
                </div>

                <div class="form-group">
                    <label for="comentarios">Comentarios:</label>
                    <textarea name="comentarios" id="comentarios" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">Solicitar Permiso</button>
                    <button type="button" class="button button-secondary" onclick="document.getElementById('modalPermiso').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalComentarios" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles del Permiso</h3>
                <span class="close" onclick="document.getElementById('modalComentarios').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <p id="comentariosTexto"></p>
            </div>
            <div class="form-actions">
                <button type="button" class="button" onclick="document.getElementById('modalComentarios').style.display='none'">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
    window.onclick = function(event) {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    }

    function verComentarios(id) {
        fetch('obtener_permiso.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('comentariosTexto').textContent = data.comentarios || 'Sin comentarios';
                document.getElementById('modalComentarios').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener los comentarios');
            });
    }
    </script>
</body>
</html>