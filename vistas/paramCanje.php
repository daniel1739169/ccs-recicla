<div class="card card-info mt-4" id="paramcanje" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">Parámetros de Canje (Catálogo)</h3>
    </div>
    <div class="card-body">
        
        <ul class="nav nav-tabs" id="canjeParamsTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-categorias" data-toggle="tab" href="#content-categorias" role="tab" aria-controls="content-categorias" aria-selected="true">
                    <i class="fas fa-list mr-2"></i>Categorías
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-productos" data-toggle="tab" href="#content-productos" role="tab" aria-controls="content-productos" aria-selected="false">
                    <i class="fas fa-box mr-2"></i>Productos / Materiales
                </a>
            </li>
        </ul>

        <div class="tab-content" id="canjeParamsTabContent">
            
            <!-- Pestaña de Categorías -->
            <div class="tab-pane fade show active" id="content-categorias" role="tabpanel" aria-labelledby="tab-categorias">
                <div class="mt-3">
                    <button class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#modalNuevaCategoria">
                        <i class="fas fa-plus"></i> Nueva Categoría
                    </button>
                    
                    <!-- Tabla desplegable de Categorías -->
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-list-alt mr-2"></i>Lista de Categorías
                                <div class="card-tools ml-auto">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-info" style="background-color: #17a2b8!important;">
                                        <tr>
                                            <th style="width: 60px; text-align: center; color: white;">#</th>
                                            <th style="color: white;">Nombre</th>
                                            <th style="width: 100px; text-align: center; color: white;">Estado</th>
                                            <th style="width: 120px; text-align: center; color: white;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-categorias">
                                        <!-- Las categorías se cargarán aquí dinámicamente -->
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando categorías...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small id="contador-categorias">Cargando...</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Productos -->
            <div class="tab-pane fade" id="content-productos" role="tabpanel" aria-labelledby="tab-productos">
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="mostrar_nuevo_producto()">
                                <i class="fas fa-plus"></i> Nuevo Producto
                            </button>
                            <button class="btn btn-outline-secondary btn-sm ml-2" onclick="cargarTablaProductos()">
                                <i class="fas fa-sync-alt"></i> Actualizar
                            </button>
                        </div>
                        <div class="form-inline">
                            <input type="text" class="form-control form-control-sm mr-2" id="buscarProducto" placeholder="Buscar producto..." style="width: 200px;">
                            <select class="form-control form-control-sm" id="filtroCategoria" style="width: 150px;">
                                <option value="">Todas las categorías</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Formulario para nuevo producto (oculto inicialmente) -->
                    <div class="card mb-3" id="form_nuevo_producto" style="display: none;">
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
                    
                    <!-- Tabla desplegable de Productos -->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-boxes mr-2"></i>Catálogo de Productos
                                <div class="card-tools ml-auto">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-primary" style="background-color: #007bff!important;">
                                        <tr>
                                            <th style="width: 60px; text-align: center; color: white;">ID</th>
                                            <th style="color: white;">Producto</th>
                                            <th style="width: 100px; text-align: center; color: white;">Categoría</th>
                                            <th style="width: 80px; text-align: center; color: white;">Puntos</th>
                                            <th style="width: 80px; text-align: center; color: white;">Stock</th>
                                            <th style="width: 100px; text-align: center; color: white;">Estado</th>
                                            <th style="width: 120px; text-align: center; color: white;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-productos">
                                        <!-- Los productos se cargarán aquí dinámicamente -->
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando productos...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small id="contador-productos">Cargando...</small>
                        </div>
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
                        <input type="text" class="form-control" id="nombreCategoria" name="nombre" required autocomplete="off">
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
        // Cargar datos iniciales
        cargarTablaCategorias();
        
        // Configurar el evento para cambiar de pestaña
        $('#canjeParamsTab a').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("href");
            if (target === '#content-productos') {
                // Si se cambia a la pestaña de productos, cargar la tabla
                cargarTablaProductos();
                cargarFiltroCategorias();
            }
        });

        // Configurar búsqueda en tiempo real para productos
        const buscarProductoInput = document.getElementById('buscarProducto');
        if (buscarProductoInput) {
            let timeoutId;
            buscarProductoInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    cargarTablaProductos(this.value, document.getElementById('filtroCategoria').value);
                }, 500);
            });
        }

        // Configurar filtro por categoría
        const filtroCategoria = document.getElementById('filtroCategoria');
        if (filtroCategoria) {
            filtroCategoria.addEventListener('change', function() {
                cargarTablaProductos(document.getElementById('buscarProducto').value, this.value);
            });
        }

        // Configurar formulario de categoría
        const form_cat = document.getElementById('formCat');
        if (form_cat) {
            form_cat.addEventListener('submit', function(regCat){
                regCat.preventDefault();
                guardarCategoria();
            });
        }

        // Configurar formulario de producto
        const formProducto = document.getElementById('formProducto');
        if (formProducto) {
            formProducto.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarProducto();
            });
        }

        // Inicializar componentes UI
        inicializarComponentesUI();
    });

    // Función para cargar tabla de categorías
    async function cargarTablaCategorias() {
        try {
            const response = await fetch('./php/get_categorias.php');
            const data = await response.json();
            
            const tabla = document.getElementById('tabla-categorias');
            const contador = document.getElementById('contador-categorias');
            
            if (data && Array.isArray(data.categorias)) {
                tabla.innerHTML = '';
                
                if (data.categorias.length === 0) {
                    tabla.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox mr-2"></i>No hay categorías registradas
                            </td>
                        </tr>
                    `;
                } else {
                    let id = 1;
                    data.categorias.forEach((categoria) => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td class="text-center" style="border-left: 3px solid #17a2b8;">
                                <span class="badge bg-info">${id++}</span>
                            </td>
                            <td>
                                <i class="fas fa-folder text-info mr-2"></i>${categoria.nombre}
                            </td>
                            <td class="text-center">
                                <span class="badge ${categoria.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">
                                    ${categoria.estado || 'activo'}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" onclick="editarCategoria(${categoria.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger ml-1" onclick="eliminarCategoria(${categoria.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tabla.appendChild(fila);
                    });
                }
                
                contador.textContent = `Total: ${data.categorias.length} categorías`;
            } else {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Error al cargar las categorías
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error al cargar categorías:', error);
            const tabla = document.getElementById('tabla-categorias');
            tabla.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error de conexión al servidor
                    </td>
                </tr>
            `;
        }
    }

    // Función para cargar tabla de productos
    async function cargarTablaProductos(busqueda = '', categoriaId = '') {
        try {
            let url = './php/productos.php';
            const params = [];
            if (busqueda) params.push(`busqueda=${encodeURIComponent(busqueda)}`);
            if (categoriaId) params.push(`categoria_id=${categoriaId}`);
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            const tabla = document.getElementById('tabla-productos');
            const contador = document.getElementById('contador-productos');
            
            if (data && Array.isArray(data.productos)) {
                tabla.innerHTML = '';
                
                if (data.productos.length === 0) {
                    tabla.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox mr-2"></i>No hay productos registrados
                            </td>
                        </tr>
                    `;
                } else {
                    data.productos.forEach((producto) => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td class="text-center" style="border-left: 3px solid #007bff;">
                                <span class="badge bg-primary">${producto.id}</span>
                            </td>
                            <td>
                                <strong>${producto.nombre}</strong>
                                ${producto.descripcion ? `<br><small class="text-muted">${producto.descripcion}</small>` : ''}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">${producto.categoria_nombre || 'Sin categoría'}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">${producto.puntos}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${producto.stock > 10 ? 'bg-success' : producto.stock > 0 ? 'bg-warning text-dark' : 'bg-danger'}">
                                    ${producto.stock}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${producto.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">
                                    ${producto.estado}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" onclick="editarProducto(${producto.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger ml-1" onclick="eliminarProducto(${producto.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tabla.appendChild(fila);
                    });
                }
                
                contador.textContent = `Total: ${data.productos.length} productos`;
            } else {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Error al cargar los productos
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error al cargar productos:', error);
            const tabla = document.getElementById('tabla-productos');
            tabla.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error de conexión al servidor
                    </td>
                </tr>
            `;
        }
    }

    // Función para cargar categorías en el filtro
    async function cargarFiltroCategorias() {
        try {
            const response = await fetch('./php/get_categorias.php');
            const data = await response.json();
            
            const select = document.getElementById('filtroCategoria');
            if (select && data && Array.isArray(data.categorias)) {
                // Mantener el primer option
                select.innerHTML = '<option value="">Todas las categorías</option>';
                
                data.categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar categorías para filtro:', error);
        }
    }

    // Funciones para guardar datos
    async function guardarCategoria() {
        const form_cat = document.getElementById('formCat');
        const formData = new FormData(form_cat);
        
        try {
            const response = await fetch('./php/crear_categoria.php', {
                method: 'POST',
                body: formData
            });
            
            const resultado = await response.text();
            
            if (resultado.trim() === 'OK') {
                alertaPositiva_cat("Categoría creada exitosamente");
                form_cat.reset();
                $('#modalNuevaCategoria').modal('hide');
                cargarTablaCategorias();
                // Si estamos en la pestaña de productos, actualizar el filtro
                if ($('#tab-productos').hasClass('active')) {
                    cargarFiltroCategorias();
                }
            } else {
                mostrarAlerta_cat(resultado);
            }
        } catch (error) {
            console.error('Error al guardar categoría:', error);
            mostrarAlerta_cat('Error de conexión al servidor');
        }
    }

    async function guardarProducto() {
        const formData = new FormData(document.getElementById('formProducto'));
        
        try {
            const response = await fetch('./php/guardar_producto.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Producto guardado correctamente');
                cancelar_nuevo_producto();
                cargarTablaProductos();
            } else {
                alert('Error al guardar el producto: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error al guardar producto:', error);
            alert('Error de conexión al servidor');
        }
    }

    // Funciones de UI para productos
    function mostrar_nuevo_producto() {
        document.getElementById('form_nuevo_producto').style.display = 'block';
        cargarCategoriasParaFormulario();
    }

    function cancelar_nuevo_producto() {
        document.getElementById('form_nuevo_producto').style.display = 'none';
        document.getElementById('formProducto').reset();
    }

    // Funciones auxiliares para alertas (mantenidas de tu código original)
    function mostrarAlerta_cat(mensaje, tipo = 'danger', tiempo = 5000) {
        const alertContainer = document.getElementById('alertContainer_cat');
        mostrarAlerta(alertContainer, mensaje, tipo, tiempo, 'Error:');
    }

    function alertaPositiva_cat(mensaje, tipo = 'success', tiempo = 5000) {
        const alertContainer = document.getElementById('alertContainer_cat');
        mostrarAlerta(alertContainer, mensaje, tipo, tiempo, 'Éxito:');
    }

    function mostrarAlerta(container, mensaje, tipo = 'danger', tiempo = 5000, titulo = '') {
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
        alerta.setAttribute('role', 'alert');
        alerta.innerHTML = `
            ${titulo ? `<strong>${titulo}</strong><br>` : ''}${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        container.innerHTML = '';
        container.appendChild(alerta);
        
        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.remove();
            }
        }, tiempo);
    }

    // Funciones para cargar categorías en el formulario de productos
    async function cargarCategoriasParaFormulario() {
        try {
            const response = await fetch('./php/get_categorias.php');
            const data = await response.json();
            
            const select = document.getElementById('categoria_producto');
            if (select) {
                select.innerHTML = '<option value="">Seleccione una categoría</option>';
                
                if (data && Array.isArray(data.categorias)) {
                    data.categorias.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.nombre;
                        select.appendChild(option);
                    });
                }
            }
        } catch (error) {
            console.error('Error al cargar categorías:', error);
        }
    }

    // Funciones de edición y eliminación (placeholder - implementar según necesidad)
    function editarCategoria(id) {
        alert(`Editar categoría ID: ${id} - Implementar esta función`);
    }

    function eliminarCategoria(id) {
        if (confirm('¿Está seguro de eliminar esta categoría?')) {
            alert(`Eliminar categoría ID: ${id} - Implementar esta función`);
        }
    }

    function editarProducto(id) {
        alert(`Editar producto ID: ${id} - Implementar esta función`);
    }

    function eliminarProducto(id) {
        if (confirm('¿Está seguro de eliminar este producto?')) {
            alert(`Eliminar producto ID: ${id} - Implementar esta función`);
        }
    }

    function inicializarComponentesUI() {
        if (typeof $ !== 'undefined') {
            // Inicializar componentes de AdminLTE si es necesario
        }
    }
</script>