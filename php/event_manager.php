<?php
// event_manager.php
require 'main.php'; // Tu conexión MySQLi
header('Content-Type: application/json');

// Leer el JSON enviado por el JavaScript (esto no cambia)
$input = json_decode(file_get_contents('php://input'), true);
$accion = $input['accion'] ?? '';

try {
    if ($accion === 'guardar') {
        // Combinar fecha y hora
        $fecha_hora = $input['fecha'] . ' ' . $input['hora'] . ':00';
        $id_org = $input['organizacion_id'];
        $responsable = $input['responsable'];
        $cargo = $input['cargo'];
        $id_promotor = $input['promotor_id'] ? (int)$input['promotor_id'] : NULL;
        $evento_id = $input['id'] ?? null; // ID del evento si estamos editando
        $estado = '0';

        if (empty($evento_id)) {
            // --- CREAR NUEVO ---
            $sql = "INSERT INTO visitas (id_organizacion, responsable, cargo, fecha, estado, id_promotor) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            // "isssii" = integer, string, string, string, integer, integer
            mysqli_stmt_bind_param($stmt, "isssii", $id_org, $responsable, $cargo, $fecha_hora, $estado, $id_promotor);
            mysqli_stmt_execute($stmt);
            $lastId = mysqli_insert_id($con); // Obtener el ID del nuevo registro
        
        } else {
            // --- ACTUALIZAR EXISTENTE ---
            $sql = "UPDATE visitas SET id_organizacion = ?, responsable = ?, cargo = ?, fecha = ?, id_promotor = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);
            // "isssii" = integer, string, string, string, integer, integer
            mysqli_stmt_bind_param($stmt, "isssii", $id_org, $responsable, $cargo, $fecha_hora, $id_promotor, $evento_id);
            mysqli_stmt_execute($stmt);
            $lastId = $evento_id; // Devolvemos el mismo ID que actualizamos
        }
        
        // Verificar si hubo error en la ejecución
        if (mysqli_stmt_error($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }
        echo json_encode(['success' => true, 'id' => $lastId]);

    } elseif ($accion === 'eliminar') {
        // --- ELIMINAR ---
        $evento_id = $input['id'];
        $sql = "DELETE FROM visitas WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql);
        // "i" = integer
        mysqli_stmt_bind_param($stmt, "i", $evento_id);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }
        echo json_encode(['success' => true]);

    } else {
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }

} catch (Exception $e) {
    // Capturar cualquier excepción de la BD
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
