<?php
header('Content-Type: application/json; charset=utf-8');

	require "main.php";

		$nombre = $_POST['nombre']; 
		$comite = $_POST['comite'];
		$ubicacion = $_POST['ubicacion'];
		$nResponsable = $_POST['nombre_responsable'];
		$aResponsable = $_POST['apellido_responsable'];
		$responsable_cedula = $_POST['cedula_responsable'];
		$responsable_telefono = $_POST['telefono_responsable'];
		$fecha = date("Y-m-d");

	$errores = [];

	if (empty($nombre)) {
		$errores['nombre_o'] = "Debe introducir el nombre de la organizacion";
	}
	if (empty($comite)) {
		$errores['comite_o'] = "";
	}
	if (empty($ubicacion)) {
		$errores['ubicacion_o'] = "Debe introducir la ubicacion de la organizacion";
	}
	if (empty($nResponsable)){
		$errores['responsable_n'] = "Debe introducir el nombre del responsable";
	}
	if (empty($aResponsable)){
		$errores['responsable_a'] = "Debe introducir el apellido del responsable";
	}
	$responsable = $nResponsable. ' ' .$aResponsable;
		
	if (empty($responsable_cedula)) {
		$errores['responsable_c'] = "Debe introducir la cedula de identidad del responsable";
	}
	if (empty($responsable_telefono)) {
		$errores['responsable_t'] = "Debe introducir el numero de telefono del responsable";
	}

	if (strlen($responsable_telefono) > 0 && strlen($responsable_telefono) > 11) {
		$errores['responsable_t'] = "El numero de telefono debe ser de 12 digitos";
	}

	# Consultar si el usuario ya existe (con cedula)
	$stmt0 = $con->prepare("SELECT nombre FROM organizacion WHERE nombre = ?");
	$stmt0->bind_param("s", $nombre);
	$stmt0->execute();
	$stmt0->store_result();
	if($stmt0->num_rows > 0) {
	    $errores['nombre_o'] = "La organizacion ya esta registrada";
	    $stmt0->close();
	}




	if (!empty($errores)) {
    	echo json_encode([
	        "status" => "error",
	        "errores" => $errores
	    ]);
	    exit;
	}


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

		//Devolver success en JSON
        echo json_encode([
            "status" => "success",
            "message" => "Organizacion registrada correctamente"]);
		}else{
					}
		$stmt->close();
		$con->close();
		}
	
?>