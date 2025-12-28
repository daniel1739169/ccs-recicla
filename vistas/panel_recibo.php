<div class="card card-success" style="display:none" id="panel_re">
    <div class="card-header">
        <h1 class="card-title" id="titulo_recibos">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Recibos
        </h1>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div id="btn-crear_pre" class="p-3"></div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-success" style="background-color: #28a745!important;">
                    <tr>
                        <th style="width: 100px; text-align: center; color: white;">Nº de recibo</th>
                        <th style="color: white;">Encargado</th>
                        <th style="color: white;">Fecha</th>
                        <th style="color: white;">Estado de correlación</th>
                        <th style="color: white;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Aquí deberías incluir tu consulta PHP para traer los recibos
                    // Ejemplo:
                    /*
                    include "./php/ver_recibos.php";
                    while ($recibo = mysqli_fetch_array($result_recibos)) {
                        echo '<tr>';
                        echo '<td class="text-center" style="border-left: 3px solid #28a745;">
                                <span class="badge bg-success">'.$recibo['id'].'</span>
                              </td>';
                        echo '<td><i class="fas fa-user text-success mr-2"></i>'.$recibo['encargado'].'</td>';
                        echo '<td>'.$recibo['fecha'].'</td>';
                        echo '<td>'.$recibo['estado'].'</td>';
                        echo '<td class="text-center">
                                <button class="btn btn-primary btn-sm" onclick="verDetalleRecibo('.$recibo['id'].')">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </button>
                              </td>';
                        echo '</tr>';
                    }
                    */
                    ?>
                </tbody>
            </table>
        </div>

        <div class="p-3">
            <button class="btn btn-danger btn-sm" onclick="salir_panelRe()">
                <i class="fas fa-arrow-left mr-1"></i>Volver a organizaciones
            </button>
        </div>
    </div>
</div>
