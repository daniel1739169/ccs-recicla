<?php

header('Content-Type: application/json; charset=utf-8');

require 'main.php';

$errores = [];

// Validaciones iniciales
if (empty($_POST['organizacion'])) $errores['org_prerecibo'] = 'No se ha encontrado la organización';
if (empty($_POST['responsable']))  $errores['responsable'] = 'No se ha encontrado el responsable';
if (empty($_POST['cargo']))        $errores['cargo'] = 'No se ha encontrado el cargo del responsable';
if (empty($_POST['fecha_pre']))    $errores['fecha_prerecibo'] = '';
/*if (empty($_POST['visita']))       $errores[] = 'No se ha encontrado esa visita';*/

    if (!empty($errores)) {
        echo json_encode([
            "status" => "error",
            "errores" => $errores
        ]);
        exit;
    }

$fecha_registro = date("Y-m-d", strtotime($_POST['fecha_pre']));
$responsable    = $_POST['responsable'];
$organizacion   = $_POST['organizacion'];
$cargo          = $_POST['cargo'];
$id_visita = $_POST['visita'];

// Consultar organización
$sql_o = "SELECT ubicacion, id FROM organizacion WHERE nombre = ?";
$stmt_o = $con->prepare($sql_o);
$stmt_o->bind_param("s", $organizacion);
$stmt_o->execute();
$result_org = $stmt_o->get_result();
$direccion  = $result_org->fetch_assoc();
$id_org     = $direccion['id'];

// Consultar correlativo
$sql_last = "SELECT MAX(correlativo) AS ultimo FROM recibo_final";
$result   = $con->query($sql_last);
$row      = $result->fetch_assoc();
$correlativo_nuevo = ($row['ultimo'] ?? 0) + 1;

$sql_m = "SELECT id, descripcion FROM material";
$resultado_m = mysqli_query($con, $sql_m);

$materiales_validos = [];
while ($row = mysqli_fetch_assoc($resultado_m)) {
    $m_id = $row['id'];
    $campo_kg = $m_id.'_cantidad';
    $campo_p  = $m_id.'_puntaje';

    // Normaliza valores; si el input puede traer coma decimal, reemplázala:
    $vkg = isset($_POST[$campo_kg]) ? str_replace(',', '.', trim($_POST[$campo_kg])) : '';
    $vp  = isset($_POST[$campo_p])  ? str_replace(',', '.', trim($_POST[$campo_p]))  : '';

    $cantidad = ($vkg !== '' && is_numeric($vkg)) ? floatval($vkg) : 0.0;
    $puntaje  = ($vp  !== '' && is_numeric($vp))  ? floatval($vp)  : 0.0;

    // Solo considerar válido si ambos > 0.00
    if ($cantidad > 0 && $puntaje > 0) {
        $materiales_validos[] = [
            'id'       => $m_id,
            'cantidad' => $cantidad,
            'puntaje'  => $puntaje,
            'desc'     => $row['descripcion']
        ];
    }
}

// Si no hay materiales válidos, NO insertes correlativos
if (count($materiales_validos) === 0) {
    echo json_encode([
        'status' => 'alert',
        'alerta' => "No se han registrado los materiales recolectados"
    ]);
    exit;
}

// Calcula los TOTALES FINALES (suma de todos los materiales válidos)
$total_kg = 0.0;
$total_p  = 0.0;
foreach ($materiales_validos as $m) {
    $total_kg += $m['cantidad'];
    $total_p  += $m['puntaje'];
}

$con->begin_transaction();

try {
    $error = [];

    // Insertar prerecibo
    $sql_p = "INSERT INTO prerecibo (correlativo, responsable, cargo, id_organizacion, fecha) VALUES (?, ?, ?, ?, ?)";
    $stmt_p = $con->prepare($sql_p);
    $stmt_p->bind_param("issis", $correlativo_nuevo, $responsable, $cargo, $id_org, $fecha_registro);
    $stmt_p->execute();
    $id_pre = $con->insert_id;

    // Insertar recibo_final
    $estado = 0;
    $sql_r = "INSERT INTO recibo_final (correlativo, responsable, cargo, id_organizacion, id_prerecibo, fecha, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_r = $con->prepare($sql_r);
    $stmt_r->bind_param("issiisi", $correlativo_nuevo, $responsable, $cargo, $id_org, $id_pre, $fecha_registro, $estado);
    $stmt_r->execute();

    $sql_v = "UPDATE visitas SET estado = 1 WHERE id = ?;";
    $stmt_v = $con->prepare($sql_v);
    $stmt_v->bind_param("i", $id_visita);
    $stmt_v->execute();



    // Insertar únicamente los materiales válidos
    foreach ($materiales_validos as $m) {
        $sql_insert_m = "INSERT INTO material_pre (correlativo, id_material, cantidad_kg, cantidad_p, total_kg, total_p, id_prerecibo) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_m = $con->prepare($sql_insert_m);

        // Usa los TOTALES FINALES (misma suma) para todas las filas
        $stmt_m->bind_param("iiddddi", $correlativo_nuevo, $m['id'], $m['cantidad'], $m['puntaje'], $total_kg, $total_p, $id_pre);

        if (!$stmt_m->execute()) {
            $error[] = "Error al insertar material ".$m['desc'].": ".$stmt_m->error;
        }
    }

    if (count($error) > 0) {
        $con->rollback();
        echo implode("<br>", $error);
    } else {
        $con->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Prerecibo registrado exitosamente'
        ]);
        exit;
    }

} catch (Exception $e) {
    $con->rollback();
    echo "ERROR: ".$e->getMessage();
}
?>
