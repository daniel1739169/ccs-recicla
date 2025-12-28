<?php
// php/organizacion.php
header('Content-Type: application/json');
require_once 'main.php';

// Manejar tanto por ID como por nombre
$id = $_GET['id'] ?? null;
$nombre = $_GET['nombre'] ?? null;

// Determinar el nombre de la columna que almacena puntos en recibo_final
// Detectar columna de puntos existente en `recibo_final`
$puntos_col = null;
$check = $con->query("SHOW COLUMNS FROM recibo_final LIKE 'total_p'");
if ($check && $check->num_rows > 0) {
    $puntos_col = 'total_p';
} else {
    $check2 = $con->query("SHOW COLUMNS FROM recibo_final LIKE 'total_puntos'");
    if ($check2 && $check2->num_rows > 0) {
        $puntos_col = 'total_puntos';
    }
}

// Si no se detecta la columna, no referenciarla en las consultas para evitar errores
$points_expr = $puntos_col ? "COALESCE(SUM(rf.`$puntos_col`), 0)" : "0";

if ($id) {
    // Buscar por ID — tomar puntos directamente desde la columna organizacion.puntos_acumulados
    $sql = "SELECT o.id, o.nombre, 
                   COALESCE(o.puntos_acumulados, 0) AS puntos_acumulados,
                   COUNT(cr.id_canje) as total_canjes
            FROM organizacion o
            LEFT JOIN canje_realizado cr ON o.id = cr.id_organizacion
            WHERE o.id = ?
            GROUP BY o.id, o.nombre";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
} elseif ($nombre) {
        // Buscar por nombre — tomar puntos desde organizacion.puntos_acumulados
        $sql = "SELECT o.id, o.nombre, 
                 COALESCE(o.puntos_acumulados, 0) AS puntos_acumulados,
                 COUNT(cr.id_canje) as total_canjes
             FROM organizacion o
             LEFT JOIN canje_realizado cr ON o.id = cr.id_organizacion
             WHERE o.nombre LIKE ?
             GROUP BY o.id, o.nombre";
    
    $stmt = $con->prepare($sql);
    $search_nombre = "%" . $nombre . "%";
    $stmt->bind_param("s", $search_nombre);
} else {
    echo json_encode(['error' => 'Se requiere ID o nombre de organización']);
    exit();
}

    $stmt->execute();
    $result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $organizacion = $result->fetch_assoc();
    echo json_encode([
        'id' => (int)$organizacion['id'],
        'nombre' => $organizacion['nombre'],
        'puntos_acumulados' => (float)$organizacion['puntos_acumulados'],
        'total_canjes' => (int)$organizacion['total_canjes']
    ]);
} else {
    // Si no encontramos la organización con el filtro de estado, intentar nuevamente
    // sin restringir por estado (fallback). Esto ayuda cuando la tabla no usa el mismo
    // valor de 'estado' esperado o hay inconsistencias.
    if ($id) {
        $sql2 = "SELECT o.id, o.nombre, 
                   COALESCE(o.puntos_acumulados, 0) AS puntos_acumulados,
                       COUNT(cr.id_canje) as total_canjes
                FROM organizacion o
                LEFT JOIN canje_realizado cr ON o.id = cr.id_organizacion
                WHERE o.id = ?
                GROUP BY o.id, o.nombre";

        $stmt2 = $con->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2 && $result2->num_rows > 0) {
            $organizacion = $result2->fetch_assoc();
            echo json_encode([
                'id' => (int)$organizacion['id'],
                'nombre' => $organizacion['nombre'],
                'puntos_acumulados' => (float)$organizacion['puntos_acumulados'],
                'total_canjes' => (int)$organizacion['total_canjes']
            ]);
            $con->close();
            exit();
        }
    }

    http_response_code(404);
    echo json_encode(['error' => 'Organización no encontrada']);
}

$con->close();
?>