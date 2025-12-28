            <!-- Input addon -->
            <div class="card card-secondary" id="form_org" style="display: none;">
              <div class="card-header">
                <h1 class="card-title">Organizaciones</h1>
              </div>
              <div class="card-body">
                <form id="orgForm">
                    
                    
        <label>Nombre de la organizacion</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-building"></i>
    </span>

            <input type="text" name="nombre" class="form-control" id="nombre_o" placeholder="Escriba el nombre de la organización" autocomplete="off">
                    
        </div>
            
        <label>Comité</label>
        <div class="input-group mb-3">    
            
                     
            <span class="input-group-text"><i class="far fa-flag"></i></span>
            
                <select name="comite" id="comite_o" class="form-control">
                  
                </select>
            

        </div>
        <label>Ubicación de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
            <input type="text" name="ubicacion" class="form-control" id="ubicacion_o" placeholder="Escriba la ubicación exacta de la organizacion" autocomplete="off">
        </div>

        <label>Nombre del responsable de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>            
            <input type="text" name="nombre_responsable" class="form-control" id="responsable_n" placeholder="Escriba el nombre del responsable" autocomplete="off">
        </div>

        <label>Apellido del responsable de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>            
            <input type="text" name="apellido_responsable" class="form-control" id="responsable_a" placeholder="Escriba el apellido del responsable" autocomplete="off">
        </div>

        <label>Cédula del responsable</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-address-card"></i></span>
            <input type="int" name="cedula_responsable" id="responsable_c" class="form-control" placeholder="Escriba la cédula del responsable" autocomplete="off">
        </div>

        <label>Teléfono del responsable</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa fa-phone"></i></span>
            <input type="text" name="telefono_responsable" id="responsable_t" placeholder="Escriba el teléfono del responsable" class="form-control" autocomplete="off">
        </div>

        <button type="submit" name="btn_register_organization" class="btn btn-success">Registrar</button>
        <button type="button" id="salir_form_org" class="btn btn-danger" onclick="cancelar_formOrg()" >Salir del formulario</button>

        <div id="alertOrg"></div>

        </form>
    </div>
</div>

<script>
function alertOrg(mensaje, tipo = 'danger', tiempo = 5000){
  const alertContainer = document.getElementById('alertOrg');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

function alertOrgpo(mensaje, tipo = 'success', tiempo = 5000){
  const alertContainer = document.getElementById('alertOrg');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>El envío fue realizado con éxito:</strong><br> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

document.addEventListener("DOMContentLoaded", function () {
  const form_org = document.getElementById("orgForm");

  form_org.addEventListener("submit", function (evento) {
    evento.preventDefault();

    const datosFormulario = new FormData(form_org);
    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
      if (xhr.status === 200) {
        let respuesta = JSON.parse(xhr.responseText);

        if (respuesta.status === "error") {
          for (let campo in respuesta.errores) {
            const elemento = document.getElementById(campo);
            if (elemento) {
              const originalValue = elemento.value; // capturar valor original
              elemento.className = 'form-control border border-danger text-danger';
              elemento.value = respuesta.errores[campo]; // mostrar mensaje temporal
              elemento.style.pointerEvents = 'none';

              setTimeout(function(){
                elemento.className = 'form-control'; // restaurar clase original
                elemento.style.pointerEvents = '';
                elemento.value = originalValue; // restaurar valor original
              }, 3000);
            }
          }
        } else if (respuesta.status === "success") {
          alertOrgpo(respuesta.message);
          form_org.reset();
        } else {
          alertOrg('Registro fallido');
        }
      } else {
        alertOrg('Error de conexión con el servidor');
      }
    };

    xhr.open("POST", "./php/register_org.php", true);
    xhr.send(datosFormulario);
  });
});
</script>

