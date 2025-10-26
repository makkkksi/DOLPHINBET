<?php session_start(); ?> <!DOCTYPE html>
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
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  
  <link rel="stylesheet" href="estilo.css">
  
<style>
    /* === NUEVO: Fondo blanco y título azul === */
    .Juegos {
      background-color: #ffffff; /* Fondo blanco */
      padding-top: 2.5rem;
      padding-bottom: 2.5rem;
    }
    .Juegos h1 {
      color: #073763ff; /* Color azul de tu tema */
      font-weight: 900;
      text-transform: uppercase;
      margin-bottom: 1.5rem; /* Más espacio */
    }
    /* === FIN DE CAMBIOS === */

    .game-swiper-container {
      position: relative;
      padding-left: 40px;  
      padding-right: 40px; 
    }
    
    .game-slide a {
      display: block;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .game-slide img {
      width: 100%;
      height: 100%;
      object-fit: cover; 
      /* === NUEVO: Proporción horizontal 16:9 === */
      aspect-ratio: 16 / 9; 
    }

    .game-slide a:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }

    /* Estilo de las flechas */
    .game-swiper .swiper-button-next,
    .game-swiper .swiper-button-prev {
      color: #073763ff; /* Flechas azules */
      background-color: rgba(255, 255, 255, 0.6); /* Fondo blanco semi-transparente */
      width: 50px;
      height: 50px;
      border-radius: 50%;
      transition: background-color 0.2s;
    }
    .game-swiper .swiper-button-next:hover,
    .game-swiper .swiper-button-prev:hover {
      background-color: rgba(255, 255, 255, 0.9);
    }
    .game-swiper .swiper-button-next::after,
    .game-swiper .swiper-button-prev::after {
      font-size: 1.2rem !important; 
      font-weight: bold;
    }
    
    .game-swiper .swiper-button-prev { left: -10px; }
    .game-swiper .swiper-button-next { right: -10px; }
    
    @media (max-width: 768px) {
      .game-swiper .swiper-button-next,
      .game-swiper .swiper-button-prev {
        display: none;
      }
      .game-swiper-container {
        padding-left: 15px;
        padding-right: 15px;
      }
    }
  </style>

</head>
<body>

  <?php include 'nav.html'; ?>
  
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active" aria-current="true"></button>
      <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2"></button>
      <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="3"></button>
    </div>
  <div class="carousel-inner">
    <div class="carousel-item active" data-bs-interval="7000">
      <img src="IMG/Wallpaper_ruleta.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h1>BIENVENIDO A DOLPHINBET</h1>
        <p>Apuesta como nunca.  Pierde como siempre</p>
        <button 
          class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2"
          data-bs-toggle="modal" 
          data-bs-target="#loginModal">
          JUGAR
        </button>
      </div>
    </div>
    <div class="carousel-item" data-bs-interval="7000">
      <img src="IMG/plata3.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h1>¡2,3% BONUS EN EL PRIMER DEPOSITO!</h1> 
        <p>+ 1 GIRO GRATIS</p>
        <button onclick="window.location.href='transaccion.php'" class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2">
          Depositar
        </button>
      </div>
    </div>
    <div class="carousel-item" data-bs-interval="7000">
      <img src="IMG/juegos.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h1>¡CONOCE NUESTROS JUEGOS!</h1>
        <p>Visita nuestros juegos mas jugados</p>
         <button onclick="window.location.href='index.php#Juegos'" class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2">
          Juegos
        </button>
      </div>
    </div>
    <div class="carousel-item" data-bs-interval="7000">
      <img src="IMG/cards.webp" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h1>PIERDE COMO NUNCA</h1>
        <p>Infórmate de nuestras bases del juego, ¡aunque igualmente perderás!</p>
        <button onclick="window.location.href='info.php'" class="btn border border-white text-white bg-transparent fw-bold rounded px-4 py-2">
          INFORMACIÓN
        </button>
      </div>
    </div>
  </div>
</div>
<div class="Juegos" id="Juegos"> 
  <h1 class="text-center">Nuestros Juegos</h1>
  
  <div class="container-fluid my-5 game-swiper-container"> <div class="swiper game-swiper">
      <div class="swiper-wrapper">
        
        <div class="swiper-slide game-slide">
          <a href="pepito.php">
            <img src="IMG/pepito.webp" alt="Pepito Paga Doble">
          </a>
        </div>
        
        <div class="swiper-slide game-slide">
          <a href="blackjack.php">
            <img src="IMG/BLACKJACK.webp" alt="Blackjack">
          </a>
        </div>
        
        <div class="swiper-slide game-slide">
          <a href="pollo.php">
            <img src="IMG/pollo.png" alt="Juego del Pollo">
          </a>
        </div>

        <div class="swiper-slide game-slide">
          <a href="ruleta.php"> 
            <img src="IMG/portada_ruleta_ejemplo.png" alt="Ruleta"> 
          </a>
        </div>

        <div class="swiper-slide game-slide">
          <a href="#">
            <img src="IMG/portada_slots_ejemplo.png" alt="Slots">
          </a>
        </div>
        
        <div class="swiper-slide game-slide">
          <a href="#">
            <img src="IMG/portada_crash_ejemplo.png" alt="Crash Game">
          </a>
        </div>

      </div>
      
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </div>
</div>
<?php include 'footer.html'; ?>


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
            <label for="registerDOB" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="registerDOB" required />
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

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="login.js"></script>

<script>
  // Script para abrir modal (el que ya tenías)
  document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error') === 'not_logged_in') {
      var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
      loginModal.show();
    }
  });

  // Script para inicializar el Slider de Juegos
  var swiper = new Swiper(".game-swiper", {
    slidesPerView: 2, // Cuántos se ven en móvil
    spaceBetween: 15, // Espacio entre ellos en móvil
    
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    
    // Puntos de quiebre (responsive)
    breakpoints: {
      // 576px (móvil grande)
      576: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      // 768px (tablet)
      768: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      // 1200px (desktop)
      1200: {
        slidesPerView: 5,
        spaceBetween: 25,
      },
    },
  });
</script>
</body>
</html>