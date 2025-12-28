<?php
include 'main.php';

$sql = "SELECT * FROM personal";
$result = $con->query($sql);

if (!$result) {
    die("Error en la consulta: " . $con->error);
}

while ($row = $result->fetch_assoc()) {
    echo $row['nombre'] . "<br>";
}
?>
