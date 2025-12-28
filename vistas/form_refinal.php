            <div class="car card-primary" id="form_refinal" style="display: none">


              <div class="card-header">
                <h1 class="card-title">Recibo final</h1>
              </div>
              <div class="card-body">
                <form id="formRefinal">

                  <label>Nº de recibo</label>
                  <div class="input-group mb-3">
                      <span class="input-group-text" id="cor_ref"></span>
                      <input type="hidden" name="correlativo" id="ref_cor">
                  </div>

                  <label>Organización</label>
                  <div class="input-group mb-3">  
                      <span class="input-group-text"><i class="far fa-building"></i></span>
                      <span class="input-group-text" id="org_refinal"></span>
                  </div>

                  

                  <label>Responsable</label>
                  <div class="input-group mb-3">
                      <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>
                      <span class="input-group-text" id="resp_refinal"></span>

                  </div>

                  <label>Cargo del responsable</label>    
                  <div class="input-group mb-3">
                      <span class="input-group-text"><i class="fas fa-solid fa-suitcase"></i></span>
                      <span class="input-group-text" id="carg_refinal"></span>
                  </div>

                  <table id="materiales_ref">
                    <thead>
                      <tr>
                        <th>Tipo de material</th>
                        <th>Cantidad (Kg)</th>
                        <th>Puntos</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_materiales_ref">
                      
                    </tbody>


                  </table><br>
              <label>Total de Material (Kg):</label>
                <input type="number" class="form-control" name="total_cantidad" id="total_cantidad_ref" step="0.01" readonly value="0">
              
              <label>Total de Puntos:</label> 

                <input type="number" class="form-control" name="total_puntos" id="total_puntos_ref" step="1" min="0" readonly value="0">
              
              <button type="submit" class="btn btn-success btn-sm">Crear</button>

              <button type="button" class="btn btn-danger btn-sm" onclick="cancelar_formRefinal()" id="cancelarFormre">Volver a recibos</button>

                      <div id="alertContainer_re"></div>

                </form>
            </div>
          </div>`

<script>
function alertRef(mensaje, tipo = 'danger', tiempo = 5000){
  const alertContainer = document.getElementById('alertContainer_re');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

function alertRefpo(mensaje, tipo = 'success', tiempo = 5000){
  const alertContainer = document.getElementById('alertContainer');
  const alerta = document.createElement('div');
  alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
  alerta.innerHTML = `<strong>El envío fue realizado con éxito:</strong><br> ${mensaje}`;
  alertContainer.appendChild(alerta);
  setTimeout(() => { if (alerta.parentElement) alerta.remove(); }, tiempo);
}

document.addEventListener("DOMContentLoaded", function () {
  const formRef = document.getElementById("formRefinal");

  formRef.addEventListener("submit", function (evento) {
    evento.preventDefault();

    const datosFormulario = new FormData(formRef);
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
          alertRefpo(respuesta.message);
          formRef.reset();
          mostrar_org();
        } else if (respuesta.status === "alert"){
          alertRef(respuesta.alerta);
        }
      } else {
        alertRef('Error de conexión con el servidor');
      }
    };

    xhr.open("POST", "./php/reg_re2.php", true);
    xhr.send(datosFormulario);
  });
});
</script>

          <!--script>


            document.addEventListener('DOMContentLoaded', function(){
              const form_ref = document.getElementById('formRefinal');

              form_ref.addEventListener('submit', function(regRef){
                regRef.preventDefault();

                const nuevoRef = new FormData(form_ref);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                  if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                      const respuesta = xhr.responseText.trim();
                      if (respuesta === 'OK') {
                        alertaPositiva_re("Recibo final creado exitosamente");
                        form_ref.reset();
                        mostrar_org();
                      }else{
                        mostrarAlerta_re(respuesta);
                      }
                     
                    }
                  }
                };

                xhr.open('POST', './php/reg_re2.php', true);
                xhr.send(nuevoRef);
              });
            });



          </script-->
