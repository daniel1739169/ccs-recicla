<div class="contenedor_form_org" id="form_org" style="display: none;">
    <form action="../php/register_org.php" method="post" class="p-4 shadow rounded bg-light">
        <h5 class="mb-3 text-success" style="text-align: center;">Nueva organización</h5>
        
        <div class="input-group mb-3">
            <span class="input-group-text"><label for="0">Nombre</label></span>
            <span class="input-group-text"><i class="bi bi-building-add"></i></span>
            <input type="text" name="nombre" class="form-control" id="0" placeholder="Escriba el nombre de la organización" required autocomplete="off">
        </div>

        <div class="input-group mb-3">

    <?php 
    
    require './php/campos_org.php';

    ?>
            <span class="input-group-text"><label for="1">Comité</label></span>
            <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
            <?php
                echo '<select name="comite" id="1" class="form-select" required>';
                echo '<option value="" disabled selected>Seleccione el comité al que pertenecera la organización</option>';
                foreach ($comites_actuales as $campo){
                echo '<option value="'.$campo['id'].'">'.$campo['descripcion'].'</option>"';
                }
                echo '</select>';
            ?>


        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><label for="2">Ubicación</label></span>
            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
            <input type="text" name="ubicacion" class="form-control" id="2" placeholder="Escriba la ubicación exacta de la organizacion" required autocomplete="off">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><label for="3">Responsable</label></span>
            <span class="input-group-text"><i class="bi bi-person-up"></i></span>            
            <input type="text" name="nombre_responsable" class="form-control" id="3" placeholder="Escriba el nombre del responsable" required autocomplete="off">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><label for="4">Cédula del Responsable</label></span>
            <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
            <input type="int" name="cedula_responsable" id="4" class="form-control" placeholder="Escriba la cédula del responsable" required autocomplete="off">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text"><label for="5">Teléfono del Responsable</label></span>
            <span class="input-group-text"><i class="bi bi-phone"></i></span>
            <input type="text" name="telefono_responsable" id="5" placeholder="Escriba el teléfono del responsable" class="form-control" autocomplete="off">
        </div>

        <button type="submit" name="btn_register_organization" class="btn btn-success">Registrar</button>
        <button type="button" id="salir_form_org" class="btn btn-danger" onclick="no_registrar_org()" >Salir del formulario</button>
    </form>
</div>