// Variables globales
let categorias = [];
let productos = [];
let kits_detalles = [];
let usuario = null;

// --- VARIABLES DE CARRITO ---
let carrito = [];
let carritoVisible = false;

// --- VARIABLES DE PAGINACION ---
let currentPage = 1;
let productsPerPage = 8;
let filteredProductos = [];

// --- FUNCIONES DE INICIALIZACIÓN ---

async function init(){
    try {
        console.log('Inicializando módulo de canje...');
        
        // Cargar catálogo
        const response = await fetch('php/productos.php');
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const data = await response.json();
        
        if (data.error) throw new Error(data.error);

        categorias = data.categorias;
        console.log('Categorias cargadas:', data.categorias);
        productos = data.productos;
        kits_detalles = data.kits_detalles || [];

        console.log('Datos cargados:', {
            categorias: categorias.length,
            productos: productos.length,
            kits: kits_detalles.length
        });

        // Inicializar filtros
        const sel = document.getElementById('catFilter');
        if (sel) {
            sel.innerHTML = '<option value="all">Todas las categorías</option>';
            categorias.forEach(c => {
                const op = document.createElement('option');
                op.value = c.id;
                op.textContent = c.nombre; 
                sel.appendChild(op);
            });
            
            // Resetear a página 1 cuando cambia el filtro
            sel.addEventListener('change', function() {
                currentPage = 1;
                renderProducts();
            });
        }
        
        // Cargar organizaciones y categorías para los selects
        cargarOrganizaciones();
        cargarCategorias();
        
        // Renderizar productos (página 1 por defecto)
        currentPage = 1;
        renderProducts();
        
        // AGREGAR EVENT DELEGATION PARA CLIC EN PRODUCTOS
        const productsList = document.getElementById('productsList');
        if (productsList) {
            productsList.addEventListener('click', function(event) {
                // Encontrar el elemento product-card clickeado
                let target = event.target;
                while (target && !target.classList.contains('product-card')) {
                    target = target.parentElement;
                }
                
                if (target && target.classList.contains('product-card')) {
                    // Encontrar el div col que contiene el ID del producto
                    let colDiv = target.closest('[data-product-id]');
                    if (colDiv && colDiv.dataset.productId) {
                        const productId = parseInt(colDiv.dataset.productId);
                        const product = productos.find(p => p.id === productId);
                        
                        if (product && (product.stock || 0) > 0) {
                            console.log('Producto seleccionado via delegation:', productId);
                            openProduct(productId);
                        } else if (product) {
                            alert('Este producto no tiene stock disponible');
                        }
                    }
                }
            });
        }
        
        // Inicializar carrito
        actualizarContadorCarrito();

        // Agregar evento para cerrar carrito al hacer clic fuera
        document.addEventListener('click', function(event) {
            const carritoDiv = document.getElementById('carritoContainer');
            const btnCarrito = document.getElementById('btnCarrito');
            
            if (carritoVisible && carritoDiv && btnCarrito && 
                !carritoDiv.contains(event.target) && 
                !btnCarrito.contains(event.target)) {
                toggleCarrito();
            }
        });

    } catch (error) {
        console.error('Error al inicializar:', error);
        alert('Error al cargar catálogo: ' + error.message);
    }
}

// --- FUNCIONES PARA SELECT DE ORGANIZACIONES Y NUEVOS PRODUCTOS ---

