<?php
require_once './php/validate_session.php';
if($_SESSION['rol'] !== 'administrador' AND $_SESSION['division'] !== 'tecnologia'){
  header("Location: login.php");
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Division de Tecnologia</title>

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
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="img/logo.png" alt="AdminLTELogo" height="180" width="180">
  </div>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="tecnologia.php" class="nav-link">Inicio</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->


      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <h6 class="brand-link">
      <img src="dist/img/logo0.jpg" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CCS Recicla</span>
    </h6>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">

        <div class="info">
          <h5 class="d-block text-light"><?= $_SESSION['nombre']?> <?= $_SESSION['apellido']?></h5><h5 class="d-block text-warning">CI: <?= $_SESSION['cedula']?></h5> 
          
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
                <a class="nav-link" onclick="mostrar_crud0()">
                  <i class="fas fa-user-alt nav-icon"></i>
                  <p style="user-select: none">Agregar personal</p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" onclick="mostrar_crud1()">
                  <i class="fas fa-user-edit nav-icon"></i>
                  <p style="user-select: none">Ver/listar Personal</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="./php/logout.php" class="nav-link">
              <i class="fas fa-sign-out-alt nav-icon"></i>
              <p>Cerrar Sesión</p>
            </a>
          </li>
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
            <h1 class="m-0">Division de Tecnologia</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <?php include "./vistas/update.php"; ?>
        <?php include "./vistas/register.php";?>
        <?php include "./vistas/read.php"?>


      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
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
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>

      <!--ACCION DE BOTONES-->
      <script>
        //CRUD
        function mostrar_crud0(){
          var crud0 = document.getElementById("form_registro");
          
          var crud1 = document.getElementById("lista_usuarios");
          /*var entradas = document.getElementById("entradas");*/
          if (crud0.style.display === "none" || crud1.style.display === ""/*|| entradas.style.display === ""*/){
            crud0.style.display = "block";
            crud1.style.display = "none";
            /*entradas.style.display = "none";*/
          }else{
            crud0.style.display = "none";
          }
        }

        
        function mostrar_crud1(){
          var crud1 = document.getElementById("lista_usuarios");
          var crud0 = document.getElementById("form_registro");
          /*var entradas = document.getElementById("entradas");*/
          if (crud1.style.display === "none" || crud0.style.display === ""/* || entradas.style.display === ""*/){
            crud1.style.display = "block"; 
            crud0.style.display = "none";
            /*entradas.style.display = "none";*/
          }else{
            crud1.style.display = "none";
          }
        } 

        function mostrar_actualizar(){


          var crud1 = document.getElementById("lista_usuarios");
          var actualizar = document.getElementById("form_update");

          if (crud1.style.display === "block" || actualizar.style.display === "none") {
            crud1.style.display = "none";
            actualizar.style.display = "block";
          }else{
            actualizar.style.display = "none";
          }

        }

        function cancelar_actualizar(){
          var crud1 = document.getElementById("lista_usuarios");
          var actualizar = document.getElementById("form_update");
          
          if (actualizar.style.display === "block" || crud1.style.display ==="") {
            crud1.style.display = "block";
            actualizar.style.display = "none";
          }else{
            actualizar.style.display = "block";
          }
        } 

      </script>



</body>
</html>
