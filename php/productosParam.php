<?php
// php/productos.php - VERSIÓN CORREGIDA
header('Content-Type: application/json');
require_once 'main.php';

// Inicializar respuesta
$response = [
    'productos' => []  // Cambiado de array múltiple a solo productos
];

try {
    // Obtener parámetros de búsqueda y filtro
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;

    // Construir consulta base para productos
    $sql = "SELECT 
            p.id_producto AS id, 
            p.nombre, 
            p.descripcion, 
            p.puntos_requeridos AS puntos,
            p.stock,
            p.activo,
            p.id_categoria,
            c.nombre AS categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE 1=1";
    
    $conditions = [];
    $params = [];
    $types = "";

    // Filtrar por búsqueda
    if (!empty($busqueda)) {
        $conditions[] = "(p.nombre LIKE ? OR p.descripcion LIKE ?)";
        $searchTerm = "%" . $busqueda . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    // Filtrar por categoría
    if ($categoria_id > 0) {
        $conditions[] = "p.id_categoria = ?";
        $params[] = $categoria_id;
        $types .= "i";
    }

    // Agregar condiciones si existen
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY p.nombre ASC";

    // Preparar y ejecutar consulta
    if (!empty($params)) {
        $stmt = $con->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $con->query($sql);
    }

    // Procesar resultados
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Asegurar que la categoría tenga nombre
            if (empty($row['categoria_nombre'])) {
                $row['categoria_nombre'] = 'Sin categoría';
            }
            
            // Convertir activo a estado texto
            $row['estado'] = ($row['activo'] == 1) ? 'activo' : 'inactivo';
            
            // Estructura que espera el frontend
            $response['productos'][] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'descripcion' => $row['descripcion'] ?? '',
                'puntos' => $row['puntos'],
                'stock' => $row['stock'],
                'estado' => $row['estado'],
                'categoria_id' => $row['id_categoria'] ?? null,
                'categoria_nombre' => $row['categoria_nombre']
            ];
        }
    }

} catch (Exception $e) {
    $response['error'] = 'Error al consultar la base de datos: ' . $e->getMessage();
}

echo json_encode($response);

if (isset($con) && $con) {
    $con->close();
}
?>