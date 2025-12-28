<?php

require("./php/main.php");

$mensaje_error = '';
$redirect_url = '';
$user = '';
$id = '';
$mostrar_formulario = false;

if(isset($_POST['btn_iniciar_personal'])){
    $cedula = limpiar_cadena($_POST['cedula']);
    $clave = limpiar_cadena($_POST['clave']);

    $errores = [];
    if(empty($cedula)){
        $errores['id_cedula'] = "Ingrese su cedula de identidad";
    }

    else if (!ctype_digit($cedula)) {
        $errores['id_cedula'] = "La cédula debe contener solo números";
    }

    else if (strlen($cedula) > 8 || strlen($cedula) < 7){
        $errores['id_cedula'] = "La cedula debe contener 7 o 8 digitos";
    }

    if (empty($clave)) {
        $errores['id_clave'] = "Ingrese su clave de acceso";
    }

    if (!empty($errores)) {
        echo json_encode([
            "status" => "invalido",
            "errores" => $errores
        ]);
        exit;
    }

    $stmt = $con->prepare("SELECT
        p.id, p.cedula, p.nombre, p.apellido, p.clave, p.status,
        r.descripcion AS rol,
        g.descripcion AS gerencia,
        d.descripcion AS division
        FROM personal p
        JOIN rol r ON p.id_rol = r.id
        LEFT JOIN gerencia g ON p.id_gerencia = g.id
        LEFT JOIN division d ON p.id_division = d.id
        WHERE p.cedula = ? AND (p.status = 1 OR p.status = 2)");

    if ($stmt === false) {
        echo json_encode(['error' => 'Error en prepare: '.$con->error]);
        exit();
    }

    // Usa "s" si cedula es VARCHAR
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $personal = $result->fetch_assoc();

        if (password_verify($clave, $personal['clave'])) {
            session_start();
            $_SESSION = [
                'id' => $personal['id'],
                'cedula' => $personal['cedula'],
                'nombre' => $personal['nombre'],
                'apellido' => $personal['apellido'],
                'rol' => $personal['rol'],
                'gerencia' => $personal['gerencia'],
                'division' => $personal['division'],
                'status' => $personal['status'],
                'loggedin' => true
            ];

            $user = $_SESSION['cedula'];
            $id = $_SESSION['id'];

            if ($_SESSION['status'] == 2) {
                $redirect_url = '';
                $mostrar_formulario = true;
            } else {
                // Redirección según rol
                switch ($personal['rol']) {
                    case 'administrador':
                        $redirect_url = 'tecnologia.php';
                        break;
                    case 'gerente':
                        switch ($personal['division']) {
                            case 'economia_circulante': $redirect_url = 'eco_circulante.php'; break;
                            default: $redirect_url = 'login.php'; break;
                        }
                        break;
                    case 'promotor':
                        switch ($personal['division']) {
                            case 'economia_circulante': $redirect_url = 'eco_circulante.php'; break;
                            default: $redirect_url = 'login.php'; break;
                        }
                        break;
                    default:
                        $redirect_url = 'login.php';
                        break;
                }
            }
        } else {
            $mensaje_error = 'Contraseña incorrecta, intente de nuevo.';
        }
    } else {
        $mensaje_error = 'El usuario no está registrado, por favor revise si está bien escrito.';
    }
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $mensaje_error,
        'redirect' => $redirect_url,
        'mostrar_formulario' => $mostrar_formulario,
        'user' => $user,
        'id' => $id
    ]);
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="./dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="./dist/css/estilos.css">
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
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">





    <style>
        .password-container {
            position: relative;
        }
    .toggle-password{
        position: absolute;
        right: 10px;
        top: 40%;
        transform: translateY(-40%);
        cursor: pointer;
        background: none;
        border: none;
    }   

    </style>
</head>
<body>

<!--Barra de Navegacion (PARA EL LOGIN)-->
<nav class="navbar_log">
    
    <!-- Logo -->
      <img src="./dist/img/ccs_recicla_logo.png" width="200px" height="100px">

    <!-- Botones con ajuste hacia la izquierda -->
  
</nav>
<!--Barra de Navegacion (PARA EL LOGIN)-->

