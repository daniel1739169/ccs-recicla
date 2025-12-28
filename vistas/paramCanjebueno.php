<div class="card card-info mt-4">
    <div class="card-header">
        <h3 class="card-title">Parámetros de Canje (Catálogo)</h3>
    </div>
    <div class="card-body">
        
        <ul class="nav nav-tabs" id="canjeParamsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-categorias" data-toggle="tab" href="#content-categorias" role="tab" aria-controls="content-categorias" aria-selected="true">Categorías</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-productos" data-toggle="tab" href="#content-productos" role="tab" aria-controls="content-productos" aria-selected="false">Productos / Materiales</a>
            </li>
        </ul>

        <div class="tab-content" id="canjeParamsTabContent">
            
            <div class="tab-pane fade show active" id="content-categorias" role="tabpanel" aria-labelledby="tab-categorias">
                <h4 class="mt-3">Gestión de Categorías</h4>
                <button class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#modalNuevaCategoria">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>

            </div>

            <div class="tab-pane fade" id="content-productos" role="tabpanel" aria-labelledby="tab-productos">
                <h4 class="mt-3">Gestión de Productos y Kits</h4>
                <button class="btn btn-primary btn-sm mb-3" onclick="mostrar_nuevo_producto()">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </button>
                
                <!-- Contenedor del catálogo (visible inicialmente) -->
                <div id="catalogo_productos">
                    <p class="text-muted">Aquí se mostrará el catálogo de productos existentes.</p>
                    <!-- El catálogo se cargará dinámicamente -->
                </div>
                
                <!-- Formulario para nuevo producto (oculto inicialmente) -->
                <div class="card mt-3" id="form_nuevo_producto" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">Crear Nuevo Producto</h3>
                    </div>
                    <div class="card-body">
                        <form id="formProducto">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre_producto">Nombre del Producto</label>
                                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="categoria_producto">Categoría</label>
                                        <select class="form-control" id="categoria_producto" name="categoria_producto" required>
                                            <option value="">Seleccione una categoría</option>
                                            <!-- Las categorías se cargarán dinámicamente -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="puntos_producto">Puntos</label>
                                        <input type="number" class="form-control" id="puntos_producto" name="puntos_producto" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stock_producto">Stock</label>
                                        <input type="number" class="form-control" id="stock_producto" name="stock_producto" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estado_producto">Estado</label>
                                        <select class="form-control" id="estado_producto" name="estado_producto" required>
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="descripcion_producto">Descripción</label>
                                <textarea class="form-control" id="descripcion_producto" name="descripcion_producto" rows="3" placeholder="Descripción opcional del producto"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Producto
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="cancelar_nuevo_producto()">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
   
            </div>
            
        </div>
        
    </div>
</div>

<!-- Modal para Nueva Categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title">Crear Nueva Categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formCat">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombreCategoria">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="nombreCategoria" name="nombre" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar Categoría</button>
                </div>
                <div id="alertContainer_cat"></div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const form_cat = document.getElementById('formCat');

        form_cat.addEventListener('submit', function(regCat){
            regCat.preventDefault();

            const nuevaCat = new FormData(form_cat);

            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function(){
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const respuesta = xhr.responseText.trim();
                        if (respuesta === 'OK') {
                            alertaPositiva_cat("Categoría creada exitosamente");
                            form_cat.reset();
                            $('#modalNuevaCategoria').modal('hide');
                        } else {
                            mostrarAlerta_cat(respuesta);
                        }
                    
                    }
                }
            };

            xhr.open('POST', './php/crear_categoria.php', true);
            xhr.send(nuevaCat);
        });

        // Manejar el envío del formulario de nuevo producto
        const formProducto = document.getElementById('formProducto');
        if (formProducto) {
            formProducto.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('./php/guardar_producto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Producto guardado correctamente');
                        cancelar_nuevo_producto();
                        // Aquí podrías recargar la lista de productos si es necesario
                    } else {
                        alert('Error al guardar el producto: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar el producto');
                });
            });
        }

        // Inicializar componentes de la interfaz
        inicializarComponentesUI();
    });

    function mostrarAlerta_cat(mensaje, tipo = 'danger', tiempo = 5000){
        const alertContainer = document.getElementById('alertContainer_cat');

        const alerta = document.createElement('div');
        // Usar template string válidos y escapar el HTML correctamente
        alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
        alerta.setAttribute('role', 'alert');
        alerta.innerHTML = `<strong>Error:</strong><br>${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>`;

        alertContainer.appendChild(alerta);

        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.remove();
            }
        }, tiempo);
    }

    function alertaPositiva_cat(mensaje, tipo = 'success', tiempo = 5000){
        const alertContainer = document.getElementById('alertContainer_cat');

        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
        alerta.setAttribute('role', 'alert');
        alerta.innerHTML = `<strong>Éxito:</strong><br>${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>`;

        alertContainer.appendChild(alerta);

        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.remove();
            }
        }, tiempo);
    }

    // Funciones para manejar el formulario de productos
    function mostrar_nuevo_producto() {
        const catalogo = document.getElementById('catalogo_productos');
        const formProducto = document.getElementById('form_nuevo_producto');
        
        if (catalogo) catalogo.style.display = 'none';
        if (formProducto) {
            formProducto.style.display = 'block';
            // Cargar categorías en el select
            cargarCategoriasParaFormulario();
        }
    }

    function cancelar_nuevo_producto() {
        const catalogo = document.getElementById('catalogo_productos');
        const formProducto = document.getElementById('form_nuevo_producto');
        
        if (formProducto) {
            formProducto.style.display = 'none';
            document.getElementById('formProducto').reset();
        }
        if (catalogo) catalogo.style.display = 'block';
    }

    // Función para cargar categorías en el formulario de productos
    async function cargarCategoriasParaFormulario() {
        try {
            const response = await fetch('./php/get_categorias.php', { credentials: 'same-origin' });

            // Si el endpoint responde con error HTTP (por ejemplo 401 si no autenticado), manejarlo
            if (!response.ok) {
                const text = await response.text();
                let msg = text;
                try { const parsed = JSON.parse(text); msg = parsed.error || JSON.stringify(parsed); } catch(e) {}
                console.error('Error al obtener categorías (HTTP ' + response.status + '):', msg);
                alert('Error al cargar categorías: ' + (msg || response.statusText));
                return;
            }

            const data = await response.json();

            const select = document.getElementById('categoria_producto');
            if (select) {
                select.innerHTML = '<option value="">Seleccione una categoría</option>';

                if (!data || !Array.isArray(data.categorias) || data.categorias.length === 0) {
                    // Mostrar información si no hay categorías
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No se encontraron categorías';
                    select.appendChild(option);
                    return;
                }

                data.categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar categorías:', error);
            alert('Error inesperado al cargar categorías. Revisa la consola para más detalles.');
        }
    }

    // Función para inicializar componentes de UI
    function inicializarComponentesUI() {
        // Inicializar elementos si es necesario
        if (typeof $ !== 'undefined') {
            // Inicializar Select2 si está disponible
            if ($.fn.select2) {
                $('.select2').select2();
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                });
            }

            // Inicializar datepickers si están disponibles
            if ($.fn.datetimepicker) {
                $('#reservationdate').datetimepicker({
                    format: 'L'
                });

                $('#reservationdatetime').datetimepicker({ 
                    icons: { time: 'far fa-clock' } 
                });
            }
        }
    }
</script>