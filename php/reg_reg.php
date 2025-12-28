<?php
require "main.php";
$comite = $_POST['nombre_comite'];
$material = $_POST['nombre_material'];


if (empty($comite) && empty($material) && empty($dividir)) {
    $error = "Debe actualizar un parametro";
    echo $error;
    exit();
}

if (!empty($comite)) {

    $sql_comite = "INSERT INTO comite (descripcion) VALUES (?)";
    $stmt1 = $con->prepare($sql_comite);
    $stmt1->bind_param("s", $comite);
        if($stmt1->execute()) {
            $error = "Registro de comite exitoso<br>";
            echo $error;
        } else {
            $error = "Error en el registro de comite: " . $con->error."<br>";
            echo $error;
        }
        
        $stmt1->close();
}


if (!empty($material)) {

    $sql_material = "INSERT INTO material (descripcion) VALUES (?)";
    $stmt2 = $con->prepare($sql_material);
    $stmt2->bind_param("s", $material);
        if($stmt2->execute()) {
            $error = "Registro de material exitoso<br>";
            echo $error;
        } else {
            $error = "Error en el registro de material: " . $con->error."<br>";
            echo $error;
        }
        
        $stmt2->close();
}

    $con->close();


?>