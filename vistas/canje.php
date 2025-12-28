<div class="container">

<div id="canje" class="row d-none;">
    
    <!-- Columna 1: Acceso Organización + Ayuda Rápida -->
    <div class="col-12 col-md-3">
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
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" id="btn_entrar_org" disabled onclick="entrarOrganizacion()">
                            Entrar
                        </button>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button class="btn btn-warning btn-block" onclick="toggleCarrito()" id="btnCarrito">
                        <i class="fas fa-shopping-cart mr-2"></i>Ver Carrito
                        <span class="badge badge-light ml-1" id="carritoContador" style="display: none;">0</span>
                    </button>
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

        <div class="card card-warning card-outline mt-3">
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
    
    <div id="carritoContainer" style="display: none; position: absolute; z-index: 1000; width: 365px; max-width: 400px; right: 73px; margin-top: 0px;"></div>


    <!-- Columna 2: Catálogo de Productos -->
    <div class="col-12 col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sitemap"></i> Catálogo de Productos</h3>
                <div class="card-tools d-flex">
                    <select id="catFilter" class="form-control form-control-sm mr-2" style="max-width: 200px;">
                        <option value="all">Todas las categorías</option>
                    </select>
                    <div class="pagination-info ml-2 align-self-center text-sm text-muted" id="paginationInfo">
                        Página 1 de 1
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Tabla de productos paginada -->
                <div id="productsTableContainer">
                    <div id="productsList" class="row product-list">
                        Cargando productos...
                    </div>
                    
                    <!-- Controles de paginación -->
                    <div class="pagination-controls mt-3" id="paginationControls" style="display: none;">
                        <nav aria-label="Paginación de productos">
                            <ul class="pagination justify-content-center pagination-sm">
                                <li class="page-item disabled" id="prevPage">
                                    <a class="page-link" href="javascript:void(0)" onclick="changePage(-1)">&laquo; Anterior</a>
                                </li>
                                <li class="page-item">
                                    <span class="page-link" id="currentPageInfo">Página 1</span>
                                </li>
                                <li class="page-item" id="nextPage">
                                    <a class="page-link" href="javascript:void(0)" onclick="changePage(1)">Siguiente &raquo;</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="text-center">
                            <small class="text-muted" id="pageInfo">Mostrando 0 de 0 productos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalle de producto -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Detalle del producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body" id="modalBody">
        <!-- Aquí se inyecta el contenido desde openProduct() -->
      </div>

      <div class="modal-footer">
        <!-- Aquí se inyectan los botones desde openProduct() -->
      </div>
      
    </div>
  </div>
</div>



    <!-- Columna 3: Historiales -->
    <div class="col-12 col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Historiales</h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-canjes" data-toggle="pill" href="#content-canjes" role="tab" aria-controls="content-canjes" aria-selected="true">Canje</a>
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

<style>
    .product-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        height: 100%;
    }
    
    .producto-disponible {
        cursor: pointer;
    }
    
    .producto-disponible:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #007bff;
    }
    
    .producto-disponible:active {
        transform: translateY(-1px);
    }
    
    .producto-sin-stock {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }
    
    .producto-sin-stock:hover {
        transform: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-color: #dee2e6;
    }
    
</style>

<style>
    #carritoContainer {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 5px;
    }
    .product-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .input-group-sm input[type="number"] {
        max-width: 70px;
    }
</style>
