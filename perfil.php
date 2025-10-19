<?php
session_start();

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Detener la ejecución del script
}

// 2. Recuperar los datos del usuario desde la sesión
$nombre_usuario = $_SESSION['user_name'] ?? 'Usuario';
$saldo_usuario = $_SESSION['user_saldo'] ?? 0;

// Formatear el saldo
$saldo_formateado = number_format($saldo_usuario, 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Mi Perfil</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="estilo.css">
</head>
<body class="perfil-page"> <?php 
    // Incluir el navbar. Como esta página es .php, se ejecutará bien
    include 'nav.html'; 
  ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="card shadow-lg card-profile">
          
          <div class="card-header">
            <ul class="nav nav-tabs profile-tabs card-header-tabs">
              <li class="nav-item">
                <a class="nav-link active" id="TabDatosPersonales" href="#">
                  <i class="fas fa-user-circle me-2"></i>Datos Personales
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="TabCambiarContraseña" href="#">
                  <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="TabCasino" href="#">
                  <i class="fas fa-dice me-2"></i>Volver al Casino
                </a>
              </li>
            </ul>
          </div>

          <div class="card-body">
            
            <div id="DivDatosPersonales" class="profile-tab-content">
              <h4>Mis Datos</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Nombre de Usuario:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Saldo Actual:</label>
                    <input type="text" class="form-control" value="$<?php echo $saldo_formateado; ?> CLP" readonly>
                  </div>
                </div>
              </div>
              </div>

            <div id="DivCambiarContraseña" class="profile-tab-content" style="display: none;">
              <h4>Cambiar Contraseña</h4>
              <form id="changePasswordForm">
                <div class="mb-3">
                  <label for="currentPassword" class="form-label">Contraseña Actual</label>
                  <input type="password" id="currentPassword" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="newPassword" class="form-label">Contraseña Nueva</label>
                  <input type="password" id="newPassword" class="form-control" aria-describedby="passwordHelpBlock" required>
                  <small id="passwordHelpBlock" class="form-text text-muted">
                    Tu contraseña debe tener al menos 8 caracteres.
                  </small>
                </div>
                <div class="mb-3">
                  <label for="confirmNewPassword" class="form-label">Confirmar Contraseña Nueva</label>
                  <input type="password" id="confirmNewPassword" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
              </form>
            </div>
            
            <div id="DivCasino" class="profile-tab-content text-center" style="display: none;">
              <h4>Volver al Casino</h4>
              <p>Haz clic en la imagen para volver a la página principal.</p>
              <a href="index.php">
                <img src="IMG/iconruleta.png" alt="Volver al casino" class="casino-return-img">
              </a>
            </div>

          </div> </div> </div> </div> </div> <?php include 'footer.html'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.profile-tabs .nav-link');
      const contents = document.querySelectorAll('.profile-tab-content');

      tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
          e.preventDefault();
          if (this.classList.contains('active')) return;

          // Quitar clase activa de todas las tabs
          tabs.forEach(t => t.classList.remove('active'));
          
          // Ocultar todos los divs de contenido
          contents.forEach(div => div.style.display = 'none');

          // Activar la tab actual
          this.classList.add('active');
          
          // Mostrar el contenido correspondiente
          const targetDiv = this.id.replace('Tab', 'Div');
          document.getElementById(targetDiv).style.display = 'block';
        });
      });
    });
  </script>
  
  <script src="login.js"></script>

</body>
</html>