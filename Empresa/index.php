<?php include 'conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Información de Recursos Humanos (SIRH)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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
        <div class="welcome-section">
            <h2>Bienvenido al Sistema de Información de Recursos Humanos</h2>
            <p>Este sistema le permite gestionar eficientemente los recursos humanos de su empresa.</p>
        </div>

        <div class="dashboard">
            <div class="dashboard-item">
                <h3>Empleados</h3>
                <p>Gestionar información de empleados, contratos y departamentos.</p>
                <a href="empleados.php" class="button">Ver Empleados</a>
            </div>

            <div class="dashboard-item">
                <h3>Nóminas</h3>
                <p>Administrar pagos y nóminas mensuales.</p>
                <a href="nominas.php" class="button">Ver Nóminas</a>
            </div>

            <div class="dashboard-item">
                <h3>Permisos</h3>
                <p>Gestionar vacaciones y permisos del personal.</p>
                <a href="permisos.php" class="button">Ver Permisos</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Información de Recursos Humanos</p>
        </div>
    </footer>
</body>
</html>
