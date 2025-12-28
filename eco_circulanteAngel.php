<?php  
require_once './php/validate_session.php';
$_SESSION['rol'];
if ($_SESSION['rol'] !== 'gerente' && $_SESSION['rol'] !== 'administrador' || $_SESSION['division'] !== 'economia_circulante'){
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Division de Economia Circulante</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- FullCalendar CSS -->
  <link rel="stylesheet" href="plugins/fullcalendar/main.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="eco_circulante.php" class="nav-link">Inicio</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <h6 href="index3.html" class="brand-link">
      <img src="dist/img/logo0.jpg"  class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CCS Recicla</span>
    </h6>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <h5 class="d-block text-success"><?= $_SESSION['nombre']?> <?= $_SESSION['apellido']?></h5><h5 class="d-block text-primary">CI: <?= $_SESSION['cedula']?></h5> 
          
        </div>
      </div>

      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" onclick="mostrar_param()">
                  <i class="far fa-solid fa-thumbs-up nav-icon"></i>
                  <p style="user-select: none;">Parametros</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" onclick="mostrar_org()">
                  <i class="far fa-building nav-icon"></i>
                  <p style="user-select: none;">Organizaciones</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" onclick="mostrar_calendario()">
                  <i class="fa fa-solid fa-calendar nav-icon"></i>
                  <p style="user-select: none;">Visitas</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" onclick="mostrar_canje()">
                  <i class="fas fa-solid fa-store nav-icon"></i>
                  <p style="user-select: none;">Canje</p>
                </a>
              </li>
              <li class="nav-item">
            <a href="./php/logout.php" class="nav-link">
              <i class="fas fa-sign-out-alt nav-icon"></i>
              <p>Cerrar Sesión</p>
            </a>
              </li>
            </ul>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Division de Economia Circulante</h1>
          </div><!-- /.col -->
          <!--QUIZA UN TEXTO DE AYUDA QUE DIGA DONDE ESTAMOS?
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Inicio</a></li> 
                <li class="breadcrumb-item active">Dashboard v1</li>
              </ol>
            </div--><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


                            <!--CONTENIDO-->


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <?php include "./vistas/parametros.php" ?>
        <?php include "./vistas/param_nuevos.php" ?>
        <?php include "./vistas/paramCanje.php" ?>


        <?php include "./vistas/panel_org.php" ?>
        <?php include "./vistas/form_org.php" ?>

        <?php include "./vistas/panel_recibo.php" ?>
        <?php include "./vistas/form_pre.php" ?>
        <?php include "./vistas/form_refinal.php" ?>



        <?php include "./vistas/canje.php" ?>

        <!-- Panel calendario solo visible con JS -->
        <div id="panel_calendario" style="display:none;">
          <?php include "./vistas/calendario.php" ?>
        </div>


        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
                          <!--FINAL CONTENIDO-->










  <!-- /.content-wrapper -->
  <!--footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer-->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery debe ir primero -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>

<!-- FullCalendar debe ir después de jQuery -->
<script src="plugins/fullcalendar/main.min.js"></script>
<script src="plugins/fullcalendar/locales-all.min.js"></script>


<script src="js/canje_logic.js"></script>
<!-- Inicialización del calendario -->
<script src="js/calendario.js"></script>

<script>
 function inicializarCanje() {
  if (typeof init === 'function') {
    init();
  } else {
    console.error('La funcion init no esta disponible');
  }
 }
  </script>



  <script>

      var formPre = document.getElementById("form_prerecibo");
      var panel_re = document.getElementById("panel_re");
      var param = document.getElementById("param");
      var paramcanje = document.getElementById("paramcanje")
      var panel_org = document.getElementById("panel_org");
      var form_org = document.getElementById("form_org");
      var form_param = document.getElementById("form_param");
      var formulario_p = document.getElementById("formParam");
      var formulario_pre = document.getElementById("formPre");
      var formulario_ref = document.getElementById('formRefinal');
      var calendario = document.getElementById("panel_calendario");
      var formRefinal = document.getElementById("form_refinal");
      var orgForm = document.getElementById("orgForm");
      var canje = document.getElementById("canje");

      // FUNCIÓN PARA OCULTAR TODOS LOS ELEMENTOS
      function ocultar_todo() {
          const elementos = [
              param, paramcanje, panel_org, panel_re, form_org, 
              form_param, formPre, formRefinal, calendario, canje
          ];
          
          elementos.forEach(elemento => {
              if (elemento) elemento.style.display = "none";
          });
      }

      // FUNCIONES PRINCIPALES
      function mostrar_param() {
        if (param.style.display === "block") {
          param.style.display = "none";
          if (paramcanje) paramcanje.style.display = "none";
        } else {
          ocultar_todo();
          param.style.display = "block";
          if (paramcanje) paramcanje.style.display = "block";
        }
      }


      function mostrar_org(){
        if(panel_org.style.display === "block"){
          panel_org.style.display = "none";
        }else{
          ocultar_todo();
          if(panel_org) panel_org.style.display = "block";
        }
      }

        function mostrar_calendario(){
          if (calendario.style.display === "block") {
            calendario.style.display = "none";
          }else{
            ocultar_todo();
            if(calendario) calendario.style.display = "block";
            // Si el calendario está inicializado globalmente, forzar ajuste
            if (window.calendar && typeof window.calendar.updateSize === 'function') {
            setTimeout(() => window.calendar.updateSize(), 100);
            }
          }
        }

      function mostrar_canje() {
        if (canje.style.display === "block") {
          canje.style.display = "none";
        }else{
          console.log('Mostrando módulo de canje');
          ocultar_todo();
          if(canje) {
              canje.style.display = "block";
              
              setTimeout(() => {
                  console.log('Verificando función init...');
                  if (typeof init === 'function') {
                      console.log('Inicializando módulo de canje...');
                      init();
                  } else {
                      console.error('ERROR: Función init no disponible');
                      const script = document.createElement('script');
                      script.src = 'js/canje_logic.js?v=' + new Date().getTime();
                      script.onload = function() {
                          console.log('Script recargado, init disponible:', typeof init);
                          if (typeof init === 'function') {
                              init();
                          }
                      };
                      document.head.appendChild(script);
                  }
              }, 300);
          }
        }
      }

      // FUNCIONES DE SUB-PANELES Y FORMULARIOS
      function mostrar_panelRe(){
          ocultar_todo();
          if(panel_re) panel_re.style.display = "block";
      }

      function salir_panelRe(){
          ocultar_todo();
          if(panel_org) panel_org.style.display = "block";
      }

      function mostrar_formParam(){
          ocultar_todo();
          if(form_param) form_param.style.display = "block";
      }

      function cancelar_formParam(){
          ocultar_todo();
          if(param) param.style.display = "block";
          if(paramcanje) paramcanje.style.display = "block";
          if(formulario_p) formulario_p.reset(); 
      }

      function mostrar_formOrg(){
          ocultar_todo();
          if(form_org) form_org.style.display = "block";
      }

      function cancelar_formOrg(){
          ocultar_todo();
          if(panel_org) panel_org.style.display = "block";
          if(orgForm) orgForm.reset(); 
      }

      function mostrar_formPre(){
          ocultar_todo();
          if(formPre) formPre.style.display = "block";
      }

      function cancelar_formPre(){
          ocultar_todo();
          if(panel_re) panel_re.style.display = "block";
      }

      function mostrar_formRefinal(){
          ocultar_todo();
          if(formRefinal) formRefinal.style.display = "block";
      }

      function cancelar_formRefinal(){
          ocultar_todo();
          if(panel_re) panel_re.style.display = "block";
      }

  </script>




  <!-- La inicialización del calendario ahora está en js/calendario.js -->




<script>
document.addEventListener('DOMContentLoaded', function(){
    fetch('./php/visitas.php') //Ruta de la consulta
      .then(respuesta => respuesta.json())
      .then(registro => {
        console.log('Visitas recibidas', registro.visitas);
        const tablaVisita = document.getElementById('lista_visitas');

        // 🔹 limpiar el tbody antes de volver a llenarlo
        tablaVisita.innerHTML = "";

        registro.visitas.forEach(v => {
          const filaVisita = document.createElement('tr');
          filaVisita.innerHTML = `
            <td>${v.nombre}</td>
            <td>${v.responsable}</td>
            <td>${v.cargo}</td>
            <td>${v.fecha}</td>
          `;
          tablaVisita.appendChild(filaVisita);
        });
      })
      .catch(error => console.error('Error al cargar visitas:', error))        

});


document.addEventListener('DOMContentLoaded', function(){
  const refresh = document.getElementById('refresh');
  refresh.addEventListener('click', function(){
    fetch('./php/visitas.php') //Ruta de la consulta
      .then(respuesta => respuesta.json())
      .then(registro => {
        console.log('Visitas recibidas', registro.visitas);
        const tablaVisita = document.getElementById('lista_visitas');

        // 🔹 limpiar el tbody antes de volver a llenarlo
        tablaVisita.innerHTML = "";

        registro.visitas.forEach(v => {
          const filaVisita = document.createElement('tr');
          filaVisita.innerHTML = `
            <td>${v.nombre}</td>
            <td>${v.responsable}</td>
            <td>${v.cargo}</td>
            <td>${v.fecha}</td>
          `;
          tablaVisita.appendChild(filaVisita);
        });
      })
      .catch(error => console.error('Error al cargar visitas:', error))        
  });
});


</script>

<script>

</script>

</body>
</html>