<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=not_logged_in');
    exit;
}

$saldo_actual = $_SESSION['user_saldo'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Pepito Paga Doble</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
  
  <style>
    .game-container {
      /* Fondo de imagen */
      background-image: url('IMG/plaza_armas.webp'); 
      background-size: cover;
      background-position: center;

      border: 10px solid #8B4513;
      border-radius: 10px;
      padding: 2rem;
      min-height: 350px;
      position: relative;

      display: flex;
      justify-content: center;
      align-items: center;
    }
    .token-board {
      display: flex;
      justify-content: center;
      gap: 20px;
      height: 120px;
      position: relative;
      width: 100%; 
    }
    .token {
      width: 100px;
      height: 100px;
      background-color: #111;
      border: 5px solid #333;
      border-radius: 50%;
      cursor: pointer;
      position: absolute; 
      transition: transform 0.4s ease-in-out;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 40px;
      color: #FFD700;
      user-select: none; 
    }
    /* Ios. iniciales */
    #token-0 { transform: translateX(-120px); }
    #token-1 { transform: translateX(0); }
    #token-2 { transform: translateX(120px); }

    .token.show-money::before { content: '💲'; }
    .token.show-lose::before { content: '❌'; color: #dc3545; }
    .token.disabled { pointer-events: none; opacity: 0.8; }
  </style>
</head>
<body class="perfil-page">

  <?php include 'nav.html'; ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div class="card shadow-lg card-profile">
          <div class="card-header">
             <h4 class="mb-0 text-center fw-bold text-white">Pepito Paga Doble</h4>
          </div>
          <div class="card-body">
            
            <form id="startForm">
              <p>¡Pepito muestra una ficha! Siguela y apreta la ganadora.</p>
              <div class="input-group mb-3">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="montoApostar" placeholder="Monto (Mín: 100)" min="100" max="<?php echo $saldo_actual; ?>" required>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-play me-2"></i>Apostar
                </button>
              </div>
            </form>
            
            <div id="gameMessage" class="alert alert-info text-center" style="display: none;">
              ¡Observa bien!
            </div>
            
            <div class="game-container mt-4">
              <div class="token-board">
                <div class="token" id="token-0" data-index="0"></div>
                <div class="token" id="token-1" data-index="1"></div>
                <div class="token" id="token-2" data-index="2"></div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.html'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="login.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      
      
      const loseSound = new Audio('audio/pepito_lose.mp3');
    
      
      const startForm = document.getElementById('startForm');
      const gameMessage = document.getElementById('gameMessage');
      const montoInput = document.getElementById('montoApostar');
      const tokens = document.querySelectorAll('.token');
      
      let gameInProgress = false;
      let gameResult = null; // Aquí guardaremos el resultado del servidor
      const saldoActual = <?php echo $saldo_actual; ?>;

      // --- 1. INICIAR EL JUEGO (AL APRETAR "APOSTAR") ---
      startForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (gameInProgress) return;

        const monto = parseInt(montoInput.value);

        if (monto < 100) {
          alert('La apuesta mínima es de $100.');
          return;
        }
        if (monto > saldoActual) {
          alert('No tienes saldo suficiente para esa apuesta.');
          return;
        }

        gameInProgress = true;
        gameResult = null;
        startForm.querySelector('button').disabled = true;
        gameMessage.style.display = 'block';
        gameMessage.className = 'alert alert-info text-center';
        gameMessage.textContent = '¡Observa bien la ficha!';
        
        // Resetear fichas
        tokens.forEach(token => {
            token.classList.remove('show-money', 'show-lose');
        });

        // --- 2. LLAMAR AL SERVIDOR (LA ESTAFA) ---
        try {
          gameResult = await handlePlayAPI(monto);
        } catch (err) {
          gameMessage.className = 'alert alert-danger text-center';
          gameMessage.textContent = 'Error de conexión: ' + err.message;
          gameInProgress = false;
          startForm.querySelector('button').disabled = false;
          return;
        }

        // --- 3. MOSTRAR LA FICHA GANADORA (EL ENGAÑO) ---
        const winningToken = tokens[1]; // Siempre mostramos la del medio
        winningToken.classList.add('show-money');
        
        // --- 4. REVOLVER (ANIMACIÓN) ---
        setTimeout(() => {
          gameMessage.textContent = '¡Revolviendo!';
          winningToken.classList.remove('show-money'); // Ocultar
          shuffleTokens(); // Inicia la animación
        }, 2000); // Espera 2 segs
        
        // --- 5. HABILITAR SELECCIÓN ---
        setTimeout(() => {
          gameMessage.textContent = '¡Elige tu ficha!';
          gameMessage.className = 'alert alert-warning text-center';
          tokens.forEach(token => token.classList.remove('disabled'));
        }, 5000); // Espera 5 segs (2 de muestra + 3 de animación)
      });
      
      // --- 6. AL SELECCIONAR UNA FICHA ---
      tokens.forEach(token => {
        token.addEventListener('click', function() {
          if (gameInProgress === false || gameResult === null) return;
          if (this.classList.contains('disabled')) return;

          // Deshabilitar todas las fichas
          tokens.forEach(t => t.classList.add('disabled'));

          const clickedIndex = parseInt(this.dataset.index);

          // --- 7. LA REVELACIÓN (LA ESTAFA REAL) ---
          
          if (gameResult.win === true) {
            // El servidor dijo que GANASTE (1% de chance)
            this.classList.add('show-money');
            gameMessage.className = 'alert alert-success text-center';
            gameMessage.textContent = gameResult.msg;
          
          } else {
            // El servidor dijo que PERDISTE (99% de chance)
            
            // --- ¡SONIDO DE PERDER AÑADIDO! ---
            loseSound.play();
            // --- FIN DE SONIDO ---
            
            this.classList.add('show-lose');
            gameMessage.className = 'alert alert-danger text-center';
            gameMessage.textContent = gameResult.msg;
            
            // Para la estafa: mostramos el dinero en una ficha DIFERENTE
            let realWinnerIndex = (clickedIndex + 1) % 3; // Elige la siguiente ficha
            
            setTimeout(() => {
              tokens[realWinnerIndex].classList.add('show-money');
            }, 500); // Pequeño delay
          }
          
          // --- 8. REINICIAR JUEGO ---
          setTimeout(() => {
            window.location.reload(); // Recarga la página para el nuevo saldo
          }, 3500); // Espera 3.5 segs
        });
      });
      
      // --- FUNCIÓN DE API ---
      async function handlePlayAPI(monto) {
        const formData = new FormData();
        formData.append('action', 'play');
        formData.append('monto', monto);
        
        const response = await fetch('juego_pepito_api.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        
        if (!result.success) {
          throw new Error(result.msg);
        }
        return result; 
      }
      
      // --- FUNCIÓN DE ANIMACIÓN (SHUFFLE) ---
      function shuffleTokens() {
        tokens.forEach(t => t.classList.add('disabled'));
        
        const positions = ['translateX(-120px)', 'translateX(0)', 'translateX(120px)'];
        
        let intervalCount = 0;
        const shuffleInterval = setInterval(() => {
          positions.sort(() => Math.random() - 0.5);
          
          tokens[0].style.transform = positions[0];
          tokens[1].style.transform = positions[1];
          tokens[2].style.transform = positions[2];
          
          intervalCount++;
          if (intervalCount >= 6) { 
            clearInterval(shuffleInterval);
          }
        }, 500); 
      }

    });
  </script>

</body>
</html>