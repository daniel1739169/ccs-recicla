<?php
// get_visitas.php
require 'main.php';
header('Content-Type: application/json');

$sql = "
    SELECT 
        v.id,
        v.fecha,
        v.responsable,
        v.cargo,
        v.id_organizacion,
        v.id_promotor,
        o.nombre AS organizacion_nombre,
        o.id_comite,
        c.descripcion AS comite_nombre,
        CONCAT(p.nombre, ' ', p.apellido) AS promotor_nombre
    FROM visitas v
    JOIN organizacion o ON v.id_organizacion = o.id
    JOIN comite c ON o.id_comite = c.id
    LEFT JOIN personal p ON v.id_promotor = p.id
";

$resultado = mysqli_query($con, $sql);

if ($resultado) {
    $visitas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    $eventos = [];
    
    // Este bucle PHP para formatear los eventos es igual
    foreach ($visitas as $visita) {
        $title = $visita['organizacion_nombre'] . ' (' . $visita['responsable'] . ')';
        if ($visita['promotor_nombre']) {
            $title .= ' - ' . $visita['promotor_nombre'];
        }
        $eventos[] = [
            'id' => $visita['id'],
            'title' => $title,
            'start' => $visita['fecha'], // FullCalendar maneja DATETIME
            'extendedProps' => [
                'id_organizacion' => $visita['id_organizacion'],
                'id_comite' => $visita['id_comite'],
                'id_promotor' => $visita['id_promotor'],
                'responsable' => $visita['responsable'],
                'cargo' => $visita['cargo']
            ]
        ];
    }
    echo json_encode($eventos);

} else {
    echo json_encode(['error' => 'Error al cargar visitas: ' . mysqli_error($con)]);
}
?>
