            <!-- Input addon -->
            <div class="card card-success" style="display: none;" id="form_registro">
              <div class="card-header">
                <h1 class="card-title">Personal</h1>
              </div>
              <div class="card-body">
                <form id="registroForm">
                <div class="input-group mb-3">
                 
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-flag"></i></span>
                  </div>

                        <select class="form-control" name="nacionalidad" id="nacionalidad">
                          <option value="" disabled selected>Seleccione la nacionalidad del personal</option>
                          <option>V</option>
                          <option>E</option>
                        </select>

                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-address-card"></i></span>
                  </div>
                  <input type="int" class="form-control" name="cedula" id="cedula" placeholder="Ingrese la cedula de identidad del personal">
                </div>

                  <label>Fecha de nacimiento</label>
                  <div class="input-group mb-3">
                    <input type="date" class="form-control" id="fecha_nac" name="fecha_nac">
                  </div>

                <label>Nombre</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
                  </div>
                  <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingresa el nombre del personal">
                </div>


                <label>Apellido</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
                  </div>
                  <input type="int" class="form-control" name="apellido" placeholder="Ingresa el apellido del personal" id="apellido">
                </div>

                <label>Gerencia</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                  </div>

                        <select class="form-control" name="gerencia" id="gerencia">
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

                        <select class="form-control" name="division" id="division">
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
                        <select class="form-control" name="rol" id="rol">
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
                  <input type="email" class="form-control" name="correo" id="correo_usu" placeholder="Ingrese el correo electronico del personal">
                </div>

                <div class="input-group input-group-sm">
                  <span class="input-group-append">
                    <button type="submit" name="btn_registrar" class="btn btn-success btn-flat">Registrar</button>
                  </span>
                </div>
               <div id="alertRegister"></div>
              </form>
            </div>
          </div>



<script>
function alertReg(mensaje, tipo = 'danger', tiempo = 5000){
  const alertContainer = document.getElementById('alertRegister');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

function alertRegpo(mensaje, tipo = 'success', tiempo = 5000){
  const alertContainer = document.getElementById('alertRegister');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>El envío fue realizado con éxito:</strong><br> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

document.addEventListener("DOMContentLoaded", function () {
  const form_reg = document.getElementById("registroForm");

  form_reg.addEventListener("submit", function (evento) {
    evento.preventDefault();

    const datosFormulario = new FormData(form_reg);
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
          alertRegpo(respuesta.message);
          form_reg.reset();
        } else if (respuesta.status === "alert"){
          alertReg(respuesta.alerta);
        }
      } else {
        alertReg('Error de conexión con el servidor');
      }
    };

    xhr.open("POST", "./php/register_user.php", true);
    xhr.send(datosFormulario);
  });
});
</script>



