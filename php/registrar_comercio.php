<?php
require("main.php"); // Archivo donde tienes la conexión ($con) y funciones como limpiar_cadena()

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Limpiar y recibir los datos del formulario
    $nombre = limpiar_cadena($_POST['nombre']);
    $ubicacion = limpiar_cadena($_POST['ubicacion']);
    $nombre_responsable = limpiar_cadena($_POST['nombre_responsable']);
    $cedula_responsable = limpiar_cadena($_POST['cedula_responsable']);
    $telefono_responsable = limpiar_cadena($_POST['telefono_responsable']);
    $tipo = limpiar_cadena($_POST['tipo']);

    // Validación básica
    if(empty($nombre) || empty($ubicacion) || empty($nombre_responsable) || empty($cedula_responsable) || empty($tipo)){
        echo "<script>alert('Por favor complete todos los campos obligatorios.'); history.back();</script>";
        exit();
    }

    // Verificar si ya existe un comercio o institución con la misma cédula de responsable
    $check = $con->prepare("SELECT id_comercio FROM comercio_institucion WHERE cedula_responsable = ?");
    $check->bind_param("s", $cedula_responsable);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Ya existe un registro con esta cédula de responsable.'); history.back();</script>";
        exit();
    }

    // Insertar los datos
    $stmt = $con->prepare("INSERT INTO comercio_institucion 
        (nombre, ubicacion, nombre_responsable, cedula_responsable, telefono_responsable, tipo) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $ubicacion, $nombre_responsable, $cedula_responsable, $telefono_responsable, $tipo);

    if ($stmt->execute()) {
        echo "<script>alert('Registro guardado exitosamente.'); window.location.href='../vistas/eco_circulante.php';</script>";
    } else {
        echo "<script>alert('Error al guardar el registro. Intente nuevamente.'); history.back();</script>";
    }

    $stmt->close();
    $con->close();
}
?>