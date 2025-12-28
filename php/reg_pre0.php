<?php
require "main.php";

$fecha_registro = date("Y-m-d");

$encargado = $_POST['encargado'];
$organizacion = $_POST['organizacion'];
$cargo = $_POST['cargo'];


$sql = "SELECT ubicacion, id FROM organizacion WHERE nombre = '$organizacion'";
$result = mysqli_query($con, $sql);
$direccion = mysqli_fetch_array($result);

//TOTALES
$total_c = $_POST['total_cantidad'];
$total_p = $_POST['total_puntos'];


$ubicacion = $direccion['ubicacion'];
$id_org = $direccion['id'];

$con->begin_transaction();

try{

    $stmt1 = $con->prepare("INSERT INTO lista_material (aluminio_c, aluminio_p, archivo_c, archivo_p, carton_c, carton_p, calamina_c, calamina_p, hierro_c, hierro_p, laton_c, laton_p, pelicula_limpia_c, pelicula_limpia_p, pelicula_sucia_c, pelicula_sucia_p, plastico_mixto_c, plastico_mixto_p, plastico_pet_c, plastico_pet_p, plastico_tobo_c, plastico_tobo_p, soplado_blanco_c, soplado_blanco_p, soplado_color_c, soplado_color_p, vidrio_c, vidrio_p, total_c, total_p) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("dddddddddddddddddddddddddddddd", $aluminio_c, $aluminio_p, $archivo_c, $archivo_p, $carton_c, $carton_p, $calamina_c, $calamina_p, $hierro_c, $hierro_p, $laton_c, $laton_p, $pelicula_limpia_c, $pelicula_limpia_p, $pelicula_sucia_c, $pelicula_sucia_p, $plastico_mixto_c, $plastico_mixto_p, $plastico_pet_c, $plastico_pet_p, $plastico_tobo_c, $plastico_tobo_p,  $soplado_blanco_c, $soplado_blanco_p, $soplado_color_c, $soplado_color_p, $vidrio_c, $vidrio_p, $total_c, $total_p);
    
    $stmt1->execute();

    $id_list = $con->insert_id;

    $estado = 0;

    //Preparar y ejecutar la insercion del prerecibo
    $stmt2 = $con->prepare("INSERT INTO prerecibo (encargado, cargo, id_organizacion, fecha, total_kg, total_p, id_lista) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("ssisddi", $encargado, $cargo, $id_org, $fecha_registro, $total_c, $total_p, $id_list);
    $stmt2->execute();

    $id_pre = $con->insert_id;

    // Insertar recibo_final incluyendo totales calculados (total_kg y total_p)
    $stmt3 = $con->prepare("INSERT INTO recibo_final (encargado, cargo, id_organizacion, id_prerecibo, fecha, total_kg, total_p, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Tipos: s (encargado), s (cargo), i (id_organizacion), i (id_prerecibo), s (fecha), d (total_kg), d (total_p), i (estado)
    $stmt3->bind_param("ssiisddi", $encargado, $cargo, $id_org, $id_pre, $fecha_registro, $total_c, $total_p, $estado);
    $stmt3->execute();


    $con->commit();

    echo "<script>
                alert('Registro Exitoso');
                window.location.href='../vistas/eco_circulante.php'                
              </script>"; 
}catch (Exception $e){

    $con->rollback();
    die("Error al insertar los recibos " . $e->getMessage());
}

$con->close();

?>