<div class="contenedor_form_login">
<form id="formLogin" class="form_login" action="login.php" method="post">

    <div class="mb-3">
       <a class="d-flex justify-content-center"><img src="./dist/img/logo.png" width="300px" height="150px"></a>
    </div>
    
  <div class="mb-3"><label><h1 style="text-align: center;">Iniciar sesión</h1></label></div>

  <div class="input-group mb-3">
    <span class="input-group-text"><i class="far fa-address-card text-light"></i></span>
    <input type="int" name="cedula" class="form-control" id="id_cedula"  placeholder="Escriba su cedula" autocomplete="off">
  </div>

  <div class="input-group mb-3">
    <span class="input-group-text"><i class="fas fa-key text-light"></i></span>
    <input type="password" name="clave" class="form-control" id="id_clave" placeholder="Introduzca su clave" autocomplete="off"><span style="background-color: #FF8A00"><button type="button" class="toggle-password" onclick="togglePassword()"><i class="far fa-eye-slash text-light"></i></button></span>
  </div>

    <div class="input-group">
        <div class="d-flex justify-content-start">
            <button class="btn btn-primary" type="button" name="btn_olvido" id="olvidada">¿Olvidaste tu contraseña?</button>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" name="btn_iniciar_personal" class="btn btn-success">Ingresar</button>
        </div>
    </div>

    </form>

    <div id="alertContainer" 
    style="
    position: absolute;  
    width: 300px;
    left: -100%;">
    </div>

    </div>


<!--MODAL SI OLVIDO LA CONTRASEÑA-->
<div class="modal fade" id="ocContainer" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Recuperacion de contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="olvideClave">
        <form id="olClave">
            <label for="olUser">Usuario</label>
          <div class="input-group mb-3">
            <input type="text" id="olUser" placeholder="Ingrese su numero de cedula de identidad" name="userOl" class="form-control">
          </div>

          <div class="input-group mb-3s" id="olMessage">
              
          </div>

        <button type="submit" class="btn btn-success">Enviar</button>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!--MODAL DE NUEVA CONTRASEÑA-->
<div class="modal fade" id="ncContainer" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Atencion registre su contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="nuevaClave">
        <form id="regClave">

          <div class="input-group mb-3">
            <input type="int" id="user" name="user" class="form-control" readonly>
          </div>

          <div class="input-group mb-3">
            <input class="form-control" type="password" name="updatePassword" id="passwordNew">
          </div>
        <button type="submit" class="btn btn-success">Guardar</button>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>



<!-- Modal para errores -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Error de Inicio de Sesión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="errorModalBody">
        <!-- El mensaje de error se insertará aquí -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script>
function mostrarAlerta(mensaje, tipo = 'danger', tiempo = 5000){
    const alertContainer = document.getElementById('alertContainer');

    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.innerHTML = `<strong>Error:</strong> ${mensaje}`;

    alertContainer.appendChild(alerta);

    setTimeout(() => {
        if (alerta.parentElement) {
            alerta.remove();
        }
    }, tiempo);
}

function alertaPositiva(mensaje, tipo = 'success', tiempo = 5000){
    const alertContainer = document.getElementById('alertContainer');

    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.innerHTML = `<strong>Listo:</strong> ${mensaje}`;

    alertContainer.appendChild(alerta);

    setTimeout(() => {
        if (alerta.parentElement) {
            alerta.remove();
        }
    }, tiempo);
}

