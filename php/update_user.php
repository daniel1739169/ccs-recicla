<?php
header('Content-Type: application/json');
include "main.php";

if (!isset($_POST['id'])) {
    echo json_encode([
        'error' => 'No se recibió el ID',
        'personal' => null
    ]);
    exit;
}


// LECTURA DE CAMPOS
     
    $id_update = intval($_POST['id']); 
    $sql_update = "SELECT id, nacionalidad, cedula, fecha_nac, nombre, apellido, correo, id_gerencia, id_division, id_rol, correo FROM personal WHERE id = $id_update"; 
    
    $result_update = mysqli_query($con, $sql_update); 
    $personal = [];
    while ($actualizar = mysqli_fetch_assoc($result_update)) {
      


            $personal = $actualizar;

        } 
  



echo json_encode([
    'personal' => $personal
    ]);
?>