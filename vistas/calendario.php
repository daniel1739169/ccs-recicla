<style>
  /* Ajustes mínimos para mantener compatibilidad con AdminLTE y FullCalendar */
  .fc-event { cursor: pointer; }
  .modal-body select { width: 100%; margin-bottom: 10px; }
  .ghost { opacity: 0; pointer-events: none; }
  #calendar { width: 100%; min-height: 360px; }
  #calendario_visita .card-body { overflow: visible; }
</style>

<!-- Card calendario -->
<div class="card card-primary" id="calendario_visita" style="display:block;">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-calendar-alt mr-2"></i>Calendario de Visitas
    </h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="card-body p-0">
    <!-- Calendario -->
    <div id="calendar" class="p-3"></div>


<!-- Modal para agregar/editar visitas -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Agregar/Editar Visita</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="eventForm">
          <input type="hidden" id="eventId">

          <div class="form-group">
            <label for="comiteSelect">Comité</label>
            <select class="form-control" id="comiteSelect">
              <option value="">-- Seleccionar Comité --</option>
            </select>
          </div>

          <div class="form-group" id="organizacionGroup" style="display: none;">
            <label for="organizacionSelect">Organización</label>
            <select class="form-control" id="organizacionSelect">
              <option value="">-- Seleccionar Organización --</option>
            </select>
          </div>

          <div class="form-group">
            <label for="responsableNombre">Nombre del responsable</label>
            <input type="text" class="form-control" id="responsableNombre" placeholder="Ingrese el nombre del responsable" required autocomplete="off">
          </div>

          <div class="form-group">
            <label for="responsableApellido">Apellido del responsable</label>
            <input type="text" class="form-control" id="responsableApellido" placeholder="Ingrese el apellido del responsable" required autocomplete="off">
          </div>

          <div class="form-group">
            <label for="responsableCargo">Cargo del responsable</label>
            <input type="text" class="form-control" id="responsableCargo" placeholder="Ingrese cargo del responsable" required autocomplete="off">
          </div>

          <div class="form-group">
            <label for="eventTime">Hora</label>
            <input type="time" class="form-control" id="eventTime" required autocomplete="off">
          </div>

          <div class="form-group">
            <label for="promotorSelect">Promotor</label>
            <select class="form-control" id="promotorSelect">
              <option value="">-- Seleccionar Promotor --</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i> Cerrar
        </button>
        <button type="button" id="deleteEvent" class="btn btn-danger" style="display:none">
          <i class="fas fa-trash mr-1"></i> Eliminar
        </button>
        <button type="button" id="saveEvent" class="btn btn-primary">
          <i class="fas fa-save mr-1"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>
