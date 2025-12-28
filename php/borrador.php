            <?php
            

            if (isset($_POST['id'])) {

              $org_id = intval($_POST['id']);

              $sql_org = "SELECT nombre FROM organizacion WHERE id = $org_id;";
              $result_org = mysqli_query($con, $sql_org);
              $org = mysqli_fetch_array($result_org);

              echo '<div class="card-header">
                <h1 class="card-title">Prerecibo</h1>
              </div>
              <div class="card-body">
                <form id="formPre">


      
        <h2 class="mb-3 text-success">Datos de Recibo</h2>

            <label>Organización</label>
            <div class="input-group mb-3">  
          <span class="input-group-text"><i class="far fa-building"></i></span>
        <input type="text" class="form-control" value="'.$org['nombre'].'" 
        name="organizacion" id="org" readonly>
    </div>

        <h2 class="mb-3 text-success">Datos de Recibo</h2>
                

    <label>Encargado</label>
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="far fa-solid fa-user"></i></span>
        <input class="form-control" type="text" name="encargado" id="encargado" placeholder="Escriba el nombre del encargado" required autocomplete="off">
    </div>

    <label>Cargo del encargado</label>    
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="fas fa-solid fa-suitcase"></i></span>
        <input class="form-control" type="text" name="cargo" id="cargo" placeholder="Escriba el cargo del encargado" required autocomplete="off">
    </div>
    <button type="submit" name="guardar_recibo">Guardar Recibo</button>
</form>
</div>
';
            }

?>
