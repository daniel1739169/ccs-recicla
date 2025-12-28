<div class="card card-success" id="panel_org" style="display: none">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-building mr-2"></i>Organizaciones
        </h1>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="alertContainer" style="position: fixed;"></div>

        <!-- CONTROLES DE BÚSQUEDA Y FILTRO -->
        <div class="d-flex justify-content-between align-items-center my-3 px-3">
            <div>
                <button class="btn btn-secondary btn-sm" id="nuevaOrg">
                    <i class="fas fa-plus mr-1"></i>Nueva organización
                </button>
                <button class="btn btn-outline-secondary btn-sm ml-2" onclick="recargarTablaOrganizaciones()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
            <div class="form-inline">
                <!-- Buscador por nombre -->
                <div class="input-group input-group-sm mr-2" style="width: 200px;">
                    <input type="text" class="form-control" id="buscarOrganizacion" placeholder="Buscar organización...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <!-- Filtro por comité -->
                <select class="form-control form-control-sm" id="filtroComite" style="width: 150px;">
                    <option value="">Todos los comités</option>
                    <!-- Los comités se cargarán dinámicamente -->
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-success" style="background-color: #28a745!important;">
                    <tr>
                        <th style="width: 60px; text-align: center; color: white;">#</th>
                        <th style="color: white;">Nombre</th>
                        <th style="color: white;">Comité</th>
                        <th style="color: white;">Ubicación</th>
                        <th style="color: white;">Responsable</th>
                        <th style="color: white;">Teléfono</th>
                        <th style="color: white;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-organizaciones">
                    <?php
                    include "./php/ver_org.php";
                    $i = 1;
                    while ($org = mysqli_fetch_array($result_org)) {
                        echo '<tr>';
                        echo '<td class="text-center" style="border-left: 3px solid #28a745;">
                                <span class="badge bg-success">'.$i++.'</span>
                              </td>';
                        echo '<td><i class="fas fa-building text-success mr-2"></i>'.$org['nombre'].'</td>';
                        echo '<td class="comite-celda">'.$org['descripcion'].'</td>';
                        echo '<td>'.$org['ubicacion'].'</td>';
                        echo '<td>'.$org['nombre_responsable'].'</td>';
                        echo '<td>'.$org['telefono_responsable'].'</td>';
                        echo '<td class="text-center">
                                <button class="btn btn-primary btn-sm btn-ver" 
                                        data-id="'.$org['id'].'" 
                                        onclick="mostrar_panelRe()">
                                    <i class="fas fa-file-invoice mr-1"></i>Ver recibos
                                </button>
                              </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer text-muted">
            <small id="contador-organizaciones"><?php echo ($i-1); ?> Organizaciones registradas</small>
        </div>
    </div>
</div>


<!-- SCRIPT PARA BÚSQUEDA Y FILTRO LOCAL -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar comités para el filtro
        cargarFiltroComites();
        
        // Configurar eventos de búsqueda y filtro (funcionalidad local)
        inicializarFiltrosLocales();
        
        // Guardar datos iniciales de la tabla para filtros locales
        guardarDatosIniciales();
    });
    
    // Función para cargar comités en el filtro
    async function cargarFiltroComites() {
        try {
            const response = await fetch('./php/get_comites.php');
            const data = await response.json();
            
            const select = document.getElementById('filtroComite');
            if (select && Array.isArray(data)) {
                // Mantener la primera opción
                select.innerHTML = '<option value="">Todos los comités</option>';
                
                data.forEach(comite => {
                    const option = document.createElement('option');
                    option.value = comite.descripcion;
                    option.textContent = comite.descripcion;
                    option.setAttribute('data-id', comite.id);
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar comités para filtro:', error);
        }
    }
    
    // Variable global para almacenar datos iniciales
    let datosOrganizaciones = [];
    
    function guardarDatosIniciales() {
        const filas = document.querySelectorAll('#tabla-organizaciones tr');
        datosOrganizaciones = [];
        
        filas.forEach((fila, index) => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length >= 7) {
                datosOrganizaciones.push({
                    index: index,
                    nombre: celdas[1]?.textContent?.trim() || '',
                    comite: celdas[2]?.textContent?.trim() || '',
                    ubicacion: celdas[3]?.textContent?.trim() || '',
                    responsable: celdas[4]?.textContent?.trim() || '',
                    telefono: celdas[5]?.textContent?.trim() || '',
                    elemento: fila
                });
            }
        });
        
        actualizarContador(datosOrganizaciones.length);
    }

    </script>

    <!-- SCRIPT PARA BÚSQUEDA Y FILTRO LOCAL - IDÉNTICO A TU OTRO CÓDIGO -->
<script>
$(document).ready(function() {
    // Buscador de Organizaciones - IDÉNTICO A TU OTRO CÓDIGO
    $('#buscarOrganizacion').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#tabla-organizaciones tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        actualizarContador();
    });

    // Filtro por Comité - IDÉNTICO A TU OTRO CÓDIGO
    $('#filtroComite').on('change', function() {
        var comiteSeleccionado = $(this).find('option:selected').text();
        
        if (comiteSeleccionado === "Todos los comités") {
            // Mostrar todos
            $('#tabla-organizaciones tr').show();
        } else {
            // Filtrar por comité
            $('#tabla-organizaciones tr').each(function() {
                var comiteCelda = $(this).find('td:nth-child(3)').text().trim();
                $(this).toggle(comiteCelda === comiteSeleccionado);
            });
        }
        actualizarContador();
    });

    // Función para actualizar contador
    function actualizarContador() {
        var visibleRows = $('#tabla-organizaciones tr:visible').length;
        $('#contador-organizaciones').text('Coincidencias: ' + visibleRows);
    }

    // Función para recargar tabla (restablecer filtros)
    window.recargarTablaOrganizaciones = function() {
        $('#buscarOrganizacion').val('');
        $('#filtroComite').val('');
        $('#tabla-organizaciones tr').show();
        actualizarContador();
    };
});

</script>

<script>
// Encuentra esta parte en tu código (línea 92) y corrígela:
nuevaOrg.addEventListener('click', function() {
    mostrar_formOrg();
    fetch('./php/campos_org.php')
        .then(respuesta => respuesta.json())
        .then(campo => {
            console.log('Comites recibidos:', campo.comites);
            
            if (campo.comites.length > 0) {
                const select = document.getElementById('comite_o');
                select.innerHTML = '<option value="" disabled selected>Selecciona el comité al que pertenecerá esta organización</option>';
                // CORRECCIÓN: Agrega comillas alrededor del string
                campo.comites.forEach(co => {
                    select.innerHTML += '<option value="${co.id}">${co.descripcion}</option>';
                });
            }
        })
});

</script>


<!-- Estos scripts van al final -->
<script src="/dist/js/demo.js"></script>
<script src="plugins/jquery/jquery.min.js"></script>



<script>

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
                crearPre.innerHTML = `
                        <button class="btn btn-secondary btn-sm" 
                            onclick="mostrar_formPre()" 
                                id="btn-prerecibo" 
                                    data-id="${data.organizacion.id}">
                                <i class="fas fa-plus mr-1"></i> Nuevo prerecibo
                        </button>`;


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