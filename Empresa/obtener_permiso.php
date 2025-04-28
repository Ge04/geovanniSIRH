<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    try {
        $sql = "SELECT comentarios FROM Permisos WHERE id_permiso = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['id']]);
        $permiso = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['comentarios' => $permiso['comentarios']]);
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Error al obtener los comentarios']);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID no proporcionado']);
}
?> 