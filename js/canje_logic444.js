// Variables globales
let categorias = [];
let productos = [];
let kits_detalles = [];
let usuario = null;

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
                // el API devuelve 'id' (y también 'id_categoria' para compatibilidad)
                op.value = c.id;
                op.textContent = c.nombre; 
                sel.appendChild(op);
            });
            
            sel.addEventListener('change', renderProducts);
        }
        
        // Cargar organizaciones y categorías para los selects
        cargarOrganizaciones();
        cargarCategorias();
        
        renderProducts();

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
            // mostrar una opción por defecto con detalle para debug
            const selectErr = document.getElementById('select_organizacion');
            if (selectErr) selectErr.innerHTML = `<option value="">Error ${response.status}: ${msg}</option>`;
            return;
        }

        const data = await response.json();
        
        const select = document.getElementById('select_organizacion');
        if (select) {
            select.innerHTML = '<option value="">Seleccione una organización...</option>';
            
            if (!data || !Array.isArray(data.organizaciones) || data.organizaciones.length === 0) {
                // mostrar mensaje cuando no hay organizaciones
                select.innerHTML = '<option value="">No se encontraron organizaciones</option>';
            } else {
                data.organizaciones.forEach(org => {
                    const option = document.createElement('option');
                    // defensiva: asegurar que exista un id válido
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

    // Asegurarnos de obtener el value (string) y el texto seleccionado de forma defensiva
    const orgId = (select.value || '').toString().trim();
    // selectedOptions puede no existir en algunos navegadores o si no hay selección
    const orgNombre = (select.selectedOptions && select.selectedOptions[0] && select.selectedOptions[0].text)
        || (select.options && select.options[select.selectedIndex] && select.options[select.selectedIndex].text)
        || '';

    // Considerar vacío '' como no seleccionado — evitar usar if(orgId) con 0
    console.debug('entrarOrganizacion: orgId=', orgId, 'orgNombre=', orgNombre);
    if (orgId === '') {
        alert('Seleccione una organización válida antes de entrar.');
        return;
    }

    // Si orgId existe (puede ser '0' u otro id) procedemos
    if (orgId !== '') {
        // Usar la función login existente pero con el ID de la organización
        // Es posible que el campo 'inpName' no exista en todas las vistas — asignar sólo si existe
        const inpNameEl = document.getElementById('inpName');
        if (inpNameEl) inpNameEl.value = orgNombre;
        usuario = {
            id: parseInt(orgId),
            nombre: orgNombre,
            puntos: 0 // Se cargarán después del login
        };
        
        // Simular el proceso de login
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
        
        // Cargar y mostrar los historiales después del login
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
        cargarCategorias(); // Cargar categorías en el select
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
                    // Recargar productos
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

        // Actualizar interfaz
        const loginPanelEl2 = document.getElementById('loginPanel');
        if (loginPanelEl2) loginPanelEl2.style.display = 'none';
        document.getElementById('userPanel').style.display = 'block';
        document.getElementById('userName').textContent = usuario.nombre;
        document.getElementById('userPoints').textContent = usuario.puntos + ' pts';
        document.getElementById('summaryPoints').textContent = usuario.puntos;
        document.getElementById('summaryExchanges').textContent = data.total_canjes || 0; 
        
        // Cargar y mostrar los historiales después del login
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
    
    // Resetear select de organizaciones
    const selectOrg = document.getElementById('select_organizacion');
    if (selectOrg) {
        selectOrg.value = '';
        const btnEntrar = document.getElementById('btn_entrar_org');
        if (btnEntrar) btnEntrar.disabled = true;
    }
    
    // Limpiar historiales al cerrar sesión
    actualizarInterfazHistoriales([], []);
}

// --- FUNCIONES DE INTERFAZ ---

function renderProducts(){
    const list = document.getElementById('productsList');
    if (!list) return;
    
    list.innerHTML='';
    const filter = document.getElementById('catFilter');
    const filterValue = filter ? filter.value : 'all';
    const filtered = productos.filter(p => filterValue === 'all' ? true : p.cat == filterValue);
    
    if (productos.length === 0) {
        list.innerHTML = '<div class="col-12 text-center text-muted mt-3">No hay productos disponibles</div>';
        return;
    }

    filtered.forEach(p => {
        // usar comparación tolerante para evitar problemas de tipo (string vs number)
        const catName = categorias.find(c => c.id == p.cat)?.nombre || 'Desconocida';
        
        // Crear la columna Bootstrap que contendrá la tarjeta
        const colDiv = document.createElement('div');
        colDiv.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-3';

        // Dentro de la columna, crear la tarjeta real
        const wrap = document.createElement('div'); 
        wrap.className='product-card h-100';
        wrap.onclick = () => openProduct(p.id);

        wrap.innerHTML = `
            <div>
                <strong>${p.nombre}</strong>
                <div class='text-sm text-muted'>${catName}</div>
                ${p.stock !== undefined ? `<small class="text-info">Stock: ${p.stock}</small>` : ''}
            </div>
            <div style='text-align:right'>
                <div class='badge badge-success'>${p.puntos} pts</div>
                <button class='btn btn-xs btn-outline-success mt-1'>Ver</button>
            </div>
        `;
        
        colDiv.appendChild(wrap);
        list.appendChild(colDiv);
    });
}

function openProduct(id){
    const p = productos.find(x => x.id === id);
    if (!p) return;

    $('#productModal').modal('show');
    document.getElementById('modalTitle').textContent = p.nombre;
    const modalBody = document.getElementById('modalBody'); 
    modalBody.innerHTML='';
    
    const catName = categorias.find(c => c.id == p.cat)?.nombre || 'Desconocida';

    modalBody.innerHTML=`
        <p class="text-sm"><strong>Categoría:</strong> ${catName}</p>
        <p class="text-lg text-success"><strong>Puntos requeridos:</strong> ${p.puntos} pts</p>
        ${p.descripcion ? `<p class="text-sm">${p.descripcion}</p>` : ''}
        ${p.stock !== undefined ? `<p class="text-sm"><strong>Stock disponible:</strong> ${p.stock}</p>` : ''}
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

    // Botón de Canje
    const modalFooter = document.querySelector('#productModal .modal-footer');
    const oldButtons = modalFooter.querySelectorAll('button.btn-success');
    oldButtons.forEach(btn => btn.remove());

    if(usuario && usuario.puntos >= p.puntos) {
        const buyBtn = document.createElement('button');
        buyBtn.type = 'button';
        buyBtn.className = 'btn btn-success';
        buyBtn.textContent = 'Canjear Ahora';
        buyBtn.onclick = () => canjear(p.id);
        modalFooter.appendChild(buyBtn);
    }
}

async function canjear(prodId){
    if(!usuario){ 
        alert('Inicia sesión primero.'); 
        return; 
    }

    const p = productos.find(x => x.id === prodId);
    if (!p) {
        alert('Producto no encontrado');
        return;
    }
    
    if(usuario.puntos < p.puntos){ 
        alert(`Puntos insuficientes. Necesitas ${p.puntos} pts y tienes ${usuario.puntos} pts.`); 
        return; 
    }

    if(!confirm(`¿Desea canjear "${p.nombre}" por ${p.puntos} puntos?`)){
        return;
    }
    
    $('#productModal').modal('hide');

    try {
        const response = await fetch('php/canjear.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 
                organizacion_id: usuario.id, 
                producto_id: prodId,
                puntos_usados: p.puntos
            })
        });

        if (!response.ok) throw new Error(`Error HTTP ${response.status}`);

        const data = await response.json();

        if (data.success) {
            // Preferir el saldo devuelto por el servidor (autoritativo)
            if (typeof data.nuevo_puntos !== 'undefined') {
                usuario.puntos = Number(data.nuevo_puntos);
            } else {
                usuario.puntos = usuario.puntos - p.puntos; // fallback
            }

            document.getElementById('userPoints').textContent = usuario.puntos + ' pts';
            document.getElementById('summaryPoints').textContent = usuario.puntos;
            document.getElementById('summaryExchanges').textContent = data.nuevo_total_canjes || document.getElementById('summaryExchanges').textContent;
            
            alert('Canje realizado con éxito.');
            
            // Actualizar historiales después del canje
            actualizarHistoriales();
            
        } else {
            alert('Fallo el canje: ' + (data.message || 'Error desconocido.'));
        }

    } catch (error) {
        console.error('Error durante el canje:', error);
        alert('Hubo un error de conexión: ' + error.message);
    }
}

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

// --- FUNCIONES PARA HISTORIALES ---

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
        // Cargar ambos historiales en paralelo
        const [historialCanje, historialReciclaje] = await Promise.all([
            cargarHistorialCanje(),
            cargarHistorialReciclaje()
        ]);
        
        console.log('Historiales cargados:', {
            canje: historialCanje.length,
            reciclaje: historialReciclaje.length
        });
        
        // Verificar si los elementos existen en el DOM
        const recList = document.getElementById('reciclajeList');
        const canList = document.getElementById('canjesList');
        
        console.log('Elementos DOM encontrados:', {
            reciclajeList: !!recList,
            canjesList: !!canList
        });
        
        // Actualizar la interfaz
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
    
    // Actualizar lista de reciclaje
    const recList = document.getElementById('reciclajeList'); 
    if (recList) {
        console.log('Elemento reciclajeList encontrado, actualizando...');
        recList.innerHTML = '';
        
        if (reciclajes.length === 0) {
            console.log('No hay reciclajes para mostrar');
            recList.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de reciclaje.</p>';
        } else {
            console.log('Mostrando', reciclajes.length, 'registros de reciclaje');
            // Crear tabla responsiva para reciclaje
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
    
    // Actualizar lista de canjes
    const canList = document.getElementById('canjesList'); 
    if (canList) {
        console.log('Elemento canjesList encontrado, actualizando...');
        canList.innerHTML = '';
        
        if (canjes.length === 0) {
            console.log('No hay canjes para mostrar');
            canList.innerHTML = '<p class="text-muted text-sm mt-3">No hay registros de canjes.</p>';
        } else {
            console.log('Mostrando', canjes.length, 'registros de canjes');
            // Crear tabla responsiva para canjes
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