async function cargarOrganizaciones() {
    try {
        const response = await fetch('./php/get_organizaciones.php', { credentials: 'same-origin' });

        if (!response.ok) {
            const text = await response.text();
            let msg = text;
            try { const parsed = JSON.parse(text); msg = parsed.error || JSON.stringify(parsed); } catch(e) {}
            console.error('Error al cargar organizaciones (HTTP ' + response.status + '):', msg);
            const selectErr = document.getElementById('select_organizacion');
            if (selectErr) selectErr.innerHTML = `<option value="">Error ${response.status}: ${msg}</option>`;
            return;
        }

        const data = await response.json();
        
        const select = document.getElementById('select_organizacion');
        if (select) {
            select.innerHTML = '<option value="">Seleccione una organización...</option>';
            
            if (!data || !Array.isArray(data.organizaciones) || data.organizaciones.length === 0) {
                select.innerHTML = '<option value="">No se encontraron organizaciones</option>';
            } else {
                data.organizaciones.forEach(org => {
                    const option = document.createElement('option');
                    const val = (org.id !== undefined && org.id !== null) ? String(org.id) : '';
                    option.value = val;
                    option.textContent = org.nombre || '';
                    select.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error al cargar organizaciones:', error);
    }
}

async function cargarCategorias() {
    try {
        const response = await fetch('./php/get_categorias.php', { credentials: 'same-origin' });
        if (!response.ok) {
            const text = await response.text();
            console.error('Error al cargar categorías (HTTP ' + response.status + '):', text);
            return;
        }
        const data = await response.json();
        
        const select = document.getElementById('categoria_producto');
        if (select) {
            select.innerHTML = '<option value="">Seleccione una categoría</option>';
            
            data.categorias.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.nombre;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}

function seleccionarOrganizacion() {
    const select = document.getElementById('select_organizacion');
    const btnEntrar = document.getElementById('btn_entrar_org');
    if (btnEntrar) {
        btnEntrar.disabled = select.value === '';
    }
}

function entrarOrganizacion() {
    const select = document.getElementById('select_organizacion');
    if (!select) {
        console.error('select_organizacion no encontrado en DOM');
        return;
    }

    const orgId = (select.value || '').toString().trim();
    const orgNombre = (select.selectedOptions && select.selectedOptions[0] && select.selectedOptions[0].text)
        || (select.options && select.options[select.selectedIndex] && select.options[select.selectedIndex].text)
        || '';

    console.debug('entrarOrganizacion: orgId=', orgId, 'orgNombre=', orgNombre);
    if (orgId === '') {
        alert('Seleccione una organización válida antes de entrar.');
        return;
    }

    if (orgId !== '') {
        const inpNameEl = document.getElementById('inpName');
        if (inpNameEl) inpNameEl.value = orgNombre;
        usuario = {
            id: parseInt(orgId),
            nombre: orgNombre,
            puntos: 0
        };
        
        realizarLoginOrganizacion(orgId);
    }
}

async function realizarLoginOrganizacion(orgId) {
    console.log('=== INICIANDO LOGIN ORGANIZACIÓN ===');
    console.log('ID organización:', orgId);
    
    try {
        const response = await fetch(`php/organizacion.php?id=${encodeURIComponent(orgId)}`, { credentials: 'same-origin' });
        
        console.log('Respuesta organizacion.php:', response.status, response.statusText);

        if (!response.ok) {
            const text = await response.text();
            let msg = text;
            try { const parsed = JSON.parse(text); msg = parsed.error || JSON.stringify(parsed); } catch(e) {}
            if (response.status === 404) {
                alert('Organización no encontrada: ' + msg);
            } else if (response.status === 401) {
                alert('No autenticado. Inicia sesión antes de entrar en la organización');
            } else {
                alert('Error al iniciar sesión en la organización: ' + (msg || response.statusText));
            }
            console.error('realizarLoginOrganizacion HTTP error', response.status, msg);
            return;
        }

        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            const raw = await response.text();
            console.error('realizarLoginOrganizacion: respuesta no JSON =>', raw);
            alert('Respuesta inesperada del servidor al iniciar sesión. Revisa Network → organizacion.php en DevTools y pega la respuesta aquí.');
            return;
        }
        console.debug('realizarLoginOrganizacion data:', data);

        if (data.error) {
            alert('Acceso fallido: ' + data.error);
            return;
        }

        usuario = {
            id: data.id, 
            nombre: data.nombre, 
            puntos: data.puntos_acumulados 
        };

        console.log('Login exitoso:', usuario);

        // Actualizar interfaz
        const loginPanelEl = document.getElementById('loginPanel');
        if (loginPanelEl) loginPanelEl.style.display = 'none';
        document.getElementById('userPanel').style.display = 'block';
        document.getElementById('userName').textContent = usuario.nombre;
        document.getElementById('userPoints').textContent = usuario.puntos + ' pts';
        document.getElementById('summaryPoints').textContent = usuario.puntos;
        document.getElementById('summaryExchanges').textContent = data.total_canjes || 0; 
        
        console.log('Interfaz actualizada, ahora cargando historiales...');
        
        actualizarHistoriales();

    } catch (error) {
        console.error('Error al iniciar sesión:', error);
        alert('Error de conexión: ' + error.message);
    }
}

// --- FUNCIONES PARA NUEVOS PRODUCTOS ---

function mostrar_nuevo_producto() {
    const catalogo = document.getElementById('catalogo_productos');
    const formProducto = document.getElementById('form_nuevo_producto');
    
    if (catalogo) catalogo.style.display = 'none';
    if (formProducto) {
        formProducto.style.display = 'block';
        cargarCategorias();
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

// Manejar el envío del formulario de nuevo producto
document.addEventListener('DOMContentLoaded', function() {
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
                    init();
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
});

// --- FUNCIONES DE LOGIN ORIGINALES (MANTENIDAS) ---

async function login(){
    const name = document.getElementById('inpName').value.trim();
    if (!name) { 
        alert('Ingresa el nombre de la organización'); 
        return; 
    }
    
    try {
        document.getElementById('inpName').disabled = true;

        const response = await fetch(`php/organizacion.php?nombre=${encodeURIComponent(name)}`, { credentials: 'same-origin' });
        if (!response.ok) {
            const text = await response.text();
            let msg = text;
            try { const parsed = JSON.parse(text); msg = parsed.error || JSON.stringify(parsed); } catch(e) {}
            alert('Error al iniciar sesión por nombre: ' + (msg || response.statusText));
            console.error('login (nombre) error', response.status, msg);
            document.getElementById('inpName').disabled = false;
            return;
        }

        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            const raw = await response.text();
            console.error('login (nombre) respuesta no JSON =>', raw);
            alert('Respuesta inesperada del servidor al iniciar sesión por nombre. Revisa Network → organizacion.php en DevTools y pega la respuesta aquí.');
            document.getElementById('inpName').disabled = false;
            return;
        }

        if (data.error) {
            alert('Acceso fallido: ' + data.error);
            document.getElementById('inpName').disabled = false;
            return;
        }

        usuario = {
            id: data.id, 
            nombre: data.nombre, 
            puntos: data.puntos_acumulados 
        };

        console.log('Login exitoso:', usuario);

        const loginPanelEl2 = document.getElementById('loginPanel');
        if (loginPanelEl2) loginPanelEl2.style.display = 'none';
        document.getElementById('userPanel').style.display = 'block';
        document.getElementById('userName').textContent = usuario.nombre;
        document.getElementById('userPoints').textContent = usuario.puntos + ' pts';
        document.getElementById('summaryPoints').textContent = usuario.puntos;
        document.getElementById('summaryExchanges').textContent = data.total_canjes || 0; 
        
        actualizarHistoriales();

    } catch (error) {
        console.error('Error al iniciar sesión:', error);
        alert('Error de conexión: ' + error.message);
        document.getElementById('inpName').disabled = false;
    }
}

function logout(){
    usuario = null;
    const loginPanelEl3 = document.getElementById('loginPanel');
    if (loginPanelEl3) loginPanelEl3.style.display = 'block';
    document.getElementById('userPanel').style.display = 'none';
    document.getElementById('inpName').disabled = false;
    document.getElementById('inpName').value = '';
    
    const selectOrg = document.getElementById('select_organizacion');
    if (selectOrg) {
        selectOrg.value = '';
        const btnEntrar = document.getElementById('btn_entrar_org');
        if (btnEntrar) btnEntrar.disabled = true;
    }
    
    carrito = [];
    actualizarContadorCarrito();
    if (carritoVisible) {
        toggleCarrito();
    }
    
    actualizarInterfazHistoriales([], []);
}

// --- FUNCIONES DE INTERFAZ ---

function renderProducts(){
    const list = document.getElementById('productsList');
    if (!list) return;
    
    const filter = document.getElementById('catFilter');
    const filterValue = filter ? filter.value : 'all';
    
    // Filtrar productos según categoría
    filteredProducts = productos.filter(p => filterValue === 'all' ? true : p.cat == filterValue);
    
    // Si no hay productos
    if (filteredProducts.length === 0) {
        list.innerHTML = '<div class="col-12 text-center text-muted mt-3">No hay productos disponibles</div>';
        const paginationControls = document.getElementById('paginationControls');
        if (paginationControls) {
            paginationControls.style.display = 'none';
        }
        const paginationInfo = document.getElementById('paginationInfo');
        if (paginationInfo) {
            paginationInfo.textContent = '0 productos';
        }
        return;
    }
    
    // Calcular total de páginas
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    
    // Asegurar que currentPage esté en rango
    if (currentPage > totalPages) currentPage = 1;
    
    // Calcular índice de inicio y fin
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = Math.min(startIndex + productsPerPage, filteredProducts.length);
    
    // Obtener productos para la página actual
    const currentProducts = filteredProducts.slice(startIndex, endIndex);
    
    // Limpiar lista
    list.innerHTML = '';
    
    // Renderizar productos de la página actual
    currentProducts.forEach(p => {
        const catName = categorias.find(c => c.id == p.cat)?.nombre || 'Desconocida';
        const stock = p.stock || 0;
        const tieneStock = stock > 0;
        
        const colDiv = document.createElement('div');
        colDiv.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-3';
        colDiv.dataset.productId = p.id;
        
        const wrap = document.createElement('div'); 
        wrap.className = `product-card h-100 ${tieneStock ? 'producto-disponible' : 'producto-sin-stock'}`;
        wrap.style.display = 'flex';
        wrap.style.flexDirection = 'column';
        wrap.style.justifyContent = 'space-between';
                wrap.onclick = function() {
            debugProductClick(p.id);
            openProduct(p.id);
        };
        
        wrap.innerHTML = `
            <div>
                <strong>${p.nombre}</strong>
                <div class='text-sm text-muted mb-1'>${catName}</div>
                <small class="${tieneStock ? 'text-success' : 'text-danger'}">
                    <i class="fas fa-box mr-1"></i> Stock: ${stock}
                </small>
            </div>
            <div style='text-align:right; margin-top: auto;'>
                <div class='badge badge-success p-2' style="font-size: 1em;">
                    ${p.puntos} pts
                </div>
                ${!tieneStock ? '<div class="text-danger text-center mt-2"><small><i class="fas fa-times-circle mr-1"></i>Sin stock</small></div>' : ''}
            </div>
        `;
        
        colDiv.appendChild(wrap);
        list.appendChild(colDiv);
    });
    
    // Actualizar controles de paginación
    updatePaginationControls(totalPages, startIndex, endIndex);
}


// Función de depuración
function debugProductClick(productId) {
    console.log('DEBUG: Click en producto ID:', productId);
    console.log('Producto encontrado:', productos.find(p => p.id === productId));
    console.log('Función openProduct existe?', typeof openProduct);
}

function updatePaginationControls(totalPages, startIndex, endIndex) {
    const controls = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const currentPageInfo = document.getElementById('currentPageInfo');
    const pageInfo = document.getElementById('pageInfo');
    
    if (controls && paginationInfo) {
        // Mostrar/ocultar controles según sea necesario
        if (filteredProducts.length > productsPerPage) {
            controls.style.display = 'block';
            
            // Actualizar información de paginación
            paginationInfo.textContent = `Página ${currentPage} de ${totalPages}`;
            currentPageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
            pageInfo.textContent = `Mostrando ${startIndex + 1}-${endIndex} de ${filteredProducts.length} productos`;
            
            // Habilitar/deshabilitar botones
            if (prevPageBtn) {
                prevPageBtn.classList.toggle('disabled', currentPage === 1);
            }
            if (nextPageBtn) {
                nextPageBtn.classList.toggle('disabled', currentPage === totalPages);
            }
        } else {
            controls.style.display = 'none';
            paginationInfo.textContent = `${filteredProducts.length} productos`;
            pageInfo.textContent = `Mostrando todos los productos (${filteredProducts.length})`;
        }
    }
}

function changePage(direction) {
    const filter = document.getElementById('catFilter');
    const filterValue = filter ? filter.value : 'all';
    filteredProducts = productos.filter(p => filterValue === 'all' ? true : p.cat == filterValue);
    
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    
    const newPage = currentPage + direction;
    
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        renderProducts();
        
        // Scroll suave hacia arriba de la lista de productos
        const productsList = document.getElementById('productsList');
        if (productsList) {
            productsList.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

function goToPage(page) {
    const filter = document.getElementById('catFilter');
    const filterValue = filter ? filter.value : 'all';
    filteredProducts = productos.filter(p => filterValue === 'all' ? true : p.cat == filterValue);
    
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderProducts();
    }
}



function openProduct(id){
    const p = productos.find(x => x.id === id);
    if (!p) {
        alert('Producto no encontrado');
        return;
    }

    $('#productModal').modal('show');
    document.getElementById('modalTitle').textContent = p.nombre;
    const modalBody = document.getElementById('modalBody'); 
    modalBody.innerHTML='';
    
    const catName = categorias.find(c => c.id == p.cat)?.nombre || 'Desconocida';
    const stock = p.stock || 0;

    modalBody.innerHTML = `
        <p class="text-sm"><strong>Categoría:</strong> ${catName}</p>
        <p class="text-lg text-success"><strong>Puntos por unidad:</strong> ${p.puntos} pts</p>
        <p class="text-sm"><strong>Stock disponible:</strong> ${stock}</p>
        ${p.descripcion ? `<p class="text-sm">${p.descripcion}</p>` : ''}
        
        <hr>
        
        <div class="form-group">
            <label><strong>Cantidad a canjear:</strong></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary" type="button" id="decrementBtn">-</button>
                </div>
                <input type="number" 
                       id="cantidadModal" 
                       class="form-control text-center" 
                       value="1" 
                       min="1" 
                       max="${stock}"
                       data-product-id="${p.id}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="incrementBtn">+</button>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text">unidades</span>
                </div>
            </div>
            <small class="text-muted">Puntos totales: <span id="puntosTotalesModal">${p.puntos}</span> pts</small>
        </div>
    `;

    // Mostrar componentes del kit si es kit
    const kitComponents = kits_detalles.filter(k => k.kitId === p.id);
    if(kitComponents.length > 0){
        const comp = kitComponents.map(k => {
            const prod = productos.find(x => x.id === k.prodId);
            return `<li>${k.cantidad} × ${prod?.nombre || 'Producto desconocido'}</li>`;
        }).join('');
        
        modalBody.innerHTML += `
            <div class="callout callout-info mt-3">
                <h5>Componentes del kit:</h5>
                <ul class="text-sm">${comp}</ul>
            </div>
        `;
    }

    // Botones del modal
    const modalFooter = document.querySelector('#productModal .modal-footer');
    modalFooter.innerHTML = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnAgregarCarritoModal">
            <i class="fas fa-cart-plus mr-1"></i> Agregar al Carrito
        </button>
    `;
    
    // Si no hay stock, deshabilitar botón
    if (stock <= 0) {
        modalFooter.querySelector('#btnAgregarCarritoModal').disabled = true;
        modalBody.innerHTML += '<div class="alert alert-warning mt-2">Producto sin stock disponible</div>';
    }

    // Agregar event listeners después de que el modal está renderizado
    setTimeout(() => {
        // Botones de cantidad
        const decrementBtn = document.getElementById('decrementBtn');
        const incrementBtn = document.getElementById('incrementBtn');
        const cantidadInput = document.getElementById('cantidadModal');
        const agregarBtn = document.getElementById('btnAgregarCarritoModal');
        
        if (decrementBtn) {
            decrementBtn.addEventListener('click', () => {
                let nuevaCantidad = parseInt(cantidadInput.value) - 1;
                if (nuevaCantidad < 1) nuevaCantidad = 1;
                if (nuevaCantidad > stock) nuevaCantidad = stock;
                cantidadInput.value = nuevaCantidad;
                actualizarPuntosTotalesModal();
            });
        }
        
        if (incrementBtn) {
            incrementBtn.addEventListener('click', () => {
                let nuevaCantidad = parseInt(cantidadInput.value) + 1;
                if (nuevaCantidad < 1) nuevaCantidad = 1;
                if (nuevaCantidad > stock) nuevaCantidad = stock;
                cantidadInput.value = nuevaCantidad;
                actualizarPuntosTotalesModal();
            });
        }
        
        if (cantidadInput) {
            cantidadInput.addEventListener('change', () => {
                let cantidad = parseInt(cantidadInput.value) || 1;
                if (cantidad < 1) cantidad = 1;
                if (cantidad > stock) cantidad = stock;
                cantidadInput.value = cantidad;
                actualizarPuntosTotalesModal();
            });
        }
        
        if (agregarBtn) {
            agregarBtn.addEventListener('click', () => {
                const cantidad = parseInt(cantidadInput.value) || 1;
                agregarDesdeModal(p.id, cantidad);
            });
        }
        
        // Actualizar puntos iniciales
        actualizarPuntosTotalesModal();
    }, 100);
}

function modificarCantidadModal(cambio) {
    const input = document.getElementById('cantidadModal');
    const stock = parseInt(input.max);
    let nuevaCantidad = parseInt(input.value) + cambio;
    
    if (nuevaCantidad < 1) nuevaCantidad = 1;
    if (nuevaCantidad > stock) nuevaCantidad = stock;
    
    input.value = nuevaCantidad;
    actualizarPuntosTotalesModal();
}

function validarCantidadModal(puntosUnidad) {
    const input = document.getElementById('cantidadModal');
    const stock = parseInt(input.max);
    let cantidad = parseInt(input.value);
    
    if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
    if (cantidad > stock) cantidad = stock;
    
    input.value = cantidad;
    actualizarPuntosTotalesModal();
}

function actualizarPuntosTotalesModal() {
    const input = document.getElementById('cantidadModal');
    const productId = parseInt(input.dataset.productId);
    const p = productos.find(x => x.id === productId);
    
    if (!p) return;
    
    const cantidad = parseInt(input.value) || 1;
    const puntosTotales = p.puntos * cantidad;
    
    document.getElementById('puntosTotalesModal').textContent = puntosTotales;
}

function agregarDesdeModal(prodId, cantidad = null) {
    console.log('agregarDesdeModal llamado con prodId:', prodId);
    
    const inputCantidad = document.getElementById('cantidadModal');
    const cantidadFinal = cantidad || parseInt(inputCantidad?.value) || 1;
    
    console.log('Cantidad a agregar:', cantidadFinal);
    
    if (agregarAlCarrito(prodId, cantidadFinal)) {
        $('#productModal').modal('hide');
    }
}

function canjear(prodId) {
    if(!usuario){ 
        alert('Inicia sesión primero.'); 
        return; 
    }

    const p = productos.find(x => x.id === prodId);
    if (!p) {
        alert('Producto no encontrado');
        return;
    }
    
    openProduct(prodId);
}

// --- FUNCIONES DEL CARRITO ---

function toggleCarrito() {
    carritoVisible = !carritoVisible;
    const carritoDiv = document.getElementById('carritoContainer');
    if (carritoDiv) {
        carritoDiv.style.display = carritoVisible ? 'block' : 'none';
    }
    if (carritoVisible) {
        renderCarrito();
    }
}

function agregarAlCarrito(prodId, cantidad = 1) {
    console.log('agregarAlCarrito:', { prodId, cantidad, usuario, productos });
    
    if (!usuario) {
        console.log('No hay usuario logueado');
        alert('Debes iniciar sesión para agregar productos al carrito.');
        return false;
    }

    const producto = productos.find(p => p.id === prodId);
    console.log('Producto encontrado:', producto);
    
    if (!producto) {
        console.log('Producto no encontrado con ID:', prodId);
        alert('Producto no encontrado.');
        return false;
    }

    // Verificar si ya existe en el carrito
    const existeIndex = carrito.findIndex(item => parseInt(item.id) === parseInt(prodId));
    
    if (existeIndex >= 0) {
        carrito[existeIndex].cantidad += cantidad;
    } else {
        carrito.push({
            id: prodId,
            nombre: producto.nombre,
            puntos: producto.puntos,
            cantidad: cantidad,
            stock: producto.stock || 0,
            categoria: categorias.find(c => c.id == producto.cat)?.nombre || 'General'
        });
    }

    // Validar que no exceda los puntos disponibles
    if (!validarCarrito()) {
        if (existeIndex >= 0) {
            carrito[existeIndex].cantidad -= cantidad;
            if (carrito[existeIndex].cantidad <= 0) {
                carrito.splice(existeIndex, 1);
            }
        } else {
            carrito.pop();
        }
        alert('No tienes puntos suficientes para agregar este producto.');
        return false;
    }

    // Validar que no exceda el stock disponible
    const itemCarrito = carrito.find(item => item.id === prodId);
    if (itemCarrito && itemCarrito.cantidad > itemCarrito.stock) {
        alert(`Stock insuficiente. Solo hay ${itemCarrito.stock} unidades disponibles.`);
        itemCarrito.cantidad = itemCarrito.stock;
    }

    // Actualizar interfaz
    actualizarContadorCarrito();
    if (carritoVisible) {
        renderCarrito();
    }

    alert(`Producto agregado al carrito: ${producto.nombre} (x${cantidad})`);
    return true;
}

function eliminarDelCarrito(prodId) {
    const index = carrito.findIndex(item => parseInt(item.id) === parseInt(prodId));
    if (index >= 0) {
        carrito.splice(index, 1);
        actualizarContadorCarrito();
        if (carritoVisible) {
            renderCarrito();
        }
    }
}

function actualizarCantidadCarrito(prodId, nuevaCantidad) {
    const item = carrito.find(item => item.id === prodId);
    if (item) {
        nuevaCantidad = parseInt(nuevaCantidad) || 1;
        
        // Validar stock
        if (nuevaCantidad > item.stock) {
            alert(`No hay suficiente stock. Máximo disponible: ${item.stock}`);
            nuevaCantidad = item.stock;
        }
        
        item.cantidad = nuevaCantidad;
        
        // Si cantidad es 0, eliminar del carrito
        if (nuevaCantidad <= 0) {
            eliminarDelCarrito(prodId);
        } else {
            // Validar puntos
            if (!validarCarrito()) {
                alert('No tienes puntos suficientes para esta cantidad.');
                const puntosDisponibles = usuario.puntos;
                const puntosOtrosItems = carrito
                    .filter(i => i.id !== prodId)
                    .reduce((sum, i) => sum + (i.puntos * i.cantidad), 0);
                
                const puntosRestantes = puntosDisponibles - puntosOtrosItems;
                const cantidadMaxima = Math.floor(puntosRestantes / item.puntos);
                
                if (cantidadMaxima <= 0) {
                    eliminarDelCarrito(prodId);
                } else {
                    item.cantidad = cantidadMaxima;
                    alert(`Cantidad ajustada a ${cantidadMaxima} (máximo con tus puntos disponibles)`);
                }
            }
        }
        
        actualizarContadorCarrito();
        if (carritoVisible) {
            renderCarrito();
        }
    }
}

function calcularTotalCarrito() {
    return carrito.reduce((total, item) => total + (item.puntos * item.cantidad), 0);
}

function validarCarrito() {
    if (!usuario) return false;
    const totalPuntos = calcularTotalCarrito();
    return totalPuntos <= usuario.puntos;
}

function actualizarContadorCarrito() {
    const contador = document.getElementById('carritoContador');
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    const totalPuntos = calcularTotalCarrito();
    
    if (contador) {
        contador.textContent = totalItems;
        contador.style.display = totalItems > 0 ? 'inline' : 'none';
    }
    
    const btnCarrito = document.getElementById('btnCarrito');
    if (btnCarrito) {
        const badge = btnCarrito.querySelector('.badge') || document.createElement('span');
        badge.className = 'badge badge-light ml-1';
        badge.textContent = `${totalPuntos} pts`;
        if (!btnCarrito.querySelector('.badge')) {
            btnCarrito.appendChild(badge);
        } else {
            btnCarrito.querySelector('.badge').textContent = `${totalPuntos} pts`;
        }
    }
}

function renderCarrito() {
    const container = document.getElementById('carritoContainer');
    if (!container) return;
    
    const totalPuntos = calcularTotalCarrito();
    const puntosRestantes = usuario ? usuario.puntos - totalPuntos : 0;
    
    let html = `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart mr-2"></i>Carrito de Canje
                    <button type="button" class="close text-white" onclick="toggleCarrito()" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
    `;
    
    if (carrito.length === 0) {
        html += `<p class="text-muted text-center">El carrito está vacío</p>`;
    } else {
        html += `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th width="100">Cantidad</th>
                            <th width="100">Puntos</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        carrito.forEach(item => {
            const subtotal = item.puntos * item.cantidad;
            html += `
                <tr>
                    <td>
                        <small><strong>${item.nombre}</strong></small><br>
                        <small class="text-muted">${item.categoria}</small>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" 
                                   class="form-control" 
                                   value="${item.cantidad}" 
                                   min="1" 
                                   max="${item.stock}"
                                   onchange="actualizarCantidadCarrito(${item.id}, this.value)"
                                   style="max-width: 70px;">
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info">${subtotal} pts</span>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick=eliminarDelCarrito(${item.id})>
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-6">
                    <strong>Total Puntos:</strong>
                </div>
                <div class="col-6 text-right">
                    <h5 class="text-success">${totalPuntos} pts</h5>
                </div>
            </div>
            
            <div class="row">
                <div class="col-6">
                    <strong>Puntos Disponibles:</strong>
                </div>
                <div class="col-6 text-right">
                    <h5>${usuario.puntos} pts</h5>
                </div>
            </div>
            
            <div class="row">
                <div class="col-6">
                    <strong>Puntos Restantes:</strong>
                </div>
                <div class="col-6 text-right">
                    <h5 class="${puntosRestantes < 0 ? 'text-danger' : 'text-success'}">
                        ${puntosRestantes} pts
                    </h5>
                </div>
            </div>
        `;
    }
    
    html += `
            </div>
            <div class="card-footer">
                <button class="btn btn-danger btn-sm" onclick="vaciarCarrito()" ${carrito.length === 0 ? 'disabled' : ''}>
                    <i class="fas fa-trash mr-1"></i> Vaciar
                </button>
                <button class="btn btn-success btn-sm float-right" onclick="procesarCanje()" id="btnProcesarCanje" ${carrito.length === 0 || !validarCarrito() ? 'disabled' : ''}>
                    <i class="fas fa-check mr-1"></i> Canjear
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    const btnProcesar = document.getElementById('btnProcesarCanje');
    if (btnProcesar) {
        btnProcesar.disabled = carrito.length === 0 || !validarCarrito();
    }
}

function vaciarCarrito() {
    if (confirm('¿Estás seguro de vaciar el carrito?')) {
        carrito = [];
        actualizarContadorCarrito();
        if (carritoVisible) {
            renderCarrito();
        }
    }
}

async function procesarCanje() {
    if (!usuario || carrito.length === 0) return;
    
    if (!validarCarrito()) {
        alert('No tienes puntos suficientes para realizar este canje.');
        return;
    }
    
    const productosCanje = carrito.map(item => ({
        id: item.id,
        cantidad: item.cantidad,
        puntos_unidad: item.puntos
    }));
    
    const totalPuntos = calcularTotalCarrito();
    
    const resumen = carrito.map(item => 
        `${item.cantidad} × ${item.nombre} (${item.puntos * item.cantidad} pts)`
    ).join('\n');
    
    if (!confirm(`¿Confirmar canje por ${totalPuntos} puntos?\n\n${resumen}`)) {
        return;
    }
    
    try {
        const response = await fetch('php/canjear.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                organizacion_id: usuario.id, 
                productos: productosCanje,
                puntos_usados: totalPuntos
            })
        });

        if (!response.ok) throw new Error(`Error HTTP ${response.status}`);

        const data = await response.json();

        if (data.success) {
            if (typeof data.nuevo_puntos !== 'undefined') {
                usuario.puntos = Number(data.nuevo_puntos);
            }
            
            document.getElementById('userPoints').textContent = usuario.puntos + ' pts';
            document.getElementById('summaryPoints').textContent = usuario.puntos;
            document.getElementById('summaryExchanges').textContent = data.nuevo_total_canjes || document.getElementById('summaryExchanges').textContent;
            
            carrito = [];
            actualizarContadorCarrito();
            if (carritoVisible) {
                renderCarrito();
                toggleCarrito();
            }
            
            alert('Canje realizado con éxito.');
            
            actualizarHistoriales();
            await init();
            
        } else {
            alert('Falló el canje: ' + (data.message || 'Error desconocido.'));
        }

    } catch (error) {
        console.error('Error durante el canje:', error);
        alert('Hubo un error de conexión: ' + error.message);
    }
}

// --- FUNCIONES PARA HISTORIALES ---

function renderHistorials(canjes = [], reciclaje = []){
    const rec = document.getElementById('reciclajeList'); 
    if (rec) {
        rec.innerHTML='';
        if (reciclaje.length === 0) {
            rec.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de reciclaje.</p>';
        }
    }
    
    const can = document.getElementById('canjesList'); 
    if (can) {
        can.innerHTML='';
        if (canjes.length === 0) {
            can.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de canjes.</p>';
        }
    }
}

async function cargarHistorialCanje() {
    if (!usuario) {
        console.log('No hay usuario, no se puede cargar historial canje');
        return [];
    }
    
    console.log('Cargando historial canje para usuario ID:', usuario.id);
    
    try {
        const response = await fetch(`php/historial_canje.php?id=${usuario.id}`);
        
        console.log('Respuesta de historial_canje.php:', response.status, response.statusText);
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const data = await response.json();
        console.log('Datos de historial canje recibidos:', data);
        
        if (data.error) {
            console.error('Error en historial canje:', data.error);
            return [];
        }
        
        console.log('Historial canje cargado:', data.canjes?.length || 0, 'registros');
        return data.canjes || [];
        
    } catch (error) {
        console.error('Error cargando historial canje:', error);
        return [];
    }
}

async function cargarHistorialReciclaje() {
    if (!usuario) {
        console.log('No hay usuario, no se puede cargar historial reciclaje');
        return [];
    }
    
    console.log('Cargando historial reciclaje para usuario ID:', usuario.id);
    
    try {
        const response = await fetch(`php/historial_reciclaje.php?id=${usuario.id}`);
        
        console.log('Respuesta de historial_reciclaje.php:', response.status, response.statusText);
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const data = await response.json();
        console.log('Datos de historial reciclaje recibidos:', data);
        
        if (data.error) {
            console.error('Error en historial reciclaje:', data.error);
            return [];
        }
        
        console.log('Historial reciclaje cargado:', data.reciclajes?.length || 0, 'registros');
        return data.reciclajes || [];
        
    } catch (error) {
        console.error('Error cargando historial reciclaje:', error);
        return [];
    }
}

async function actualizarHistoriales() {
    if (!usuario) {
        console.log('No hay usuario, no se pueden actualizar historiales');
        return { historialCanje: [], historialReciclaje: [] };
    }
    
    console.log('Actualizando historiales para:', usuario.nombre);
    
    try {
        const [historialCanje, historialReciclaje] = await Promise.all([
            cargarHistorialCanje(),
            cargarHistorialReciclaje()
        ]);
        
        console.log('Historiales cargados:', {
            canje: historialCanje.length,
            reciclaje: historialReciclaje.length
        });
        
        const recList = document.getElementById('reciclajeList');
        const canList = document.getElementById('canjesList');
        
        console.log('Elementos DOM encontrados:', {
            reciclajeList: !!recList,
            canjesList: !!canList
        });
        
        actualizarInterfazHistoriales(historialCanje, historialReciclaje);
        
        return { historialCanje, historialReciclaje };
        
    } catch (error) {
        console.error('Error actualizando historiales:', error);
        return { historialCanje: [], historialReciclaje: [] };
    }
}

function actualizarInterfazHistoriales(canjes, reciclajes) {
    console.log('Actualizando interfaz con:', {
        canjes: canjes.length,
        reciclajes: reciclajes.length
    });
    
    const recList = document.getElementById('reciclajeList'); 
    if (recList) {
        console.log('Elemento reciclajeList encontrado, actualizando...');
        recList.innerHTML = '';
        
        if (reciclajes.length === 0) {
            console.log('No hay reciclajes para mostrar');
            recList.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de reciclaje.</p>';
        } else {
            console.log('Mostrando', reciclajes.length, 'registros de reciclaje');
            const table = document.createElement('div');
            table.className = 'table-responsive';
            table.innerHTML = `
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Correlativo</th>
                            <th>Materiales</th>
                            <th>Puntos</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody id="reciclajeTableBody"></tbody>
                </table>
            `;
            recList.appendChild(table);
            
            const tbody = document.getElementById('reciclajeTableBody');
            reciclajes.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.fecha_formateada || '-'}</td>
                    <td>${item.correlativo || '-'}</td>
                    <td>${item.materiales || '-'}</td>
                    <td><span class="badge badge-success">${item.puntos || 0} pts</span></td>
                    <td>${item.responsable || '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        }
    } else {
        console.error('Elemento reciclajeList NO encontrado en el DOM');
    }
    
    const canList = document.getElementById('canjesList'); 
    if (canList) {
        console.log('Elemento canjesList encontrado, actualizando...');
        canList.innerHTML = '';
        
        if (canjes.length === 0) {
            console.log('No hay canjes para mostrar');
            canList.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de canjes.</p>';
        } else {
            console.log('Mostrando', canjes.length, 'registros de canjes');
            const table = document.createElement('div');
            table.className = 'table-responsive';
            table.innerHTML = `
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Puntos</th>
                        </tr>
                    </thead>
                    <tbody id="canjesTableBody"></tbody>
                </table>
            `;
            canList.appendChild(table);
            
            const tbody = document.getElementById('canjesTableBody');
            canjes.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.fecha_formateada || '-'}</td>
                    <td>${item.producto || '-'}</td>
                    <td><span class="badge badge-warning">${item.total_puntos || 0} pts</span></td>
                `;
                tbody.appendChild(tr);
            });
        }
    } else {
        console.error('Elemento canjesList NO encontrado en el DOM');
    }
}

console.log('canje_logic.js cargado - listo para inicialización');