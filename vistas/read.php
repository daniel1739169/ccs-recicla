<div class="card card-success" id="lista_usuarios" style="display: none;">
  <div class="card-header">
    <h1 class="card-title">
      <i class="fas fa-users mr-2"></i>Listado de personal
    </h1>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="card-body">
    <!-- Buscador -->
    <div class="input-group mb-3">
      <input type="text" id="searchUsuarios" class="form-control" placeholder="Buscar por cédula, nombre o apellido...">
      <div class="input-group-append">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-striped mb-0" id="tablaUsuarios">
        <thead class="bg-success" style="background-color: #28a745!important;">
          <tr>
            <th style="color: white;">#</th>
            <th style="color: white;">Cédula</th>
            <th style="color: white;">Nacimiento</th>
            <th style="color: white;">Nombre</th>
            <th style="color: white;">Apellido</th>
            <th style="color: white;">Rol</th>
            <th style="color: white;">Gerencia</th>
            <th style="color: white;">División</th>
            <th style="color: white;">Status</th>
            <th style="color: white;">Cambiar</th>
            <th style="color: white;">Actualizar</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include_once "./php/read_user.php";
          $i= 1;

          while ($row = mysqli_fetch_array($result)) {
              // Traducciones de gerencia
              switch($row['id_gerencia']){
                  case "1": $row['id_gerencia'] = 'Gestión Interna'; break;
                  case "2": $row['id_gerencia'] = 'Consultoría Jurídica'; break;
                  case "3": $row['id_gerencia'] = 'Operaciones'; break;
                  case "4": $row['id_gerencia'] = 'Gestión Comercial'; break;
              }

              // Traducciones de división
              switch($row['id_division']){
                  case "1": $row['id_division'] = 'Administración'; break;
                  case "2": $row['id_division'] = 'Gestión Humana'; break;
                  case "3": $row['id_division'] = 'Seguridad Integral'; break;
                  case "4": $row['id_division'] = 'Planificación y Presupuesto'; break;
                  case "5": $row['id_division'] = 'Tecnología'; break;
                  case "6": $row['id_division'] = 'Gestión Comunicacional'; break;
                  case "7": $row['id_division'] = 'Servicios'; break;
                  case "8": $row['id_division'] = 'Recolección'; break;
                  case "9": $row['id_division'] = 'Comercialización'; break;
                  case "10": $row['id_division'] = 'Economía Circulante'; break;
              }

              // Traducciones de rol
              switch($row['id_rol']){
                  case "1": $row['id_rol'] = 'Administrador'; break;
                  case "2": $row['id_rol'] = 'Gerente'; break;
                  case "3": $row['id_rol'] = 'Promotor'; break;
              }

              // Traducciones de status
              switch($row['status']){
                  case "1": $row['status'] = '<h5 class="text-success">Activo</h5>'; break;
                  case "0": $row['status'] = '<h5 class="text-danger">Inactivo</h5>'; break;
                  case "2": $row['status'] = '<h5 class="text-warning">Verificar</h5>'; break;
              }
          ?>
            <tr>
              <td><?= $i++?></td>
              <td><?= $row['nacionalidad']?> <?= $row['cedula']?></td>
              <td><?= $row['fecha_nac']?></td>
              <td><?= $row['nombre']?></td>
              <td><?= $row['apellido']?></td>
              <td><?= $row['id_rol']?></td>
              <td><?= $row['id_gerencia']?></td>
              <td><?= $row['id_division']?></td>
              <td class="estado"><?= $row['status']?></td>
              <td>
                <button type="button" class="btn btn-warning btn-sm btn-status" data-id="<?= $row['id']?>">Cambiar</button>
              </td>
              <td>
                <button type="button" onclick="mostrar_actualizar()" class="btn btn-primary btn-sm btn-actualizar" data-id="<?= $row['id']?>">Actualizar</button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // Buscador por cédula, nombre y apellido
  document.getElementById('searchUsuarios').addEventListener('keyup', function(){
    var value = this.value.toLowerCase();
    document.querySelectorAll('#tablaUsuarios tbody tr').forEach(function(row){
      var cedula = row.cells[1].textContent.toLowerCase();
      var nombre = row.cells[3].textContent.toLowerCase();
      var apellido = row.cells[4].textContent.toLowerCase();
      if(cedula.indexOf(value) > -1 || nombre.indexOf(value) > -1 || apellido.indexOf(value) > -1){
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });

  // Mantienes tu lógica de botones Cambiar y Actualizar igual
  const botones = document.querySelectorAll('.btn-status');
  document.querySelectorAll('tr').forEach(fila => {
    const estadoCelda = fila.querySelector('.estado');
    const boton = fila.querySelector('.btn-status');
    if (estadoCelda && estadoCelda.textContent.trim() === 'Verificar') {
      if (boton) boton.disabled = true;
    }
  });

  botones.forEach(function(boton){
    boton.addEventListener('click', function(evento){
      evento.preventDefault();
      const id = boton.getAttribute('data-id');
      const datosFormulario = new FormData();
      datosFormulario.append('id', id);
      const xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function(){
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            const fila = boton.closest('tr');
            fila.querySelector('.estado').innerHTML = xhr.responseText;
          }
        }
      };
      xhr.open('POST', './php/on_off_user.php', true);
      xhr.send(datosFormulario);
    });
  });

  const botonU = document.querySelectorAll('.btn-actualizar');
  botonU.forEach(function(update){
    update.addEventListener('click', function(){
      const id = this.getAttribute('data-id');
      const datos = new FormData();
      datos.append('id', id);
      fetch('./php/update_user.php', {
        method: 'POST',
        body: datos
      })
      .then(respuesta => respuesta.json())
      .then(data => {
        document.getElementById("x").value = data.personal.id;
        document.getElementById("nacionalidad_u").value = data.personal.nacionalidad;
        document.getElementById("cedula_u").value = data.personal.cedula;
        document.getElementById("fecha_nac_u").value = data.personal.fecha_nac;
        document.getElementById("nombre_u").value = data.personal.nombre;
        document.getElementById("apellido_u").value = data.personal.apellido;
        document.getElementById("gerencia_u").value = data.personal.id_gerencia;
        document.getElementById("division_u").value = data.personal.id_division;
        document.getElementById("rol_u").value = data.personal.id_rol;
        document.getElementById("correo_u").value = data.personal.correo;
      });
    });
  });
});
</script>

<script src="/dist/js/demo.js"></script>
<script src="plugins/jquery/jquery.min.js"></script>

