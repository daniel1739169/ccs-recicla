<?php
// php/canjear.php - VERSIÓN COMPATIBLE CON AMBOS FORMATOS
header('Content-Type: application/json');
include 'main.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "JSON inválido: " . json_last_error_msg()]);
    exit();
}

$org_id = $data['organizacion_id'] ?? null;

// DETECTAR FORMATO: ¿productos array (nuevo) o producto_id (viejo)?
$productos = [];
if (isset($data['productos']) && is_array($data['productos']) && !empty($data['productos'])) {
    // Formato nuevo: array de productos
    $productos = $data['productos'];
    $puntos_usados = $data['puntos_usados'] ?? 0;
} elseif (isset($data['producto_id'])) {
    // Formato viejo: un solo producto
    $productos = [[
        'id' => $data['producto_id'],
        'cantidad' => 1,
        'puntos_unidad' => $data['puntos_usados'] ?? 0
    ]];
    $puntos_usados = $data['puntos_usados'] ?? 0;
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos para el canje."]);
    exit();
}

if (!$org_id || empty($productos)) {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos para el canje."]);
    exit();
}

$org_id = (int)$org_id;
$puntos_usados = (int)$puntos_usados;

$con->begin_transaction();

try {
    // 1. Verificar puntos de la organización
    $sql_puntos = "SELECT COALESCE(puntos_acumulados, 0) AS puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_puntos = $con->prepare($sql_puntos);
    $stmt_puntos->bind_param("i", $org_id);
    $stmt_puntos->execute();
    $result_puntos = $stmt_puntos->get_result();
    $puntos_data = $result_puntos->fetch_assoc();
    $puntos_actuales = (int)$puntos_data['puntos_acumulados'];

    if ($puntos_actuales < $puntos_usados) {
        throw new Exception("Puntos insuficientes. Disponibles: {$puntos_actuales}, Requeridos: {$puntos_usados}.");
    }

    // 2. Verificar stock de cada producto ANTES de procesar
    foreach ($productos as $item) {
        $producto_id = (int)$item['id'];
        $cantidad = (int)($item['cantidad'] ?? 1);
        
        $sql_stock = "SELECT stock, nombre FROM productos WHERE id_producto = ?";
        $stmt_stock = $con->prepare($sql_stock);
        $stmt_stock->bind_param("i", $producto_id);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        
        if ($result_stock->num_rows === 0) {
            throw new Exception("Producto ID {$producto_id} no encontrado.");
        }
        
        $producto_data = $result_stock->fetch_assoc();
        if ($producto_data['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para {$producto_data['nombre']}. Disponible: {$producto_data['stock']}, Solicitado: {$cantidad}");
        }
    }

    // 3. Registrar el canje principal
    $sql_insert_canje = "INSERT INTO canje_realizado (id_organizacion, fecha, total_puntos) VALUES (?, NOW(), ?)";
    $stmt_canje = $con->prepare($sql_insert_canje);
    $stmt_canje->bind_param("id", $org_id, $puntos_usados);
    
    if (!$stmt_canje->execute()) {
        throw new Exception("Error al registrar el canje principal: " . $con->error);
    }
    
    $id_canje = $con->insert_id;

    // 4. Registrar detalles y descontar stock
    $sql_insert_detalle = "INSERT INTO canje_detalle (id_canje, id_producto, cantidad, puntos_unidad) VALUES (?, ?, ?, ?)";
    $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
    
    foreach ($productos as $item) {
        $producto_id = (int)$item['id'];
        $cantidad = (int)($item['cantidad'] ?? 1);
        $puntos_unidad = (float)($item['puntos_unidad'] ?? $item['puntos_usados'] ?? 0);
        
        // Registrar detalle
        $stmt_detalle = $con->prepare($sql_insert_detalle);
        $stmt_detalle->bind_param("iiid", $id_canje, $producto_id, $cantidad, $puntos_unidad);
        
        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al registrar detalle del producto ID {$producto_id}: " . $con->error);
        }
        
        // Descontar stock
        $stmt_update = $con->prepare($sql_update_stock);
        $stmt_update->bind_param("ii", $cantidad, $producto_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Error al actualizar stock del producto ID {$producto_id}: " . $con->error);
        }
    }

    // 5. Actualizar puntos de la organización
    $check_col = $con->query("SHOW COLUMNS FROM organizacion LIKE 'total_canjes'");
    $has_total_canjes = ($check_col && $check_col->num_rows > 0);

    if ($has_total_canjes) {
        $sql_update_org = "UPDATE organizacion SET puntos_acumulados = puntos_acumulados - ?, total_canjes = COALESCE(total_canjes,0) + 1 WHERE id = ?";
        $stmt_update_org = $con->prepare($sql_update_org);
        $stmt_update_org->bind_param("ii", $puntos_usados, $org_id);
    } else {
        $sql_update_org = "UPDATE organizacion SET puntos_acumulados = puntos_acumulados - ? WHERE id = ?";
        $stmt_update_org = $con->prepare($sql_update_org);
        $stmt_update_org->bind_param("ii", $puntos_usados, $org_id);
    }
    
    if (!$stmt_update_org->execute()) {
        throw new Exception("Error al actualizar puntos de la organización: " . $con->error);
    }

    // 6. Obtener nuevo estado
    $sql_after = "SELECT COALESCE(puntos_acumulados,0) AS puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_after = $con->prepare($sql_after);
    $stmt_after->bind_param("i", $org_id);
    $stmt_after->execute();
    $res_after = $stmt_after->get_result();
    $puntos_data_after = $res_after->fetch_assoc();
    $nuevo_puntos = (int)$puntos_data_after['puntos_acumulados'];

    $sql_count = "SELECT COUNT(*) AS nuevo_total FROM canje_realizado WHERE id_organizacion = ?";
    $stmt_count = $con->prepare($sql_count);
    $stmt_count->bind_param("i", $org_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $count_data = $result_count->fetch_assoc();
    $nuevo_total_canjes = (int)$count_data['nuevo_total'];

    // 7. Confirmar transacción
    $con->commit();
    
    echo json_encode([
        "success" => true, 
        "message" => "Canje registrado con éxito.",
        "nuevo_total_canjes" => $nuevo_total_canjes,
        "nuevo_puntos" => $nuevo_puntos
    ]);

} catch (Exception $e) {
    $con->rollback();
    echo json_encode([
        "success" => false, 
        "message" => "Error durante el proceso de canje: " . $e->getMessage()
    ]);
} finally {
    $con->close();
}
?>