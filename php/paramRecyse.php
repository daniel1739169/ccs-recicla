<?php
require "main.php";

$dividir = $_POST['valor'];

$errores = [];

if (empty($dividir)) {
    $errores['valorDiv'] = "";
}

if (!empty($errores)) {
    echo json_encode([
        "status" => "error",
        "errores" => $errores
    ]);
    exit;
}



    $sql_dividir = "UPDATE parametro_calculo SET division_kilo = ? WHERE id = 1";
    $stmt3 = $con->prepare($sql_dividir);
    $stmt3->bind_param("i", $dividir);

    if ($stmt3->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Factor divisor actualizado correctamente"
        ]);    
    }

    $con->close();


?>