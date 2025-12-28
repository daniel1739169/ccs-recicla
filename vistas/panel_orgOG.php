<div class="car card-success" id="panel_org" style="display: none">
    <div class="card-header">
        <h1 class="card-title">Organizaciones</h1>
    </div>
    <div class="card-body">
        <style>

            th{
                padding: 15px;
            }

            tr{
                padding: 15px;
            }

            td{
                padding: 15px;
            }

    </style>

    
    <div id="alertContainer"style="position: fixed;"></div>

    <div><button class="btn btn-secondary btn-sm" id="nuevaOrg">Nueva organización</button></div>
    <table class="table table-striped table-bordered table-secondary" style="border: 2px solid;">
        <thead style="padding: 2px">
            <th style="text-align: center;">#</th>
            <th style="text-align: center;">Nombre</th>
            <th style="text-align: center;">Comité</th>
            <th style="text-align: center;">Ubicación</th>
            <th style="text-align: center;">Responsable</th>
            <th style="text-align: center;">Teléfono del responsable</th>
            <th style="text-align: center;">Acciones</th>
        </thead>
        <tbody>
            
            <?php

            include "./php/ver_org.php";

            $i = 1;

            while ($org = mysqli_fetch_array($result_org)) {
             ?>


                <tr>
                    <td scope="col" style="text-align: center;"><?= $i++?></td>
                    <td scope="col" style="text-align: center;"><?= $org['nombre']?></td>
                    <td scope="col" style="text-align: center;"><?= $org['descripcion']?></td>
                    <td scope="col" style="text-align: center;"><?= $org['ubicacion']?></td>
                    <td scope="col" style="text-align: center;"><?= $org['nombre_responsable']?></td>
                    <td scope="col" style="text-align: center;"><?= $org['telefono_responsable']?></td>
                    <td scope="col" style="text-align: center;"><button class="btn btn-primary btn-sm btn-ver" data-id="<?= $org['id']?>" onclick="mostrar_panelRe()">Ver recibos</button></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

    </div>
    
<script>

const nuevaOrg = document.getElementById('nuevaOrg');

nuevaOrg.addEventListener('click', function() {
    mostrar_formOrg();
  fetch('./php/campos_org.php')
    .then(respuesta => respuesta.json())
    .then(campo => {
      console.log('Comites recibidos:', campo.comites);

      if (campo.comites.length > 0) {
        const select = document.getElementById('comite_o');
        select.innerHTML = '<option value="" disbaled selected>Selecciona el comite al que pertenecera esta organizacion</option>'; // limpiar antes
        campo.comites.forEach(co => {
          select.innerHTML += `<option value="${co.id}">${co.descripcion}</option>`;
        });
      }

    })
})


document.addEventListener('DOMContentLoaded', function () {
    const botones = document.querySelectorAll('.btn-ver');

    botones.forEach(function (boton) {
        boton.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            const datos = new FormData();
            datos.append('id', id);

            fetch('./php/leer_correlacion.php', {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.json())
            .then(data => {



                console.log('Organización:', data.organizacion);
                console.log('Recibos:', data.recibos);

                // Mostrar el nombre de la organización
                document.getElementById('titulo_recibos').textContent = `Recibos de ${data.organizacion.nombre}`;


                var crearPre = document.getElementById('btn-crear_pre');
                crearPre.innerHTML = `<button class="btn btn-secondary btn-sm" onclick="mostrar_formPre()" id="btn-prerecibo" data-id="${data.organizacion.id}">Nuevo prerecibo</button>`;
                


                    const boton_crearPre = document.getElementById('btn-prerecibo');
                    boton_crearPre.addEventListener('click', function () {
                        const id_org = this.getAttribute('data-id');

                        const dato_org = new FormData();
                        dato_org.append('id', id_org);

                        fetch('./php/reg_pre.php', {
                            method: 'POST',
                            body: dato_org
                        })
                        .then(respuesta => respuesta.json())
                        .then(data_org => {
                        console.log('Organización recibida:', data_org.organizacion_pre);
                        console.log('Materiales para campos recibidos:', data_org.materiales_pre);
                        console.log('Parametro divisor recibido:', data_org.division);
                        console.log('Visitas recibidas:', data_org.visitas_pre);

                        const selectFecha = document.getElementById('fecha_prerecibo');
                        const responsable = document.getElementById('responsable');
                        const cargo = document.getElementById('cargo');
                        const visita_at = document.getElementById('visita');

                        // Limpiar opciones previas
                        selectFecha.innerHTML = '<option value="">-- Seleccionar fecha --</option>';

                        if (data_org.visitas_pre.length > 0) {
                          data_org.visitas_pre.forEach(v => {
                            // Agregar opción
                            selectFecha.innerHTML += `<option value="${v.fecha}">${v.fecha}</option>`;
                          });

                          // Listener una sola vez
                          selectFecha.addEventListener('change', function() {
                            const valorSeleccionado = this.value;
                            // Buscar la visita correspondiente
                            const visita = data_org.visitas_pre.find(v => v.fecha == valorSeleccionado);
                            if (visita) {
                              responsable.value = visita.responsable;
                              cargo.value = visita.cargo;
                              visita_at.value = visita.id;
                            }
                          });
                        }

                         const btnReset = document.getElementById('cancelarFormpre');

                        btnReset.addEventListener('click', () => {
                          tabla_materiales.innerHTML = "";
                          disponibles = [...data_org.materiales_pre];
                          document.getElementById('formPre').reset();
                        });



                        // Rellenar el campo de organización
                        document.getElementById('org_prerecibo').value = data_org.organizacion_pre.nombre;

                        let disponibles = [...data_org.materiales_pre];
                        const tabla_materiales = document.getElementById('cuerpo_materiales_pre');

                        filaPre();


                        function filaPre() {
                          const fila_mp = document.createElement('tr');

                          // --- Select ---
                          const tdSelect = document.createElement('td');
                          const select = document.createElement('select');
                          select.classList.add('form-control');
                          select.innerHTML = '<option value="" disabled selected>Selecciona el material recogido</option>';

                          disponibles.forEach(mp => {
                            select.innerHTML += `<option value="${mp.id}">${mp.descripcion}</option>`;
                          });

                          tdSelect.appendChild(select);
                          fila_mp.appendChild(tdSelect);

                          // --- Cantidad ---
                          const tdCantidad = document.createElement('td');
                          const inputCantidad = document.createElement('input');
                          inputCantidad.type = 'number';
                          inputCantidad.classList.add('form-control', 'cantidad');
                          inputCantidad.value = 0;
                          tdCantidad.appendChild(inputCantidad);
                          fila_mp.appendChild(tdCantidad);

                          // --- Puntaje ---
                          const tdPuntaje = document.createElement('td');
                          const inputPuntaje = document.createElement('input');
                          inputPuntaje.type = 'number';
                          inputPuntaje.classList.add('form-control', 'puntaje');
                          inputPuntaje.readOnly = true;
                          tdPuntaje.appendChild(inputPuntaje);
                          fila_mp.appendChild(tdPuntaje);

                          // Agregar la fila completa a la tabla
                          tabla_materiales.appendChild(fila_mp);

                          // --- Eventos ---
                          select.addEventListener('change', () => {
                            const id = parseInt(select.value);
                            if (!id) return;

                            // Actualizar los name de los inputs con el id elegido
                            inputCantidad.name = `${id}_cantidad`;
                            inputPuntaje.name = `${id}_puntaje`;

                            // Quitar material de disponibles
                            disponibles = disponibles.filter(m => m.id != id);

                            // Bloquear el select actual
                            select.disabled = true;

                            // Crear nueva fila si quedan materiales
                            if (disponibles.length > 0) {
                              filaPre();
                            }
                          });

                          inputCantidad.addEventListener('input', () => {
                            let totalKg = 0;
                            let totalPuntos = 0;
                            const cantidad = parseFloat(inputCantidad.value) || 0;
                            const divisor = data_org.division.division_kilo; // tu divisor real
                            inputPuntaje.value = (cantidad / divisor).toFixed(2);
                            document.querySelectorAll('input.cantidad').forEach(c => {
                              totalKg += parseFloat(c.value) || 0;
                            });

                            document.querySelectorAll('input.puntaje').forEach(p => {
                              totalPuntos += parseFloat(p.value) || 0;
                            });

                            document.getElementById('total_cantidad_pre').value = totalKg.toFixed(2);
                            document.getElementById('total_puntos_pre').value = totalPuntos.toFixed(2);
                          });
                        }

                       

                            /*tabla_materiales.innerHTML = "";

                            // Materiales de la DB para escoh=ger kg y puntos
                            if (data_org.materiales_pre.length > 0) {
                                data_org.materiales_pre.forEach(m => {
                                    const fila_m = document.createElement('tr');
                                    fila_m.innerHTML = `
                                        <td>${m.descripcion}</td>
                                        <td><input type="number" value="0" class="form-control cantidad" name="${m.id}_cantidad"></td>
                                        <td><input type="number" class="form-control puntaje" name="${m.id}_puntaje" readonly></td>
                                    `;
                                    tabla_materiales.appendChild(fila_m);

                                });

                                activarCalculoMateriales();
                            }

                                function activarCalculoMateriales() {
                                  const filas = document.querySelectorAll('#cuerpo_materiales_pre tr');

                                  filas.forEach(fila => {
                                    const inputCantidad = fila.querySelector('input.cantidad');
                                    const inputPuntaje = fila.querySelector('input.puntaje');

                                    if (inputCantidad && inputPuntaje) {
                                      inputCantidad.addEventListener('input', function () {
                                        const cantidad = parseFloat(this.value) || 0;
                                        const divisor = parseFloat(data_org.division.division_kilo);
                                        const puntaje = cantidad / divisor;
                                        inputPuntaje.value = puntaje.toFixed(2);

                                        // Recalcular totales
                                        let totalKg = 0;
                                        let totalPuntos = 0;

                                        document.querySelectorAll('input.cantidad').forEach(c => {
                                          totalKg += parseFloat(c.value) || 0;
                                        });

                                        document.querySelectorAll('input.puntaje').forEach(p => {
                                          totalPuntos += parseFloat(p.value) || 0;
                                        });

                                        document.getElementById('total_cantidad_pre').value = totalKg.toFixed(2);
                                        document.getElementById('total_puntos_pre').value = totalPuntos.toFixed(2);
                                      });
                                    }
                                  });
                                }*/

                        })
                        .catch(error => console.error('Error al cargar prerecibo:', error));
                    });





                // Construir la tabla de recibos
                const tbody = document.createElement('tbody');

            if (data.recibos.length > 0) {
                data.recibos.forEach(r => {
                    const fila = document.createElement('tr');

                    // Si el estado es 0, agregas el botón extra
                    if (r.estado === '0') {
                        r.estado = '<p class="text-danger">Incompleto</p>';
                        fila.innerHTML = `
                            <td>${r.correlativo}</td>
                            <td>${r.responsable}</td>
                            <td>${r.fecha}</td>
                            <td>${r.estado}</td>
                            <td>
                                <button class="btn btn-success btn-sm btn-detalle" data-id="${r.correlativo}"><i class="fas fa-solid fa-recycle"></i><br>Material</button>
                                <a class="btn btn-danger btn-sm" href="./php/pdf_re.php?c=${r.correlativo}" target="_blank"><i class="fas fa-file-pdf"></i><br>PDF</a>
                                <button class="btn btn-primary btn-sm btn-refinal" data-id="${r.correlativo}" onclick="mostrar_formRefinal()"><i class="fas fa-solid fa-receipt"></i><br>Recibo final</button>
                            </td>
                        `;
                    } else {
                        r.estado = '<p class="text-success">Completo</p>';
                        let fechaObj = new Date(r.fecha);
                        let fechaFormateada = fechaObj.toLocaleDateString("es-ES", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric"
                        });
                        fila.innerHTML = `
                            <td>${r.correlativo}</td>
                            <td>${r.responsable}</td>
                            <td>${fechaFormateada}</td>
                            <td>${r.estado}</td>
                            <td>
                                <button class="btn btn-success btn-sm btn-detalle" data-id="${r.correlativo}"><i class="fas fa-solid fa-recycle"></i><br>Material</button>
                                <a class="btn btn-danger btn-sm" href="./php/pdf_re.php?c=${r.correlativo}" target="_blank"><i class="fas fa-file-pdf"></i><br>PDF</a>
                            </td>
                        `;
                    }

                    tbody.appendChild(fila);
                });
            } else {
                const fila = document.createElement('tr');
                fila.innerHTML = `<td colspan="5">No hay registros disponibles para esta organización.</td>`;
                tbody.appendChild(fila);
            }


                // Reemplazar el contenido del cuerpo de la tabla
                const tabla = document.querySelector('#panel_re table');
                const viejoTbody = tabla.querySelector('tbody');
                if (viejoTbody) tabla.removeChild(viejoTbody);
                tabla.appendChild(tbody);

                // Mostrar el panel

            })
            .catch(error => console.error('Error:', error));
        });
    });
});


              function mostrarAlerta6(mensaje, tipo = 'danger', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer6');

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

            function alertaPositiva6(mensaje, tipo = 'success', tiempo = 5000){
              const alertContainer = document.getElementById('alertContainer6');

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



document.addEventListener('click', function (e) {
    const btnDetalle = e.target.closest('.btn-detalle');
    if (!btnDetalle) return;

    const num_co = btnDetalle.getAttribute('data-id');
    const filaPadre = btnDetalle.closest('tr');

    // Verifica si ya existe la fila de detalles justo después
    const filaExistente = filaPadre.nextElementSibling;
    if (filaExistente && filaExistente.classList.contains('fila-detalles')) {
        filaExistente.remove();
        return; // toggle: si existe, lo quita
    }

    // Si no existe, pedimos los datos al backend
    const data_d = new FormData();
    data_d.append('num_co', num_co);

    fetch('./php/leer_detalles.php', {
        method: 'POST',
        body: data_d
    })
    .then(res => res.json())
    .then(ver => {
        if ((ver.detalles_m && ver.detalles_m.length > 0) || (ver.detalles_mr && ver.detalles_mr.length > 0)) {
            const filaDetalles = document.createElement('tr');
            filaDetalles.classList.add('fila-detalles');

            let contenido = "<td colspan='5'><div style='display:flex; gap:20px;'>";

            //PRERECIBO
            if (ver.detalles_m && ver.detalles_m.length > 0) {
                contenido += `
                    <table style="border:1px solid #ccc; border-collapse:collapse; flex:1;">
                        <tr><td colspan="3"><strong>PRERECIBO</strong></td></tr>
                        <tr><td>Tipo de material</td><td>Cantidad Kg</td><td>Puntaje</td></tr>
                `;
                ver.detalles_m.forEach(dm => {
                    contenido += `
                        <tr>
                            <td>${dm.descripcion}</td>
                            <td>${dm.cantidad_kg}</td>
                            <td>${dm.cantidad_p}</td>
                        </tr>
                    `;
                });
                contenido += "</table>";
            }

            //RECIBO FINAL
            if (ver.detalles_mr && ver.detalles_mr.length > 0) {
                contenido += `
                    <table style="border:1px solid #ccc; border-collapse:collapse; flex:1;">
                        <tr><td colspan="3"><strong>RECIBO FINAL</strong></td></tr>
                        <tr><td>Tipo de material</td><td>Cantidad Kg</td><td>Puntaje</td></tr>
                `;
                ver.detalles_mr.forEach(dm => {
                    contenido += `
                        <tr>
                            <td>${dm.descripcion}</td>
                            <td>${dm.cantidad_kg}</td>
                            <td>${dm.cantidad_p}</td>
                        </tr>
                    `;
                });
                contenido += "</table>";
            }

            contenido += "</div></td>";

            filaDetalles.innerHTML = contenido;
            filaPadre.parentNode.insertBefore(filaDetalles, filaPadre.nextSibling);
        }
    })
    .catch(err => {
        console.error('Error al obtener detalles:', err);
    });
});













document.addEventListener('DOMContentLoaded', function () {

  // Delegación de eventos para botones dinámicos
  document.addEventListener('click', function(e){
    if(e.target.classList.contains('btn-refinal')){
      const correl = e.target.getAttribute('data-id');

      const dato_cor = new FormData();
      dato_cor.append('correlativo', correl);

      fetch('./php/reg_ref.php', {
        method: 'POST',
        body: dato_cor
      })
      .then(respuesta => respuesta.json())
      .then(data_cor => {
        console.log('Organización recibida:', data_cor.organizacion_ref);
        console.log('Materiales para campos recibidos:', data_cor.materiales_ref);
        console.log('Parametro divisor recibido:', data_cor.division);

        // Rellenar el campo de organización
        document.getElementById('org_refinal').innerHTML = data_cor.organizacion_ref.nombre;
        document.getElementById('resp_refinal').innerHTML = data_cor.organizacion_ref.responsable;
        document.getElementById('carg_refinal').innerHTML = data_cor.organizacion_ref.cargo;

        document.getElementById('cor_ref').innerHTML = data_cor.organizacion_ref.correlativo;
        document.getElementById('ref_cor').value = data_cor.organizacion_ref.correlativo;
        tabla_materiales = document.getElementById('cuerpo_materiales_ref');


                         const btnReset = document.getElementById('cancelarFormre');

                        btnReset.addEventListener('click', () => {
                          tabla_materiales.innerHTML = "";
                          disponibles = [...data_cor.materiales_ref];
                          document.getElementById('formRefinal').reset();
                        });



                        // Rellenar el campo de organización

                        let disponibles = [...data_cor.materiales_ref];

                        filaRef();


                        function filaRef() {
                          const fila_mr = document.createElement('tr');

                          // --- Select ---
                          const tdSelect = document.createElement('td');
                          const select = document.createElement('select');
                          select.classList.add('form-control');
                          select.innerHTML = '<option value="" disabled selected>Selecciona el material recogido</option>';

                          disponibles.forEach(mr => {
                            select.innerHTML += `<option value="${mr.id}">${mr.descripcion}</option>`;
                          });

                          tdSelect.appendChild(select);
                          fila_mr.appendChild(tdSelect);

                          // --- Cantidad ---
                          const tdCantidad = document.createElement('td');
                          const inputCantidad = document.createElement('input');
                          inputCantidad.type = 'number';
                          inputCantidad.classList.add('form-control', 'cantidad');
                          inputCantidad.value = 0;
                          tdCantidad.appendChild(inputCantidad);
                          fila_mr.appendChild(tdCantidad);

                          // --- Puntaje ---
                          const tdPuntaje = document.createElement('td');
                          const inputPuntaje = document.createElement('input');
                          inputPuntaje.type = 'number';
                          inputPuntaje.classList.add('form-control', 'puntaje');
                          inputPuntaje.readOnly = true;
                          tdPuntaje.appendChild(inputPuntaje);
                          fila_mr.appendChild(tdPuntaje);

                          // Agregar la fila completa a la tabla
                          tabla_materiales.appendChild(fila_mr);

                          // --- Eventos ---
                          select.addEventListener('change', () => {
                            const id = parseInt(select.value);
                            if (!id) return;

                            // Actualizar los name de los inputs con el id elegido
                            inputCantidad.name = `${id}_cantidad`;
                            inputPuntaje.name = `${id}_puntaje`;

                            // Quitar material de disponibles
                            disponibles = disponibles.filter(m => m.id != id);

                            // Bloquear el select actual
                            select.disabled = true;

                            // Crear nueva fila si quedan materiales
                            if (disponibles.length > 0) {
                              filaRef();
                            }
                          });

                          inputCantidad.addEventListener('input', () => {
                            let totalKg = 0;
                            let totalPuntos = 0;
                            const cantidad = parseFloat(inputCantidad.value) || 0;
                            const divisor = data_cor.division.division_kilo; // tu divisor real
                            inputPuntaje.value = (cantidad / divisor).toFixed(2);
                            document.querySelectorAll('input.cantidad').forEach(c => {
                              totalKg += parseFloat(c.value) || 0;
                            });

                            document.querySelectorAll('input.puntaje').forEach(p => {
                              totalPuntos += parseFloat(p.value) || 0;
                            });

                            document.getElementById('total_cantidad_ref').value = totalKg.toFixed(2);
                            document.getElementById('total_puntos_ref').value = totalPuntos.toFixed(2);
                          });
                        }




      })
      .catch(error => console.error('Error al cargar recibo final:', error));
    }
  });

});



</script>

<script>
    

    function alertaPositiva_pre(mensaje, tipo = 'success', tiempo = 5000){
      const alertContainer = document.getElementById('alertContainer');

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


    function alertaPositiva_re(mensaje, tipo = 'success', tiempo = 5000){
      const alertContainer = document.getElementById('alertContainer');

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


</script>

</div>