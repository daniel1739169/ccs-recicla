<?php
include "main.php";

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    // Cambiar estado
    $sql_status = "UPDATE personal 
                   SET status = CASE 
                       WHEN status = '1' THEN '0' 
                       WHEN status = '0' THEN '1' 
                       ELSE status END 
                   WHERE id = $id";
    $result = mysqli_query($con, $sql_status);

    if($result){
        // Consultar el nuevo estado
        $res = mysqli_query($con, "SELECT status FROM personal WHERE id = $id");
        $row = mysqli_fetch_assoc($res);

        if($row['status'] == '1'){
            echo '<h5 class="text-success">Activo</h5>';
        } else if ($row['status'] == '0') {
            echo '<h5 class="text-danger">Inactivo</h5>';
        }
            
        
    } else {
        http_response_code(500);
        echo "Error al actualizar";
    }
}
?>
