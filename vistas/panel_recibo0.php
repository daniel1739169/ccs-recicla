<!DOCTYPE html>
<html>
<head>
    <title>Economia Circulante</title>
    <link rel="stylesheet" href="../css/estilos_vistas.css">
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
    <style>
        body{
            min-height: 100vh;
            overflow-y: auto;
            background-image: url("../img/fondo.png"); /* Reemplaza con la URL de tu imagen */
            background-repeat: no-repeat;
            background-position: 100% 50%;
            background-attachment: fixed;
            background-blend-mode: multiply;
            background-size: 80% 100%;
        }   
    </style>

<body>
<div class="contenedor_panel_recibo" id="panel_recibo">
    <style>

        th{
            padding: 15px;
        }

        tr{
            padding: 15px;
        }

        td{
            padding: 15px;
        }

    </style>
<table class="table table-striped table-bordered table-secondary" style="border: 1px solid;">   
    <thead>
        <tr>
            <th style="text-align: center;">Correlativo</th>
            <th style="text-align: center;">Encargado</th>
            <th style="text-align: center;">Fecha</th>
            <th style="text-align: center;">Estado de correlación</th>
            <th colspan="3" style="text-align: center;">
                <button class="btn btn-success btn-sm" onclick="registrar_prerecibo()">Nuevo prerecibo</button>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        include_once "../php/leer_correlacion.php";

        while ($correlacion = mysqli_fetch_array($result_r)) {
            $id = $correlacion['id'];
            switch ($correlacion['estado']) {
                case '0':
                    $estado = '<h5 class="text-danger">Incompleta</h5>';
                    break;
                case '1':
                    $estado = '<h5 class="text-success">Completa</h5>';
                    break;
            }
        ?>
        
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>



            <td colspan="2" style="text-align: center;">
                <button class="btn btn-warning btn-sm">Detalles de material</button>
            </td>
            <td style="text-align: center;">
                <a class="btn btn-primary btn-sm" href="../vistas/formulario_refinal.php?id=<?= $id ?>">Recibo Final</a>
            </td>
        </tr>

        <!-- Fila oculta con detalles -->
        <tr style="display: none;">
            <td colspan="7">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>TIPO DE MATERIAL RECIBIDO</th>
                            <th>CANTIDAD KG</th>
                            <th>PUNTOS</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <tr>
                            <td><strong>TOTALES</strong></td>
                            <td><strong></strong></td>
                            <td><strong></strong></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Script para mostrar/ocultar detalles -->

</div>

    <?php include "formulario_recibo.php";?>


    <script>
        function registrar_prerecibo(){
            var form_recibo = document.getElementById("form_recibo");
            var panel = document.getElementById("panel_recibo");

            if (form_recibo.style.display === "none" || panel.style.display === "block") {
                form_recibo.style.display = "block";
                panel.style.display = "none";
            }
        }
    </script>    