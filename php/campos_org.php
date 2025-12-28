<?php
require "main.php";

header('Content-Type: application/json; charset=utf-8');

$comites_campos = "SELECT * FROM comite";
$comites_actuales = mysqli_query($con, $comites_campos);

if (!$comites_actuales) {
    echo json_encode(["error" => mysqli_error($con)]);
    exit;
}

$comites = [];

while ($row = mysqli_fetch_assoc($comites_actuales)) {
    $comites[] = $row;
}

echo json_encode([
    "comites" => $comites
]);
?>
