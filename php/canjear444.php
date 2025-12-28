<?php
// php/canjear.php - VERSIÓN COMPATIBLE CON TU BD
header('Content-Type: application/json');
include 'main.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "JSON inválido: " . json_last_error_msg()]);
    exit();
}

$org_id = $data['organizacion_id'] ?? null;
$prod_id = $data['producto_id'] ?? null;
$puntos_usados = $data['puntos_usados'] ?? null;

// Validación básica
if (!$org_id || !$prod_id || !$puntos_usados) {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos para el canje."]);
    exit();
}

$org_id = (int)$org_id;
$prod_id = (int)$prod_id;
$puntos_usados = (int)$puntos_usados;

$con->begin_transaction();

try {
    // A. Obtener Puntos Acumulados desde organizacion.puntos_acumulados (columna fiable)
    $sql_puntos = "SELECT COALESCE(puntos_acumulados, 0) AS puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_puntos = $con->prepare($sql_puntos);
    $stmt_puntos->bind_param("i", $org_id);
    $stmt_puntos->execute();
    $result_puntos = $stmt_puntos->get_result();
    
    $puntos_data = $result_puntos->fetch_assoc();
    $puntos_actuales = (int)$puntos_data['puntos_acumulados'];

    // B. Verificar Saldo
    if ($puntos_actuales < $puntos_usados) {
        throw new Exception("Puntos insuficientes. Disponibles: {$puntos_actuales}, Requeridos: {$puntos_usados}.");
    }

    // C. Registrar en canje_realizado (tu tabla principal)
    $sql_insert_canje = "INSERT INTO canje_realizado (id_organizacion, fecha, total_puntos) VALUES (?, NOW(), ?)";
    $stmt_canje = $con->prepare($sql_insert_canje);
    $stmt_canje->bind_param("id", $org_id, $puntos_usados);
    
    if (!$stmt_canje->execute()) {
        throw new Exception("Error al registrar el canje principal: " . $con->error);
    }
    
    $id_canje = $con->insert_id;

    // D. Registrar detalle en canje_detalle
    $sql_insert_detalle = "INSERT INTO canje_detalle (id_canje, id_producto, cantidad, puntos_unidad) VALUES (?, ?, 1, ?)";
    $stmt_detalle = $con->prepare($sql_insert_detalle);
    $puntos_unidad = (float)$puntos_usados;
    $stmt_detalle->bind_param("iid", $id_canje, $prod_id, $puntos_unidad);
    
    if (!$stmt_detalle->execute()) {
        throw new Exception("Error al registrar el detalle del canje: " . $con->error);
    }

    // E. Calcular el nuevo total de canjes (usando canje_realizado)
    $sql_count = "SELECT COUNT(*) AS nuevo_total FROM canje_realizado WHERE id_organizacion = ?";
    $stmt_count = $con->prepare($sql_count);
    $stmt_count->bind_param("i", $org_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $count_data = $result_count->fetch_assoc();
    $nuevo_total_canjes = (int)$count_data['nuevo_total'];

    // G. Descontar puntos de la organización y, si existe, actualizar el contador total_canjes.
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

    // Leer el nuevo saldo para devolverlo al cliente
    $sql_after = "SELECT COALESCE(puntos_acumulados,0) AS puntos_acumulados FROM organizacion WHERE id = ?";
    $stmt_after = $con->prepare($sql_after);
    $stmt_after->bind_param("i", $org_id);
    $stmt_after->execute();
    $res_after = $stmt_after->get_result();
    $puntos_data_after = $res_after->fetch_assoc();
    $nuevo_puntos = (int)$puntos_data_after['puntos_acumulados'];
    // $nuevo_total_canjes lo habíamos calculado previamente usando COUNT(*) sobre canje_realizado
    // (ver sección E). Usamos ese valor ya calculado. Si la tabla organizacion tiene total_canjes,
    // el UPDATE hecho arriba incrementó el contador, y $nuevo_total_canjes (COUNT) será coherente.

    // F. Confirmar Transacción
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