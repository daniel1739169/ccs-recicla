<div id="canje" class="row" style="display: none;">
    
    <div class="col-md-3">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-circle"></i> Acceso Organización</h3>
            </div>
            <div class="card-body box-profile">
                
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Acceso Organización</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Selecciona la organización para registrar un canje</label>
            <select class="form-control" id="select_organizacion" onchange="seleccionarOrganizacion()">
                <option value="">Seleccione una organización...</option>
                <!-- Las organizaciones se cargarán dinámicamente -->
            </select>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="btn_entrar_org" disabled onclick="entrarOrganizacion()">
            Entrar
        </button>
    </div>
</div>

                <div id="userPanel" style="display: none;">
                    <h3 class="profile-username text-center" id="userName">Nombre Org</h3>
                    <p class="text-muted text-center">Puntos Acumulados</p>
                    <h1 class="text-center text-success" id="userPoints">0 pts</h1>
                    <button class="btn btn-default btn-block btn-sm" onclick="logout()">Cambiar Org</button>
                    
                    <hr>
                    
                    <strong><i class="fas fa-exchange-alt mr-1"></i> Resumen</strong>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Puntos restantes</b> <a class="float-right" id="summaryPoints">0</a>
                        </li>
                        <li class="list-group-item">
                            <b>Canjes realizados</b> <a class="float-right" id="summaryExchanges">0</a>
                        </li>
                    </ul>
                </div>
                
            </div>
        </div>
        
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Ayuda Rápida</h3>
            </div>
            <div class="card-body">
                <p class="text-sm">
                    Los puntos se calculan automáticamente desde los recibos finales de reciclaje.
                </p>
            </div>
        </div>
        
    </div>
    
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sitemap"></i> Catálogo de Productos</h3>
                <div class="card-tools">
                    <select id="catFilter" class="form-control form-control-sm">
                        <option value="all">Todas las categorías</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="productsList" class="product-list">
                    Cargando productos...
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Historiales</h3>
            </div>
            <div class="card-body">
                
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-canjes" data-toggle="pill" href="#content-canjes" role="tab" aria-controls="content-canjes" aria-selected="true">Canjes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-reciclaje" data-toggle="pill" href="#content-reciclaje" role="tab" aria-controls="content-reciclaje" aria-selected="false">Reciclaje</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="content-canjes" role="tabpanel" aria-labelledby="tab-canjes">
                        <div id="canjesList" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-muted text-sm mt-3">Inicia sesión para ver el historial de canjes.</p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="content-reciclaje" role="tabpanel" aria-labelledby="tab-reciclaje">
                         <div id="reciclajeList" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-muted text-sm mt-3">Inicia sesión para ver el historial de reciclaje.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="modalTitle">Detalles del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .product-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .product-card {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    .product-card:hover {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 0.85rem;
    }
</style>