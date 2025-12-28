<?php
// debug_rutas.php - Colócalo en la misma carpeta que eco_circulante.php
echo "<h1>🔍 DEBUG DE RUTAS</h1>";
echo "<pre>";

echo "=== INFORMACIÓN DEL SERVIDOR ===\n";
echo "URL actual: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Directorio: " . __DIR__ . "\n";

echo "\n=== ARCHIVOS EN EL DIRECTORIO ===\n";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file\n";
    }
}

echo "\n=== ARCHIVOS EN CARPETA PHP (si existe) ===\n";
$php_dir = __DIR__ . '/php';
if (is_dir($php_dir)) {
    $php_files = scandir($php_dir);
    foreach ($php_files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- php/$file\n";
        }
    }
} else {
    echo "❌ La carpeta 'php' no existe en: " . $php_dir . "\n";
}

echo "\n=== TEST DE INCLUSIÓN ===\n";
$main_path = __DIR__ . '/php/main.php';
if (file_exists($main_path)) {
    echo "✅ main.php encontrado en: $main_path\n";
    
    // Probar conexión
    require_once $main_path;
    if ($con) {
        echo "✅ Conexión a BD exitosa\n";
        echo "✅ Servidor: " . $con->host_info . "\n";
    } else {
        echo "❌ Error de conexión\n";
    }
} else {
    echo "❌ main.php NO encontrado. Buscado en: $main_path\n";
}

echo "</pre>";
?>