<?php
// tabla_visitas.php - VERSIÓN CORREGIDA
// Incluir conexión desde main.php
include_once "./php/main.php";

// Verificar conexión
if (!$con) {
    die('<div class="alert alert-danger">Error de conexión a la base de datos</div>');
}
?>

<div class="card card-primary" id="panel_tablaVisitas" style="display:none;">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-table mr-2"></i>Tabla de Visitas
    </h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="card-body p-0">

    <!-- Buscador de organizaciones -->
    <div class="input-group p-3">
      <input type="text" id="searchVisitasOrg" class="form-control" placeholder="Buscar organización...">
      <div class="input-group-append">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-striped mb-0" id="tablaVisitas">
        <thead class="bg-primary" style="background-color: #007bff!important;">
          <tr>
            <th style="text-align: center; color: white;">#</th>
            <th style="color: white;">Organización</th>
            <th style="color: white;">Responsable</th>
            <th style="color: white;">Cargo</th>
            <th style="color: white;">Fecha</th>
            <th style="color: white;">Estado</th>
            <th style="color: white;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Consulta directa en este archivo (sin ver_visitas.php)
          $sql_v = "SELECT v.*, o.nombre as nombre_organizacion 
                   FROM visitas v 
                   LEFT JOIN organizacion o ON v.id_organizacion = o.id 
                   ORDER BY v.fecha DESC";  
          
          $result_visitas = mysqli_query($con, $sql_v);
          
          if ($result_visitas && mysqli_num_rows($result_visitas) > 0) {
              $i = 1;
              while ($visita = mysqli_fetch_array($result_visitas)) {
                  echo '<tr>';
                  echo '<td class="text-center" style="border-left: 3px solid #007bff;">
                          <span class="badge bg-primary">'.$i++.'</span>
                        </td>';
                  echo '<td><i class="fas fa-building text-primary mr-2"></i>'.$visita['nombre_organizacion'].'</td>';
                  echo '<td><i class="fas fa-user text-primary mr-2"></i>'.$visita['responsable'].'</td>';
                  echo '<td>'.$visita['cargo'].'</td>';
                  echo '<td><span class="badge bg-info">'.date('d-m-Y H:i', strtotime($visita['fecha'])).'</span></td>';
                  
                  // Estado
                  $estado = $visita['estado'];
                  $estadoTexto = 'Pendiente';
                  $estadoClase = 'warning';
                  
                  if ($estado == 1) {
                      $estadoTexto = 'Realizada';
                      $estadoClase = 'success';
                  } elseif ($estado == 2) {
                      $estadoTexto = 'Cancelada';
                      $estadoClase = 'danger';
                  }
                  
                  echo '<td><span class="badge badge-'.$estadoClase.'">'.$estadoTexto.'</span></td>';
                  
                  echo '<td class="text-center">
                          <button class="btn btn-sm btn-success" onclick="verDetalleVisita('.$visita['id'].')" data-toggle="modal" data-target="#modalDetalleVisita">
                            <i class="fas fa-eye mr-1"></i> Ver
                          </button>
                        </td>';
                  echo '</tr>';
              }
          } else {
              echo '<tr><td colspan="7" class="text-center">No hay visitas registradas</td></tr>';
          }
          
          if ($result_visitas) {
              mysqli_free_result($result_visitas);
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal para detalles de visita -->
<div class="modal fade" id="modalDetalleVisita" tabindex="-1" role="dialog" aria-labelledby="modalDetalleVisitaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de Visita</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detalleContenido">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#searchVisitasOrg').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#tablaVisitas tbody tr').filter(function() {
      // Solo filtra por la columna de Organización (2da columna)
      var org = $(this).find('td:nth-child(2)').text().toLowerCase();
      $(this).toggle(org.indexOf(value) > -1);
    });
  });
});
</script>

<script src="/dist/js/demo.js"></script>
<script src="plugins/jquery/jquery.min.js"></script>


