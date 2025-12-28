<?php


   	$sql_comite = "SELECT descripcion FROM comite";
    $comites = mysqli_query($con, $sql_comite);

    $sql_material = "SELECT descripcion FROM material";
	$materiales = mysqli_query($con, $sql_material);


?>