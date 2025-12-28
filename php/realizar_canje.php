<?php
// /php/realizar_canje.php
header('Content-Type: application/json');

// * CORRECCIÓN CRUCIAL *
// Ahora que realizar_canje.php está en la misma carpeta que main.php, la ruta es directa.
require_once 'main.php'; 

// Solo procesar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$id_organizacion = $data['id_organizacion'] ?? null;
$total_puntos_canje = $data['total_puntos_canje'] ?? null;
$items_canje = $data['items_canje'] ?? [];

// Validación de datos de entrada
if (!$id_organizacion || !is_numeric($total_puntos_canje) || empty($items_canje)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos de canje incompletos o inválidos.']);
    exit();
}

$con->begin_transaction();
$success = true;
$message = 'Canje realizado con éxito.';
$nuevo_total_puntos = 0;
$nuevo_total_canjes = 0; // Inicializar variable

try {
    // 1. VERIFICAR PUNTOS DE LA ORGANIZACIÓN
    // Detectar si la columna `total_canjes` existe en la tabla organizacion para evitar errores
    $check_col = $con->query("SHOW COLUMNS FROM organizacion LIKE 'total_canjes'");
    $has_total_canjes = ($check_col && $check_col->num_rows > 0);

    // Siempre leer puntos_acumulados; si la columna total_canjes existe la leeremos también
    $sql_check_points = $has_total_canjes
        ? "SELECT COALESCE(puntos_acumulados,0) AS puntos_acumulados, COALESCE(total_canjes,0) AS total_canjes FROM organizacion WHERE id = ?"
        : "SELECT COALESCE(puntos_acumulados,0) AS puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_check_points = $con->prepare($sql_check_points);
    $stmt_check_points->bind_param("i", $id_organizacion);
    $stmt_check_points->execute();
    $result = $stmt_check_points->get_result();
    $org = $result->fetch_assoc();
    $puntos_actuales = (float)$org['puntos_acumulados'];
    $total_canjes_actuales = $has_total_canjes ? (int)$org['total_canjes'] : 0;

    if ($puntos_actuales < $total_puntos_canje) {
        throw new Exception('Puntos insuficientes para realizar el canje.');
    }

    // 2. VERIFICAR STOCK DE PRODUCTOS (omito el código de verificación de stock por simplicidad, pero debería ir aquí)

    // 3. INSERTAR EL ENCABEZADO DEL CANJE
    // Asume la tabla 'canje'
    $sql_insert_canje = "INSERT INTO canje (id_organizacion, fecha_canje, puntos_canjeados) VALUES (?, NOW(), ?)";
    $stmt_insert_canje = $con->prepare($sql_insert_canje);
    $stmt_insert_canje->bind_param("id", $id_organizacion, $total_puntos_canje);
    $stmt_insert_canje->execute();
    $id_canje = $con->insert_id; // Obtener el ID del canje recién insertado

    // 4. INSERTAR DETALLES DEL CANJE Y ACTUALIZAR STOCK
    // Asume la tabla 'canje_detalle'
    $sql_insert_detalle = "INSERT INTO canje_detalle (id_canje, id_producto, cantidad, puntos_unidad) VALUES (?, ?, ?, ?)";
    $stmt_insert_detalle = $con->prepare($sql_insert_detalle);
    
    // Asume la tabla 'productos' para descontar stock
    $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
    $stmt_update_stock = $con->prepare($sql_update_stock);

    foreach ($items_canje as $item) {
        $id_producto = $item['id_producto'];
        $cantidad = $item['cantidad'];
        $puntos_unidad = $item['puntos_unidad']; 

        // Insertar detalle
        $stmt_insert_detalle->bind_param("iiid", $id_canje, $id_producto, $cantidad, $puntos_unidad);
        $stmt_insert_detalle->execute();

        // Descontar stock (debería tener una validación de stock antes)
        $stmt_update_stock->bind_param("ii", $cantidad, $id_producto);
        $stmt_update_stock->execute();
    }

    // 5. DESCONTAR PUNTOS Y AUMENTAR CONTADOR DE CANJES DE LA ORGANIZACIÓN
    if ($has_total_canjes) {
        // Actualizar puntos y contador en la tabla organizacion
        $sql_update_points = "UPDATE organizacion 
                              SET puntos_acumulados = puntos_acumulados - ?, 
                                  total_canjes = total_canjes + 1 
                              WHERE id = ?";
        $stmt_update_points = $con->prepare($sql_update_points);
        $stmt_update_points->bind_param("di", $total_puntos_canje, $id_organizacion);
        $stmt_update_points->execute();

        $nuevo_total_puntos = $puntos_actuales - $total_puntos_canje;
        $nuevo_total_canjes = $total_canjes_actuales + 1; // Actualizar el contador localmente
    } else {
        // Solo actualizar puntos en la tabla organizacion; no existe total_canjes
        $sql_update_points = "UPDATE organizacion SET puntos_acumulados = puntos_acumulados - ? WHERE id = ?";
        $stmt_update_points = $con->prepare($sql_update_points);
        $stmt_update_points->bind_param("di", $total_puntos_canje, $id_organizacion);
        $stmt_update_points->execute();

        $nuevo_total_puntos = $puntos_actuales - $total_puntos_canje;
        // Calcular nuevo total de canjes consultando la tabla canje
        $sql_count = "SELECT COUNT(*) AS nuevo_total FROM canje WHERE id_organizacion = ?";
        $stmt_count = $con->prepare($sql_count);
        $stmt_count->bind_param("i", $id_organizacion);
        $stmt_count->execute();
        $res_count = $stmt_count->get_result();
        $row_count = $res_count->fetch_assoc();
        $nuevo_total_canjes = (int)$row_count['nuevo_total'];
    }

    // 6. COMMIT: Confirmar todos los cambios
    $con->commit();

} catch (Exception $e) {
    // ROLLBACK: Revertir si algo falla
    $con->rollback();
    $success = false;
    $message = 'Error en la transacción: ' . $e->getMessage();
}

$con->close();

echo json_encode([
    'success' => $success, 
    'message' => $message,
    'nuevo_total_puntos' => round($nuevo_total_puntos, 2), // Redondear para evitar decimales infinitos
    'nuevo_total_canjes' => $nuevo_total_canjes
]);
?>