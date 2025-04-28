<?php
include 'conexion.php';

// Función para obtener todas las nóminas
function obtenerNominas($pdo) {
    $sql = "SELECT n.*, e.nombre, e.apellidos 
            FROM Nominas n 
            JOIN Empleados e ON n.id_empleado = e.id_empleado 
            ORDER BY n.fecha_nomina DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener empleados
function obtenerEmpleados($pdo) {
    $sql = "SELECT id_empleado, nombre, apellidos FROM Empleados ORDER BY apellidos, nombre";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener nómina por ID
function obtenerNomina($pdo, $id) {
    try {
        $sql = "SELECT n.*, e.nombre, e.apellidos 
                FROM Nominas n 
                JOIN Empleados e ON n.id_empleado = e.id_empleado 
                WHERE n.id_nomina = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

// Función para actualizar nómina
function actualizarNomina($pdo, $datos) {
    try {
        $sql = "UPDATE Nominas SET 
                id_empleado = :id_empleado,
                fecha_nomina = :fecha_nomina,
                salario_base = :salario_base,
                bonificaciones = :bonificaciones,
                deducciones = :deducciones,
                total_neto = :total_neto
                WHERE id_nomina = :id_nomina";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($datos);
    } catch (PDOException $e) {
        return false;
    }
}

// Función para agregar nómina
function agregarNomina($pdo, $datos) {
    try {
        $sql = "INSERT INTO Nominas (id_empleado, fecha_nomina, salario_base, bonificaciones, deducciones, total_neto) 
                VALUES (:id_empleado, :fecha_nomina, :salario_base, :bonificaciones, :deducciones, :total_neto)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Función para eliminar nómina
function eliminarNomina($pdo, $id) {
    try {
        $sql = "DELETE FROM Nominas WHERE id_nomina = ?";
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
                $salario_base = $_POST['salario_base'];
                $bonificaciones = $_POST['bonificaciones'] ?? 0;
                $deducciones = $_POST['deducciones'] ?? 0;
                $total_neto = $salario_base + $bonificaciones - $deducciones;

                $datos = [
                    'id_empleado' => $_POST['id_empleado'],
                    'fecha_nomina' => $_POST['fecha_nomina'],
                    'salario_base' => $salario_base,
                    'bonificaciones' => $bonificaciones,
                    'deducciones' => $deducciones,
                    'total_neto' => $total_neto
                ];
                if (agregarNomina($pdo, $datos)) {
                    $mensaje = "Nómina registrada exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al registrar la nómina";
                    $tipo_mensaje = "error";
                }
                break;
            case 'actualizar':
                $salario_base = $_POST['salario_base'];
                $bonificaciones = $_POST['bonificaciones'] ?? 0;
                $deducciones = $_POST['deducciones'] ?? 0;
                $total_neto = $salario_base + $bonificaciones - $deducciones;

                $datos = [
                    'id_nomina' => $_POST['id_nomina'],
                    'id_empleado' => $_POST['id_empleado'],
                    'fecha_nomina' => $_POST['fecha_nomina'],
                    'salario_base' => $salario_base,
                    'bonificaciones' => $bonificaciones,
                    'deducciones' => $deducciones,
                    'total_neto' => $total_neto
                ];
                if (actualizarNomina($pdo, $datos)) {
                    $mensaje = "Nómina actualizada exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al actualizar la nómina";
                    $tipo_mensaje = "error";
                }
                break;
            case 'eliminar':
                if (eliminarNomina($pdo, $_POST['id_nomina'])) {
                    $mensaje = "Nómina eliminada exitosamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al eliminar la nómina";
                    $tipo_mensaje = "error";
                }
                break;
        }
    }
}

$nominas = obtenerNominas($pdo);
$empleados = obtenerEmpleados($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Nóminas - SIRH</title>
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
            <h2>Gestión de Nóminas</h2>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="actions-container">
                <button class="button" onclick="document.getElementById('modalNomina').style.display='block'">
                    Agregar Nómina
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Fecha</th>
                            <th>Salario Base</th>
                            <th>Bonificaciones</th>
                            <th>Deducciones</th>
                            <th>Total Neto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nominas as $nomina): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nomina['apellidos'] . ', ' . $nomina['nombre']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($nomina['fecha_nomina'])); ?></td>
                            <td><?php echo number_format($nomina['salario_base'], 2); ?> €</td>
                            <td><?php echo number_format($nomina['bonificaciones'], 2); ?> €</td>
                            <td><?php echo number_format($nomina['deducciones'], 2); ?> €</td>
                            <td><?php echo number_format($nomina['total_neto'], 2); ?> €</td>
                            <td>
                                <div class="table-actions">
                                    <button class="button button-small" onclick="editarNomina(<?php echo $nomina['id_nomina']; ?>)">
                                        Editar
                                    </button>
                                    <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea eliminar esta nómina?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_nomina" value="<?php echo $nomina['id_nomina']; ?>">
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

    <div id="modalNomina" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nómina</h3>
                <span class="close" onclick="document.getElementById('modalNomina').style.display='none'">&times;</span>
            </div>
            <form method="POST" action="" class="form-container" id="formNomina">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id_nomina" id="id_nomina">
                
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
                    <label for="fecha_nomina">Fecha:</label>
                    <input type="date" name="fecha_nomina" id="fecha_nomina" required>
                </div>

                <div class="form-group">
                    <label for="salario_base">Salario Base:</label>
                    <input type="number" name="salario_base" id="salario_base" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="bonificaciones">Bonificaciones:</label>
                    <input type="number" name="bonificaciones" id="bonificaciones" step="0.01" value="0">
                </div>

                <div class="form-group">
                    <label for="deducciones">Deducciones:</label>
                    <input type="number" name="deducciones" id="deducciones" step="0.01" value="0">
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">Guardar</button>
                    <button type="button" class="button button-secondary" onclick="document.getElementById('modalNomina').style.display='none'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    window.onclick = function(event) {
        var modal = document.getElementById('modalNomina');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function editarNomina(id) {
        fetch('obtener_nomina.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id_nomina').value = data.id_nomina;
                document.getElementById('id_empleado').value = data.id_empleado;
                document.getElementById('fecha_nomina').value = data.fecha_nomina;
                document.getElementById('salario_base').value = data.salario_base;
                document.getElementById('bonificaciones').value = data.bonificaciones;
                document.getElementById('deducciones').value = data.deducciones;
                document.getElementById('formNomina').elements['accion'].value = 'actualizar';
                document.getElementById('modalNomina').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener los datos de la nómina');
            });
    }
    </script>
</body>
</html> 