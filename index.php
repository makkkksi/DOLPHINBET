<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Casino Online</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg"> 
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<!--README
LEER

La mayoria de cosas son codigo del boostrap.
-->
<body>

  <?php include 'nav.html'; ?>
  
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
  

<!--carrusel copaio del bustrap-->
  <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active" aria-current="true"></button>
    <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2"></button>
    </div>
  <div class="carousel-inner">

    <div class="carousel-item active" data-bs-interval="7000">
      <img src="IMG/Wallpaper_ruleta.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h2>BIENVENIDO A DOLPHINBET</h2>
        <p>Apuesta como nunca.  Pierde como siempre</p>
<button 
  class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2"
  data-bs-toggle="modal" 
  data-bs-target="#registerModal">
  JUGAR
</button>
      </div>

    </div>
    <div class="carousel-item" data-bs-interval="7000">
      <img src="IMG/juegos.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h2>¡CONOCE NUESTROS JUEGOS!</h2>
        <p>Visita nuestros juegos mas jugados</p>
         <button onclick="window.location.href='juegos.html'" class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2">
          Juegos
        </button>
      </div>
    </div>
    <div class="carousel-item" data-bs-interval="7000">
      <img src="IMG/plata3.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h2>PIERDE COMO NUNCA</h2>
        <p>Infórmate de nuestras bases del juego, ¡aunque igualmente perderás!</p>
        <button onclick="window.location.href='info.html'" class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2">
          INFORMACIÓN
        </button>
      </div>
      
    </div>
  </div>
</div>
<!--carrusel //-->

<div class="Juegos" id="Juegos">
<h1 class="text-center">NUESTROS JUEGOS</h1>
    <div class="container my-5">
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <a href="id.html">
        <img src="IMG/poker_index.png" alt="" class="img-juegos"></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="id.html">
        <img src="IMG/cohete.png" alt="" class="img-juegos"></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="id.html">
        <img src="IMG/pollo.png" alt="" class="img-juegos"></a>
      </div>
    </div>
  </div>



</div>










<?php include 'footer.html'; ?>











<!---MODAL ACA PQ EN EL NAV QL NO CARGA POR EL FETCH NOSE Q WEAA-->
<!--Modal del Login-->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div id="loginError" class="text-danger mb-3" style="display: none;"></div>
        <form id="loginForm">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="loginEmail" required />
          </div>

          <div class="mb-3">
            <label for="loginPassword" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="loginPassword" required />
          </div>

          <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
      </div>

    </div>
  </div>
</div>

  <!--Modal del Registro copiado del boostrap y visto en CLASES!!!!! -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="registerModalLabel">Registrarse</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div id="registerError" class="text-danger mb-3" style="display: none;"></div>
        <form id="registerForm">
          <div class="mb-3">
            <label for="registerName" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="registerName" required />
          </div>
          <div class="mb-3">
            <label for="registerEmail" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="registerEmail" required />
          </div>
          <div class="mb-3">
            <label for="registerPassword" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="registerPassword" required />
          </div>
          <div class="mb-3">
            <label for="registerConfirmPassword" class="form-label">Confirmar contraseña</label>
            <input type="password" class="form-control" id="registerConfirmPassword" required />
          </div>

          <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form>
      </div>

    </div>
  </div>
</div>

  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


<script src="login.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('error') === 'not_logged_in') {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }
    });
  </script>

</body>
</html>