<?php

	$destino_d = $_POST['destino_d'];
	$num_archivos=count($_FILES['archivo']['name']);
	

	//CONTEO DE ARCHIVOS
	for ($i=0; $i <=$num_archivos; $i++) {

		if ( !empty($_FILES['archivo']['name'][$i]) ) {
			$ruta_nueva = "uploads/".$_FILES['archivo']['name'][$i];

		//CAMBIO DE RUTA DE DESTINO
		switch ($destino_d) {
		case 'Recoleccion':
			$ruta_nueva = "../arch_operaciones/".$_FILES['archivo']['name'][$i];
			break;
		case 'Comercializacion':
			$ruta_nueva = "../arch_gestion_com/".$_FILES['archivo']['name'][$i];
			break;
		}

			if (file_exists($ruta_nueva)) {
				echo "El archivo ".$_FILES['archivo']['name'][$i]." ya se encuentra en el servidor<br>";
			}else{
				$ruta_temporal = $_FILES['archivo']['tmp_name'][$i];
				move_uploaded_file($ruta_temporal, $ruta_nueva);
				echo "El archivo ".$_FILES['archivo']['name'][$i]." se subio de manera exitosa<br>";
				}
		}
	}
	

?>