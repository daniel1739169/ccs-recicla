<div class="contenedor_panel" id="panel_org" style="display: none;">
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

    

    <table class="table table-striped table-bordered table-secondary" style="border: 2px solid;">
        <thead style="padding: 2px">
            <th style="text-align: center;">Nombre</th>
            <th style="text-align: center;">Comité</th>
            <th style="text-align: center;">Ubicación</th>
            <th style="text-align: center;">Responsable</th>
            <th style="text-align: center;">Teléfono del responsable</th>
            <th colspan="2" style="text-align: center;"><button class="btn btn-success btn-sm" onclick="registrar_org()">Nueva organización</button></th>
        </thead>
        <tbody>
            
            <?php

            include_once "../php/panel_org.php";

            

            while ($row = mysqli_fetch_array($result_org)) {


                switch ($row['id_comite']) {
                    case '1':
                        $row['id_comite'] = 'COIR';
                        break;
                    case '2';
                        $row['id_comite'] = 'COLOR';
                        break;
                    case '3';
                        $row['id_comite'] = 'Caracas Recicla en mi Escuela';
                        break;
                }

             ?>


                <tr>
                    <td scope="col" style="text-align: center;"><?= $row['nombre']?></td>
                    <td scope="col" style="text-align: center;"><?= $row['id_comite']?></td>
                    <td scope="col" style="text-align: center;"><?= $row['ubicacion']?></td>
                    <td scope="col" style="text-align: center;"><?= $row['nombre_responsable']?></td>
                    <td scope="col" style="text-align: center;"><?= $row['telefono_responsable']?></td>
                    <td scope="col" style="text-align: center;"><a class="btn btn-primary btn-sm" href="../vistas/panel_recibo.php?id=<?=$row['id']?>">Ver recibos</a></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
    

</div>
 