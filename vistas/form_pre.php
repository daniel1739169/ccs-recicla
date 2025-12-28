
            <!-- Input addon -->
            <div class="car card-secondary" id="form_prerecibo" style="display: none">


              <div class="card-header">
                <h1 class="card-title">Prerecibo</h1>
              </div>
              <div class="card-body">
                <form id="formPre">

                  <input type="hidden" name="visita" id="visita">

                  <label>Organización</label>
                  <div class="input-group mb-3">  
                      <span class="input-group-text"><i class="far fa-building"></i></span>
                      <input type="text" class="form-control" 
                      name="organizacion" id="org_prerecibo" readonly>
                  </div>

                  <label>Fecha de visita</label>
                  <div class="input-group mb-3">  
                      <span class="input-group-text"><i class="far fa-calendar"></i></span>
                      <select class="form-control" 
                      name="fecha_pre" id="fecha_prerecibo"></select>
                  </div>
                            
                  <label>Responsable de la visita</label>
                  <div class="input-group mb-3">
                      <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>
                      <input class="form-control" type="text" name="responsable" id="responsable" placeholder="Escriba el nombre del responsable" required autocomplete="off" readonly>
                  </div>

                  <label>Cargo del responsable de la visita</label>    
                  <div class="input-group mb-3">
                      <span class="input-group-text"><i class="fas fa-solid fa-suitcase"></i></span>
                      <input class="form-control" type="text" name="cargo" id="cargo" placeholder="Escriba el cargo del responsable" required autocomplete="off" readonly>
                  </div>

                  <table id="materiales_pre">
                    <thead>
                      <tr>
                        <th>Tipo de material</th>
                        <th>Cantidad (Kg)</th>
                        <th>Puntos</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_materiales_pre">
                      
                    </tbody>


                  </table><br>
              <label>Total de Material (Kg):</label>
                <input type="number" class="form-control" name="total_cantidad" id="total_cantidad_pre" step="0.01" readonly value="0">
              
              <label>Total de Puntos:</label> 

                <input type="number" class="form-control" name="total_puntos" id="total_puntos_pre" step="1" min="0" readonly value="0">
              
              <button type="submit" class="btn btn-success btn-sm">Crear</button>

              <button type="button" class="btn btn-danger btn-sm" id="cancelarFormpre" onclick="cancelar_formPre()">Volver a recibos</button>

                      <div id="alertContainer_pre"></div>

                </form>
            </div>

          </div>


<script>
function alertPre(mensaje, tipo = 'danger', tiempo = 5000){
  const alertContainer = document.getElementById('alertContainer_pre');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

function alertPrepo(mensaje, tipo = 'success', tiempo = 5000){
  const alertContainer = document.getElementById('alertContainer');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>El envío fue realizado con éxito:</strong><br> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

document.addEventListener("DOMContentLoaded", function () {
  const formPre = document.getElementById("formPre");

  formPre.addEventListener("submit", function (evento) {
    evento.preventDefault();

    const datosFormulario = new FormData(formPre);
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
          alertPrepo(respuesta.message);
          formPre.reset();
          mostrar_org();
        } else if (respuesta.status === "alert"){
          alertPre(respuesta.alerta);
        }
      } else {
        alertPre('Error de conexión con el servidor');
      }
    };

    xhr.open("POST", "./php/reg_re1.php", true);
    xhr.send(datosFormulario);
  });
});
</script>


          <!--script>

            document.addEventListener('DOMContentLoaded', function(){
              const form_pre = document.getElementById('formPre');

              form_pre.addEventListener('submit', function(regPre){
                regPre.preventDefault();

                const nuevoPre = new FormData(form_pre);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                  if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                      const respuesta = xhr.responseText.trim();
                      if (respuesta === 'OK') {
                        alertaPositiva_pre("Prerecibo creado exitosamente");
                        form_pre.reset();
                        mostrar_org();
                      }else{
                        mostrarAlerta_pre(respuesta);
                      }
                     
                    }
                  }
                };

                xhr.open('POST', './php/reg_re1.php', true);
                xhr.send(nuevoPre);
              });
            });



          </script-->