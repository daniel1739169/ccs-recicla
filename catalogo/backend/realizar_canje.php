<?php
// catalogo/backend/realizar_canje.php
header('Content-Type: application/json');
require_once '../../php/main.php'; 

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

try {
    // 1. VERIFICAR PUNTOS DE LA ORGANIZACIÓN
    $sql_check_points = "SELECT puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_check_points = $con->prepare($sql_check_points);
    $stmt_check_points->bind_param("i", $id_organizacion);
    $stmt_check_points->execute();
    $result_points = $stmt_check_points->get_result();
    
    if ($result_points->num_rows === 0) {
        throw new Exception("Organización no encontrada.");
    }
    
    $org = $result_points->fetch_assoc();
    $puntos_actuales = $org['puntos_acumulados'];

    if ($puntos_actuales < $total_puntos_canje) {
        throw new Exception("Puntos insuficientes. Puntos actuales: {$puntos_actuales}. Puntos requeridos: {$total_puntos_canje}.");
    }

    // 2. VERIFICAR Y DESCONTAR STOCK DE CADA PRODUCTO
    foreach ($items_canje as $item) {
        $id_producto = $item['id_producto'];
        $cantidad = $item['cantidad'];

        // Bloquear fila de producto (FOR UPDATE) para manejo de concurrencia
        $sql_lock_stock = "SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE";
        $stmt_lock_stock = $con->prepare($sql_lock_stock);
        $stmt_lock_stock->bind_param("i", $id_producto);
        $stmt_lock_stock->execute();
        $res_stock = $stmt_lock_stock->get_result();
        
        if ($res_stock->num_rows === 0) {
             throw new Exception("Producto con ID {$id_producto} no encontrado.");
        }
        
        $current_stock = $res_stock->fetch_assoc()['stock'];
        
        if ($current_stock < $cantidad) {
            throw new Exception("Stock insuficiente para el producto ID {$id_producto}. Stock disponible: {$current_stock}.");
        }

        // Descontar stock
        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
        $stmt_update_stock = $con->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ii", $cantidad, $id_producto);
        $stmt_update_stock->execute();
    }

    // 3. REGISTRAR EL CANJE EN LA TABLA PRINCIPAL
    // Asume la tabla 'canje_realizado'
    $sql_insert_canje = "INSERT INTO canje_realizado (id_organizacion, fecha, total_puntos) VALUES (?, NOW(), ?)";
    $stmt_insert_canje = $con->prepare($sql_insert_canje);
    $stmt_insert_canje->bind_param("id", $id_organizacion, $total_puntos_canje);
    $stmt_insert_canje->execute();
    $id_canje = $con->insert_id;

    // 4. REGISTRAR DETALLE DEL CANJE
    // Asume la tabla 'canje_detalle'
    $sql_insert_detalle = "INSERT INTO canje_detalle (id_canje, id_producto, cantidad, puntos_unidad) VALUES (?, ?, ?, ?)";
    $stmt_insert_detalle = $con->prepare($sql_insert_detalle);
    
    foreach ($items_canje as $item) {
        $id_producto = $item['id_producto'];
        $cantidad = $item['cantidad'];
        $puntos_unidad = $item['puntos_unidad']; // Puntos requeridos por unidad

        $stmt_insert_detalle->bind_param("iiid", $id_canje, $id_producto, $cantidad, $puntos_unidad);
        $stmt_insert_detalle->execute();
    }

    // 5. DESCONTAR PUNTOS DE LA ORGANIZACIÓN y actualizar total_canjes si existe
    $check_col = $con->query("SHOW COLUMNS FROM organizacion LIKE 'total_canjes'");
    $has_total_canjes = ($check_col && $check_col->num_rows > 0);

    if ($has_total_canjes) {
        $sql_update_points = "UPDATE organizacion SET puntos_acumulados = puntos_acumulados - ?, total_canjes = COALESCE(total_canjes,0) + 1 WHERE id = ?";
        $stmt_update_points = $con->prepare($sql_update_points);
        $stmt_update_points->bind_param("di", $total_puntos_canje, $id_organizacion);
        $stmt_update_points->execute();

        // calcular nuevo contador usando la columna si se requiere (o podemos confiar en COUNT)
        $sql_count = "SELECT COALESCE(total_canjes,0) AS nuevo_total FROM organizacion WHERE id = ?";
        $stmt_count_after = $con->prepare($sql_count);
        $stmt_count_after->bind_param("i", $id_organizacion);
        $stmt_count_after->execute();
        $res_count_after = $stmt_count_after->get_result();
        $nuevo_total_row = $res_count_after->fetch_assoc();
        $nuevo_total_canjes = (int)$nuevo_total_row['nuevo_total'];
        $nuevo_total_puntos = $puntos_actuales - $total_puntos_canje;
    } else {
        $sql_update_points = "UPDATE organizacion SET puntos_acumulados = puntos_acumulados - ? WHERE id = ?";
        $stmt_update_points = $con->prepare($sql_update_points);
        $stmt_update_points->bind_param("di", $total_puntos_canje, $id_organizacion);
        $stmt_update_points->execute();

        $nuevo_total_puntos = $puntos_actuales - $total_puntos_canje;

        // calcular nuevo total de canjes consultando la tabla canje_realizado
        $sql_count = "SELECT COUNT(*) AS nuevo_total FROM canje_realizado WHERE id_organizacion = ?";
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
    // ROLLBACK: Deshacer todos los cambios si algo falló
    $con->rollback();
    $success = false;
    $message = 'Fallo la transacción de canje: ' . $e->getMessage();
    http_response_code(500);

} finally {
    if (isset($con)) $con->close();
}

echo json_encode([
    'success' => $success, 
    'message' => $message, 
    'nuevo_total_puntos' => $nuevo_total_puntos,
    'nuevo_total_canjes' => $nuevo_total_canjes
]);
?>