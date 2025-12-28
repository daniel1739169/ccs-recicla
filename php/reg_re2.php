<?php
require 'main.php';

if (!isset($_POST['correlativo'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan datos generales'
    ]);
    exit; // ← faltaba el punto y coma
}

$correlativo = $_POST['correlativo'];

$con->begin_transaction();

try {
    // Buscar id_refinal y id_organizacion con prepared statement
    $sql_ref = "SELECT id, id_organizacion, estado FROM recibo_final WHERE correlativo = ?";
    $stmt_ref = $con->prepare($sql_ref);
    $stmt_ref->bind_param("i", $correlativo);
    $stmt_ref->execute();
    $result_ref = $stmt_ref->get_result();
    $ref = $result_ref->fetch_assoc();

    if (!$ref) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No existe recibo_final para el correlativo indicado'
        ]);
        $con->rollback();
        exit;
    }

    $id_ref = $ref['id'];
    $id_org = $ref['id_organizacion'];
    $estado_ref = isset($ref['estado']) ? (int)$ref['estado'] : 0;

    // Evitar reprocesar si ya fue marcado como procesado
    if ($estado_ref === 1) {
        echo json_encode([
            'status' => 'error',
            'message' => "Este recibo final ya ha sido procesado"
        ]);
        $con->rollback();
        exit;
    }

    // Consultar materiales
    $sql_m = "SELECT id, descripcion FROM material";
    $resultado_m = mysqli_query($con, $sql_m);

    $materiales_validos = [];
    $total_kg = 0.0;
    $total_p  = 0.0;

    while ($row = mysqli_fetch_assoc($resultado_m)) {
        $m_id = $row['id'];
        $m_desc = $row['descripcion'];

        $campo_kg = $m_id.'_cantidad';
        $campo_p  = $m_id.'_puntaje';

        if (isset($_POST[$campo_kg]) && isset($_POST[$campo_p])) {
            $cantidad = floatval($_POST[$campo_kg]);
            $puntaje  = floatval($_POST[$campo_p]);

            // Validación: ambos > 0.00
            if ($cantidad > 0 && $puntaje > 0) {
                $materiales_validos[] = [
                    'id'       => $m_id,
                    'cantidad' => $cantidad,
                    'puntaje'  => $puntaje,
                    'desc'     => $m_desc
                ];
                $total_kg += $cantidad;
                $total_p  += $puntaje;
            }
        }
    }

    if (count($materiales_validos) === 0) {
        echo json_encode([
            'status' => 'alert',
            'alerta' => "No se han registrado materiales válidos para el recibo final"
        ]);
        $con->rollback();
        exit;
    }

    $errores = [];
    foreach ($materiales_validos as $m) {
        $sql_insert_m = "INSERT INTO material_refinal (correlativo, id_material, cantidad_kg, cantidad_p, total_kg, total_p, id_refinal)
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_m = $con->prepare($sql_insert_m);
        $stmt_m->bind_param("iiddddi", $correlativo, $m['id'], $m['cantidad'], $m['puntaje'], $total_kg, $total_p, $id_ref);

        if (!$stmt_m->execute()) {
            $errores[] = "Error al guardar el material ".$m['desc'].": ".$stmt_m->error;
        }
    }

    if (count($errores) > 0) {
        $con->rollback();
        echo implode("<br>", $errores);
        exit;
    }

    $sql_update_e = "UPDATE recibo_final SET estado = '1' WHERE correlativo = ?";
    $stmt_e = $con->prepare($sql_update_e);
    $stmt_e->bind_param("i", $correlativo);
    $stmt_e->execute();

    $sql_update_totales = "UPDATE recibo_final SET total_kg = ?, total_p = ? WHERE id = ?";
    $stmt_tot = $con->prepare($sql_update_totales);
    $stmt_tot->bind_param("ddi", $total_kg, $total_p, $id_ref);
    if (!$stmt_tot->execute()) {
        $con->rollback();
        echo "Error al actualizar totales en recibo_final: " . $stmt_tot->error;
        exit;
    }

    if ($id_org && $total_p > 0) {
        $sql_up_org = "UPDATE organizacion SET puntos_acumulados = COALESCE(puntos_acumulados,0) + ? WHERE id = ?";
        $stmt_up_org = $con->prepare($sql_up_org);
        $stmt_up_org->bind_param("di", $total_p, $id_org);
        if (!$stmt_up_org->execute()) {
            $con->rollback();
            echo "Error al actualizar puntos de la organización: " . $stmt_up_org->error;
            exit;
        }
    }

    $con->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Recibo final registrado exitosamente'
    ]);

} catch (Exception $e) {
    $con->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'ERROR'
    ]);
}
?>
