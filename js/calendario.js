// js/calendario.js
// Inicialización y lógica del calendario, movido desde vistas/calendario.php

$(document).ready(function() {
    // --- 1. CARGA INICIAL DE DATOS ---
    function loadComites() {
        $.ajax({
            url: 'php/get_comites.php',
            type: 'GET',
            dataType: 'json',
            success: function(comites) {
                console.log('loadComites: recibidos', comites);
                const comiteSelect = $('#comiteSelect');
                comiteSelect.empty().append('<option value="">-- Seleccionar Comité --</option>');
                comites.forEach(comite => {
                    comiteSelect.append($('<option></option>').val(comite.id).text(comite.descripcion));
                });
            },
            error: function() {
                alert('Error al cargar los comités');
            }
        });
    }

    function loadOrganizaciones(comiteId, organizacionIdSeleccionada = null) {
        const orgSelect = $('#organizacionSelect');
        const orgGroup = $('#organizacionGroup');
        if (!comiteId) {
            orgGroup.hide();
            orgSelect.empty().append('<option value="">-- Seleccionar Organización --</option>');
            return;
        }
        console.log('loadOrganizaciones: comiteId=', comiteId);
        $.ajax({
            url: `php/get_organizaciones.php?comite_id=${comiteId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // El endpoint puede devolver un array (old) o un objeto {organizaciones: []}
                var organizaciones = [];
                if (Array.isArray(response)) organizaciones = response;
                else if (response && Array.isArray(response.organizaciones)) organizaciones = response.organizaciones;

                console.log('loadOrganizaciones respuesta:', response, 'usando:', organizaciones.length, 'organizaciones');

                orgSelect.empty().append('<option value="">-- Seleccionar Organización --</option>');
                organizaciones.forEach(org => {
                    orgSelect.append($('<option></option>').val(org.id).text(org.nombre));
                });
                if (organizacionIdSeleccionada) {
                    orgSelect.val(organizacionIdSeleccionada);
                }
                orgGroup.show();
            },
            error: function(xhr, status, error) {
                alert('Error al cargar las organizaciones');
                console.error('Error loadOrganizaciones:', status, error, xhr);
                orgGroup.hide();
            }
        });
    }

    loadComites();

    function loadPromotores() {
        $.ajax({
            url: 'php/get_promotores.php',
            type: 'GET',
            dataType: 'json',
            success: function(promotores) {
                console.log('loadPromotores: recibidos', promotores);
                const promotorSelect = $('#promotorSelect');
                promotorSelect.empty().append('<option value="">-- Seleccionar Promotor --</option>');
                promotores.forEach(promotor => {
                    promotorSelect.append($('<option></option>').val(promotor.id).text(promotor.nombre));
                });
            },
            error: function() {
                alert('Error al cargar los promotores');
            }
        });
    }

    loadPromotores();

    // --- 2. INICIALIZACIÓN DE FULLCALENDAR ---
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: 'php/get_visitas.php',
        dateClick: function(info) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (info.date < today) {
                alert('Por favor seleccione un día posterior al actual.');
                return;
            }
            $('#eventForm')[0].reset();
            $('#organizacionGroup').hide();
            $('#eventId').val('');
            $('#eventModalLabel').text('Agregar Visita');
            $('#deleteEvent').hide();
            $('#eventModal').modal('show');
            $('#saveEvent').data('date', info.dateStr); 
        },
        eventClick: function(info) {
            $('#eventForm')[0].reset();
            const props = info.event.extendedProps;
            const start = info.event.start;
            const hora = start.toTimeString().substring(0, 5);
            $('#eventId').val(info.event.id);
            $('#comiteSelect').val(props.id_comite);
            loadOrganizaciones(props.id_comite, props.id_organizacion);
            const partes = props.responsable.trim().split(" ");
            const nombre = partes[0];
            const apellido = partes.slice(1).join(" ");
            $('#responsableNombre').val(nombre);
            $('#responsableApellido').val(apellido);
            $('#responsableCargo').val(props.cargo);
            $('#eventTime').val(hora);
            $('#promotorSelect').val(props.id_promotor);
            const fecha = start.toISOString().substring(0, 10);
            $('#saveEvent').data('date', fecha);
            $('#eventModalLabel').text('Editar Visita');
            $('#deleteEvent').show();
            $('#eventModal').modal('show');
        }
    });
    calendar.render();
    // Exponer calendario para permitir actualizaciones desde otras funciones
    window.calendar = calendar;

    // Si el contenedor del calendario cambia (se muestra/oculta), actualizar tamaño
    var card = document.getElementById('calendario_visita');
    if (card) {
        var observer = new MutationObserver(function(mutations) {
            // Si el elemento está visible, actualizar el tamaño
            if (window.getComputedStyle(card).display !== 'none') {
                setTimeout(function() { if (window.calendar && window.calendar.updateSize) window.calendar.updateSize(); }, 50);
            }
        });
        observer.observe(card, { attributes: true, attributeFilter: ['style', 'class'] });
    }

    // --- 3. MANEJADORES DE EVENTOS DEL MODAL ---
    $('#comiteSelect').change(function() {
        const selectedValue = $(this).val();
        loadOrganizaciones(selectedValue);
    });
    $('#saveEvent').click(function() {
        const eventId = $('#eventId').val();
        const comiteId = $('#comiteSelect').val();
        const organizacionId = $('#organizacionSelect').val();
        const nombre = $('#responsableNombre').val() ? $('#responsableNombre').val().trim() : '';
        const apellido = $('#responsableApellido').val() ? $('#responsableApellido').val().trim() : '';
        const responsable = (nombre + ' ' + apellido).trim();
        const cargo = $('#responsableCargo').val() ? $('#responsableCargo').val().trim() : '';
        const time = $('#eventTime').val();
        const promotorId = $('#promotorSelect').val();
        const date = $(this).data('date');

        // Debug: mostrar valores en consola para depuración
        console.log('saveEvent clicked:', { eventId, comiteId, organizacionId, nombre, apellido, responsable, cargo, time, promotorId, date });

        // Validación más explícita: construir lista de campos faltantes
        const missing = [];
        if (!comiteId) missing.push('Comité');
        if (!organizacionId) missing.push('Organización');
        if (!nombre) missing.push('Nombre del responsable');
        if (!apellido) missing.push('Apellido del responsable');
        if (!cargo) missing.push('Cargo');
        if (!time) missing.push('Hora');
        if (!promotorId) missing.push('Promotor');

        if (missing.length > 0) {
            // Mostrar que campos están faltando para que el usuario sepa exacto
            alert('Por favor llenar los siguientes campos: ' + missing.join(', '));
            return;
        }
        const eventData = {
            accion: 'guardar',
            id: eventId,
            fecha: date,
            hora: time,
            organizacion_id: organizacionId,
            responsable: responsable,
            cargo: cargo,
            promotor_id: promotorId || null
        };
        fetch('php/event_manager.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(eventData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                $('#eventModal').modal('hide');
            } else {
                alert('Error al guardar la visita: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error en AJAX:', error);
            alert('Error de conexión al guardar.');
        });
    });
    $('#deleteEvent').click(function() {
        const eventId = $('#eventId').val();
        if (!eventId) return;
        if (confirm('¿Está seguro de que desea eliminar esta visita?')) {
            const eventData = {
                accion: 'eliminar',
                id: eventId
            };
            fetch('php/event_manager.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(eventData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calendar.refetchEvents();
                    $('#eventModal').modal('hide');
                } else {
                    alert('Error al eliminar: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error en AJAX:', error);
                alert('Error de conexión al eliminar.');
            });
        }
    });
});
