<!-- Input addon -->
<div class="card card-success" id="moAdmin" style="display: none;">
  <div class="card-header">
    <h1 class="card-title">
      Modulo administrativo
    </h1>
  </div>
  <div class="card-body">

    <!-- Comités -->
    <div class="card card-outline card-success mb-4">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-users mr-2"></i>Comités
        </h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Buscador Comités -->
        <div class="input-group mb-3">
          <input type="text" id="searchComites" class="form-control" placeholder="Buscar comité...">
          <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
        </div>

        <?php include "./php/leer_param.php" ?>
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0" id="tablaComites">
            <thead class="bg-success" style="background-color: #28a745!important;">
              <tr>
                <th style="width: 60px; text-align: center; color: white;">#</th>
                <th style="color: white;">Nombre del Comité</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $id0 = 1;
              foreach ($comites as $c_actual) {
                  echo '<tr>';
                  echo '<td class="text-center" style="border-left: 3px solid #28a745;"><span class="badge bg-success">' . $id0++ . '</span></td>';
                  echo '<td><i class="fas fa-user-friends text-success mr-2"></i>' . $c_actual['descripcion'] . '</td>';
                  echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Materiales -->
    <div class="card card-outline card-success mb-4">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-boxes mr-2"></i>Materiales
        </h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Buscador Materiales -->
        <div class="input-group mb-3">
          <input type="text" id="searchMateriales" class="form-control" placeholder="Buscar material...">
          <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0" id="tablaMateriales">
            <thead class="bg-success" style="background-color: #28a745!important;">
              <tr>
                <th style="width: 60px; text-align: center; color: white;">#</th>
                <th style="color: white;">Nombre del Material</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $id1 = 1;
              foreach ($materiales as $m_actual) {
                  echo '<tr>';
                  echo '<td class="text-center" style="border-left: 3px solid #28a745;"><span class="badge bg-success">' . $id1++ . '</span></td>';
                  echo '<td><i class="fas fa-box text-success mr-2"></i>' . $m_actual['descripcion'] . '</td>';
                  echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <button class="btn btn-success btn-lg" type="button" onclick="mostrar_formMoadmin()" name="actualizar_param">
        <i class="fas fa-sync-alt mr-2"></i>Agregar
      </button>
    </div>

  </div>
</div>

<script src="/dist/js/demo.js"></script>
<script src="plugins/jquery/jquery.min.js"></script>

<!-- Page specific script -->
<script>
  // Buscador para Comités
  $('#searchComites').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#tablaComites tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

  // Buscador para Materiales
  $('#searchMateriales').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#tablaMateriales tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
</script>
