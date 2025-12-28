            <div class="card card-success" id="form_moAdmin" style="display: none">
              <div class="card-header">
                <h1 class="card-title">Agregar parametros de facturacion</h1>
              </div>
              <form id="formMoadmin">
              <div class="card-body">


                <label>Nuevo comite</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-flag"></i></span>
                  </div>

                  <input class="form-control" type="text" name="nombre_comite" id="nombre_c" placeholder="Escriba el nombre del nuevo comite" autocomplete="off">
                </div>

                <label>Nuevo tipo de material</label>
                <div class="input-group mb-3">  
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-recycle"></i></span>
                  </div>

                  <input type="nombre_material" class="form-control" name="nombre_material" id="nombre_m" placeholder="Ingrese el nombre del nuevo tipo de material">
                </div>

                <button class="btn btn-primary" type="submit" name="actualizar_param" id="update_param">Agregar</button>
                <button class="btn btn-danger" type="button" onclick="cancelar_formMoadmin()" id="cancel_update">Cancelar</button>

               <div id="alertContainer_mo"></div>
               </form>
              </div>
            </div>


          <script>
              function mostrarAlerta0(mensaje, tipo = 'danger', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer_mo');

              const alerta = document.createElement('div');
              alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
              alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;

              alertContainer.appendChild(alerta);

              setTimeout(() => {
                if (alerta.parentElement) {
                  alerta.remove();
                }
              }, tiempo);
            }

            function alertaPositiva0(mensaje, tipo = 'success', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer_mo');

              const alerta = document.createElement('div');
              alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
              alerta.innerHTML = `<strong>El envio fue realizado con exito:</strong><br> ${mensaje}`;

              alertContainer.appendChild(alerta);

              setTimeout(() => {
                if (alerta.parentElement) {
                  alerta.remove();
                }
              }, tiempo);
            }


            document.addEventListener('DOMContentLoaded', function(){
                const form_moAdmin = document.getElementById('formMoadmin');

                form_moAdmin.addEventListener('submit', function(evento){
                evento.preventDefault();
                
                const nuevosMoadmin = new FormData(form_moAdmin);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            alertaPositiva0(xhr.responseText);
                            
                        }else{
                            mostrarAlerta0('Envio no realizado');
                        }
                    }
                };

                xhr.open('POST', './php/reg_reg.php', true);
                xhr.send(nuevosMoadmin);
              });
              });

            </script>