function mostrarModalError(mensaje) {
    const modalBody = document.getElementById('errorModalBody');
    modalBody.textContent = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

function mostrarForm2(alerta) {
    const ncBody = document.getElementById('nuevaClave');
    const nc = new bootstrap.Modal(document.getElementById('ncContainer'));
    nc.show();
}

function mostrarForm3(alerta) {
    const ocBody = document.getElementById('olvideClave');
    const oc = new bootstrap.Modal(document.getElementById('ocContainer'));
    oc.show();
}

//Cuando se presiona el botón de enviar
document.getElementById('formLogin').addEventListener('submit', function(e) {
    e.preventDefault(); 
    const cedula = document.getElementById('id_cedula').value;
    const clave = document.getElementById('id_clave').value;
        const formData = new FormData();
        formData.append('cedula', cedula);
        formData.append('clave', clave);
        formData.append('btn_iniciar_personal', '1');

        fetch('login.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta completa:", data);
            console.log("Valor de user:", data.user, "ID:", data.id);

            if (data.status === "invalido") {
              for (let campo in data.errores) {
                const elemento = document.getElementById(campo);
                if (elemento) {
                  const originalValue = elemento.value;
                  const originalType = elemento.type;
                  elemento.className = 'form-control border border-danger text-danger';
                  elemento.value = data.errores[campo]; 
                  elemento.style.pointerEvents = 'none';
                  elemento.type = "text";

                  setTimeout(function(){
                    elemento.className = 'form-control';
                    elemento.style.pointerEvents = '';
                    elemento.value = originalValue;
                    elemento.type = originalType;
                  }, 3000);
                }
              }
            } else if (data.error) {
                mostrarModalError(data.error);
            } else if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.mostrar_formulario){
                mostrarForm2();
                document.getElementById('user').value = data.user;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarModalError('Error interno del servidor.');
        });
    
});

const olvidada = document.getElementById('olvidada');
olvidada.addEventListener('click',function(){
    mostrarForm3();
    const olClave = document.getElementById('olClave');
    olClave.addEventListener('submit', function(evento){
        evento.preventDefault();
        const confirmar = confirm('¿Esta seguro de renovar su contraseña?');

        if (!confirmar) {
        return;    
        }
        const datosOl = new FormData(olClave);
        fetch('clave.php',{
            method: 'POST',
            body: datosOl
        })
        .then(respuesta => respuesta.json())
        .then(oL => {
        console.log('Status de la consulta:', oL.status);

            if (oL.status === "invalido") {
              for (let campo in oL.errores) {
                const elemento = document.getElementById(campo);
                if (elemento) {
                  const originalValue = elemento.value;
                  elemento.className = 'form-control border border-danger text-danger';
                  elemento.value = oL.errores[campo]; 
                  elemento.style.pointerEvents = 'none';

                  setTimeout(function(){
                    elemento.className = 'form-control';
                    elemento.style.pointerEvents = '';
                    elemento.value = originalValue;
                  }, 3000);
                }
              }
            }

            else if (oL.status === "success") {
            document.getElementById('olMessage').innerHTML = `<p>${oL.message}</p>`;
                olClave.reset();

            }
            else if(oL.status === "alert"){
                mostrarAlerta(oL.alerta);
            }
        })
    });
});



const regClave = document.getElementById('regClave');
regClave.addEventListener('submit', function(nc){
    nc.preventDefault();
    const nuevaClave = document.getElementById('passwordNew').value;
    const id = document.getElementById('user').value; // ya lo guardaste en el input
    const formPassword = new FormData(regClave);
    formPassword.append('id', id);
    formPassword.append('btn_actualizar_clave', '1');

fetch('claveUp.php', {
  method: 'POST',
  body: formPassword
})
.then(r => r.json()) // <-- leer como JSON
.then(data => {
  console.log("Respuesta claveUp:", data);
  if (data.success) {
    alertaPositiva(data.message);
    const modal = bootstrap.Modal.getInstance(document.getElementById('ncContainer'));
    modal.hide(); 
    // cerrar modal
  } else {
    mostrarModalError(data.message);
  }
})
.catch(err => console.error("Error claveUp:", err));

});


function togglePassword() {
    const passwordInput = document.getElementById('id_clave');
    const toggleBtn = document.querySelector('.toggle-password');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.innerHTML = '<i class="far fa-eye text-light"></i>';
    } else {
        passwordInput.type = 'password';
        toggleBtn.innerHTML = '<i class="far fa-eye-slash text-light"></i>';
    }
}
</script>


<script>


    function togglePassword() {
        const passwordInput = document.getElementById('id_clave');
        const toggleBtn = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.innerHTML = '<i class="far fa-eye text-light"></i>';
        }else{
            passwordInput.type = 'password';
            toggleBtn.innerHTML = '<i class="far fa-eye-slash text-light"></i>';
        }
    }
</script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
