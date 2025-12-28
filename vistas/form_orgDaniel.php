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

            <input type="text" name="nombre" class="form-control" id="0" placeholder="Escriba el nombre de la organización" autocomplete="off">
                    
        </div>
            
        <label>Comité</label>
        <div class="input-group mb-3">    
            
                <?php 
               
                    require './php/campos_org.php';

                ?>
                     
            <span class="input-group-text"><i class="far fa-flag"></i></span>
            
                <select name="comite" id="1" class="form-control">
            <?php
                echo '<option value="" disabled selected>Seleccione el comité al que pertenecera la organización</option>';
                foreach ($comites_actuales as $campo){
                echo '<option value="'.$campo['id'].'">'.$campo['descripcion'].'</option>"';
                }
            ?>
                </select>
            

        </div>
        <label>Ubicación de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
            <input type="text" name="ubicacion" class="form-control" id="2" placeholder="Escriba la ubicación exacta de la organizacion" autocomplete="off">
        </div>

        <label>Nombre del responsable de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>            
            <input type="text" name="nombre_responsable" class="form-control" id="3" placeholder="Escriba el nombre del responsable" autocomplete="off">
        </div>

        <label>Apellido del responsable de la organización</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>            
            <input type="text" name="apellido_responsable" class="form-control" id="3" placeholder="Escriba el apellido del responsable" autocomplete="off">
        </div>

        <label>Cédula del responsable</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa-address-card"></i></span>
            <input type="int" name="cedula_responsable" id="4" class="form-control" placeholder="Escriba la cédula del responsable" autocomplete="off">
        </div>

        <label>Teléfono del responsable</label>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="far fa fa-phone"></i></span>
            <input type="text" name="telefono_responsable" id="5" placeholder="Escriba el teléfono del responsable" class="form-control" autocomplete="off">
        </div>

        <button type="submit" name="btn_register_organization" class="btn btn-success">Registrar</button>
        <button type="button" id="salir_form_org" class="btn btn-danger" onclick="cancelar_formOrg()" >Salir del formulario</button>

        <div id="alertContainer1"></div>

        </form>
    </div>
</div>


          <script>
              function mostrarAlerta1(mensaje, tipo = 'danger', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer1');

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

            function alertaPositiva1(mensaje, tipo = 'success', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer1');

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
                const form_org = document.getElementById('orgForm');

                form_org.addEventListener('submit', function(evento){
                evento.preventDefault();
                
                const nuevasOrg = new FormData(form_org);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            alertaPositiva1(xhr.responseText);
                            form_org.reset();
                            
                        }else{
                            mostrarAlerta1('Envio no realizado');
                        }
                    }
                };

                xhr.open('POST', './php/register_org.php', true);
                xhr.send(nuevasOrg);
              });
              });

          </script>


