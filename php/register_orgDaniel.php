<?php

	require "main.php";

	$error = [];

	if (empty($_POST['comite'])) {
		$_POST['comite'] = 0;
		$error[] = "Debe seleccionar un comite<br>";
	}
	if (empty($_POST['nombre'])) {
		$error[] = "Debe introducir el nombre de la organizacion<br>";
	}
	if (empty($_POST['ubicacion'])) {
		$error[] = "Debe introducir la ubicacion de la organizacion<br>";
	}
	if (empty($_POST['nombre_responsable'])){
		$error[] = "Debe introducir el nombre del responsable<br>";
	}
	if (empty($_POST['apellido_responsable'])){
		$error[] = "Debe introducir el apellido del responsable<br>";
	}
	if (empty($_POST['cedula_responsable'])) {
		$error[] = "Debe introducir la cedula de identidad del responsable<br>";
	}
	if (empty($_POST['telefono_responsable'])) {
		$error[] = "Debe introducir el numero de telefono del responsable<br>";
	}



		$nombre = $_POST['nombre']; 
		$comite = $_POST['comite'];
		$ubicacion = $_POST['ubicacion'];
		$nResponsable = $_POST['nombre_responsable'];
		$aResponsable = $_POST['apellido_responsable'];
		$responsable = $nResponsable. ' ' .$aResponsable;
		$responsable_cedula = $_POST['cedula_responsable'];
		$responsable_telefono = $_POST['telefono_responsable'];
		$fecha = date("Y-m-d");

		$responsable_cedula = preg_replace('/\D/', '', $responsable_cedula);

	    if (strlen($responsable_cedula) < 7 || strlen($responsable_cedula) > 8) {
		$error[] = "Cédula debe tener 7 u 8 dígitos";
		}
	
		if (!empty($error)) {
			echo implode("<br>", $error);
			exit();
		}else{

	    $sql = "INSERT INTO organizacion(nombre, id_comite, ubicacion, nombre_responsable, cedula_responsable, telefono_responsable, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ssssiss", $nombre, $comite, $ubicacion, $responsable, $responsable_cedula, 
			$responsable_telefono, $fecha);

		if ($stmt->execute()) {
			$exito = "Registro Exitoso";
			echo $exito;	
		}else{
			$fracaso = "Error durante el registro";
			echo $fracaso;
		}
		$stmt->close();
		$con->close();
		}
	
?>