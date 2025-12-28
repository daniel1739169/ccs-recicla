<?php
require("./php/main.php"); // asegúrate de incluir la conexión

if (isset($_POST['id']) && isset($_POST['updatePassword'])) {
    $id = intval($_POST['id']);
    $clave = $_POST['updatePassword'];
    $user = $_POST['user'];
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
    $status = '1';

    $sql_clave = "UPDATE personal SET clave = ?, status = ? WHERE cedula = ?";
    $stmt = $con->prepare($sql_clave);
    $stmt->bind_param("sii", $clave_hash, $status, $user);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Clave actualizada correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "No se actualizó ninguna fila"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar clave: ".$stmt->error]);
    }

}
?>
