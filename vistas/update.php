              
              <div class="card card-primary" id="form_update" style="display: none">

                <div class="card-header">
                  <h1 class="card-title">Actualizacion de personal</h1>
                </div>

                <div class="card-body">

    <form id="updateForm">
    
      <input type="hidden" name="id" id="x">

        <div class="input-group mb-3">
         
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-flag"></i></span>
          </div>

            <select class="form-control" name="nac_actualizar" id="nacionalidad_u">

              <option value="" disabled selected>Seleccione la nacionalidad del personal</option>
              <option value="V">V</option>
              <option value="E">E</option>
            </select>

              <div class="input-group-prepend">
                <span class="input-group-text"><i class="far fa-address-card"></i></span>
              </div>
                <input type="text" class="form-control" name="cedula_actualizar" id="cedula_u" placeholder="Ingrese la cedula de identidad del personal" readonly>
        </div>
          <label>Fecha de nacimiento</label>
          <div class="input-group mb-3">
            <input type="date" class="form-control" name="fecha_actualizar" id="fecha_nac_u">
          </div>
        


        <label>Nombre</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
          </div>
          <input type="int" class="form-control" name="nombre_actualizar" id="nombre_u" placeholder="Ingresa el nombre del personal">
        </div>
        <label>Apellido</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
          </div>
          <input type="int" class="form-control" name="apellido_actualizar" id="apellido_u" placeholder="Ingresa el apellido del personal">
        </div>

        <label>Gerencia</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
          </div>

                <select class="form-control" name="gerencia_actualizar" id="gerencia_u">
                  <option value="" disabled selected>Seleccione la gerencia correspondiente al personal</option>
                  <option value="1">Gestion Interna</option>
                  <option value="2">Consultoria Juridica</option>
                  <option value="3">Operaciones</option>
                  <option value="4">Gestion Comercial</option>
                </select>
        </div>

        <label>Division</label>
        <div class="input-group mb-3">
         
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
          </div>

                <select class="form-control" name="division_actualizar" id="division_u">
                  <option value="" disabled selected>Seleccione la division correspondiente al personal</option>
                  <option value="1">Administracion y Finanzas</option>
                  <option value="2">Gestion Humana</option>
                  <option value="3">Seguridad Integral</option>
                  <option value="4">Planificacion y Presupuesto</option>
                  <option value="5">Tecnologias de la Informacion y Comunicacion</option>
                  <option value="6">Gestion Comunicacional</option>
                  <option value="7">Servicios</option>
                  <option value="8">Recoleccion</option>
                  <option value="9">Comercializacion</option>
                  <option value="10">Economia Circulante</option>
                </select>
        </div>

        <label>Rol</label>
        <div class="input-group mb-3">
         
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
          </div>
                <select class="form-control" name="rol_actualizar" id="rol_u">

                  <option value="" disabled selected>Seleccione el rol correspondiente al personal</option>
                  <option value="1">Administrador</option>
                  <option value="2">Gerente</option>
                  <option value="3">Promotor</option>
                </select>
        </div>

        <label>Correo electronico</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i>@</i></span>
          </div>
          <input type="int" class="form-control" name="correo_actualizar" id="correo_u" placeholder="Ingresa el nuevo correo electronico del personal">
        </div>

       <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-append">
            <button type="submit" class="btn btn-primary btn-flat">Actualizar</button>
          </span>
        </div>

        <div class="input-group-prepend">
          <span class="input-group-append">
            <button type="button" onclick="cancelar_actualizar()" name="btn_cancelar" class="btn btn-danger btn-flat">Cancelar</button>
          </span> 
        </div>
      </div>
      <div id="alertUpdate"></div>
    </form>

  </div>
</div>

<script>
function alertUpd(mensaje, tipo = 'danger', tiempo = 5000){
  const alertContainer = document.getElementById('alertUpdate');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

function alertUpdpo(mensaje, tipo = 'success', tiempo = 5000){
  const alertContainer = document.getElementById('alertUpdate');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>El envío fue realizado con éxito:</strong><br> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

document.addEventListener("DOMContentLoaded", function () {
  const form_upd = document.getElementById("updateForm");

  form_upd.addEventListener("submit", function (evento) {
    evento.preventDefault();

    const datosUpdate = new FormData(form_upd);
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
          alertUpdpo(respuesta.message);
        } else if (respuesta.status === "alert") {
          alertUpd(respuesta.alerta);
        }
      }
    };

    xhr.open("POST", "./php/register_update.php", true);
    xhr.send(datosUpdate);
  });
});
</script>
