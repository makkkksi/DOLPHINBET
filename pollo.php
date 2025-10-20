<?php
session_start();

// 1. BLOQUE DE PROTECCIÓN
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=not_logged_in');
    exit;
}

// 2. RECUPERAR DATOS DEL USUARIO
$usuario_id = $_SESSION['user_id'];
$saldo_actual = $_SESSION['user_saldo'] ?? 0;

// 3. VERIFICAR SI HAY UN JUEGO EN CURSO
$juego_en_curso = false;
$juego_data = [
    'lane' => 0,
    'bet' => 0,
    'winnings' => 0
];

if (isset($_SESSION['pollo_game']) && $_SESSION['pollo_game']['active']) {
    $juego_en_curso = true;
    $juego_data = $_SESSION['pollo_game'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Pollo</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
  
  <style>
    .game-board {
      background-color: #333;
      padding: 10px;
      border-radius: 10px;
      overflow: hidden;
      border: 5px solid #555;
      position: relative; 
    }
    .lane {
      background-color: #5a5a5a;
      border-bottom: 2px dashed #777;
      height: 70px;
      display: flex;
      align-items: center;
      padding-left: 10px;
      position: relative;
    }
    .lane:last-child { border-bottom: none; }
    .lane.safe { background-color: #28a745; }

    #chicken {
      width: 75px; 
      height: 75px;
      object-fit: contain;
      position: absolute;
      left: 50%; /* Centrado */
      transform: translateX(-50%); /* Centrado */
      top: 360px; /* Posición inicial (calculada por JS) */
      transition: top 0.3s ease-in-out;
      z-index: 10;
    }
  </style>

</head>
<body class="perfil-page">

  <?php include 'nav.html'; ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <div class="card shadow-lg card-profile">
          <div class="card-header">
            <h4 class="mb-0 text-center fw-bold text-white">POLLO</h4>
          </div>
          <div class="card-body">
            
            <form id="startForm" class="<?php echo $juego_en_curso ? 'd-none' : ''; ?>">
              <p>Apuesta para iniciar. ¡Cada carril que cruces duplicará tu ganancia!</p>
              <div class="input-group mb-3">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="montoApostar" placeholder="Monto (Mín: 100)" min="100" max="<?php echo $saldo_actual; ?>" required>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-play me-2"></i>Iniciar Juego
                </button>
              </div>
            </form>
            
            <div id="gameControls" class="<?php echo !$juego_en_curso ? 'd-none' : ''; ?>">
              <div class="alert alert-info" id="gameStatus"></div>
              <div class="d-grid gap-2 d-md-flex">
                <button id="advanceBtn" class="btn btn-warning btn-lg flex-grow-1">
                  <i class="fas fa-arrow-up me-2"></i>Cruzar Siguiente Carril
                </button>
                <button id="cashoutBtn" class="btn btn-primary btn-lg">
                  <i class="fas fa-dollar-sign me-2"></i>Retirarse
                </button>
              </div>
            </div>

            <div class="game-board mt-4">
              <img id="chicken" src="IMG/pollo_sprite.png" alt="Pollo">
              <div class="lane"></div> <div class="lane"></div> <div class="lane"></div> <div class="lane"></div> <div class="lane"></div> <div class="lane safe"></div> </div>

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
    
      const jumpSound = new Audio('audio/salto.mp3');
      const loseSound = new Audio('audio/muerte.mp3');
    
      const startForm = document.getElementById('startForm');
      const gameControls = document.getElementById('gameControls');
      const advanceBtn = document.getElementById('advanceBtn');
      const cashoutBtn = document.getElementById('cashoutBtn');
      const gameStatus = document.getElementById('gameStatus');
      const chicken = document.getElementById('chicken');

      let gameData = <?php echo json_encode($juego_data); ?>;
      let gameInProgress = <?php echo $juego_en_curso ? 'true' : 'false'; ?>;
      const saldoActual = <?php echo $saldo_actual; ?>;

      if (!startForm || !gameControls || !chicken) {
        console.error("Error: Faltan elementos esenciales del juego en el HTML.");
        return; 
      }

      // --- FUNCIÓN PRINCIPAL DE API (MODIFICADA) ---
      async function handleGameAction(action, monto = 0) {
        advanceBtn.disabled = true;
        cashoutBtn.disabled = true;
        
        const formData = new FormData();
        formData.append('action', action);
        if (monto > 0) {
          formData.append('monto', monto);
        }

        try {
          const response = await fetch('juego_pollo_api.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();

          if (!result.success) {
            throw new Error(result.msg);
          }

          gameInProgress = result.gameInProgress;
          gameData = result.gameData;

          if (!gameInProgress) {
            // --- INICIO DE LA CORRECCIÓN DE SONIDO ---
            if (result.msg.includes("atropellado")) {
                // 1. Toca el sonido
                loseSound.play();
                // 2. Espera a que termine
                loseSound.onended = function() {
                    // 3. Muestra la alerta y recarga
                    alert(result.msg);
                    window.location.reload();
                };
            } else {
                // Si ganó (o se retiró), no hay sonido, solo alerta
                alert(result.msg);
                window.location.reload();
            }
            // --- FIN DE LA CORRECCIÓN ---

          } else {
            if (action === 'advance') {
                jumpSound.play();
            }
            updateUI();
          }

        } catch (err) {
          alert('Error: ' + err.message);
          if(action !== 'start' && action !== 'advance') window.location.reload(); 
        } finally {
          // Esta lógica se moverá a updateUI para evitar conflictos
        }
      }

      // --- FUNCIÓN PARA ACTUALIZAR LA INTERFAZ ---
      function updateUI() {
        if (gameInProgress) {
          startForm.classList.add('d-none');
          gameControls.classList.remove('d-none');
          
          const currentLane = gameData.lane;
          const potentialWinnings = gameData.winnings;
          const nextRisk = 10 + (currentLane * 5); 

          const topPosition = (5 - currentLane) * 70 + 10;
          chicken.style.top = `${topPosition}px`;
          
          cashoutBtn.innerHTML = `<i class="fas fa-dollar-sign me-2"></i>Retirar $${potentialWinnings.toLocaleString('es-CL')}`;
          cashoutBtn.disabled = false; // Asegurarse de que esté habilitado

          if (currentLane === 5) {
            gameStatus.innerHTML = `
              <strong>¡FELICIDADES!</strong> ¡Has llegado al final! | 
              <strong>Ganancia Máxima:</strong> $${potentialWinnings.toLocaleString('es-CL')}
            `;
            advanceBtn.disabled = true;
            advanceBtn.innerHTML = `<i class="fas fa-flag-checkered me-2"></i>Camino Completado`;
            cashoutBtn.classList.remove('btn-primary');
            cashoutBtn.classList.add('btn-success', 'btn-lg', 'flex-grow-1');
          } else {
            gameStatus.innerHTML = `
              <strong>Carril Actual:</strong> ${currentLane} | 
              <strong>Ganancia Potencial:</strong> $${potentialWinnings.toLocaleString('es-CL')} |
              <strong>Riesgo de Morir:</strong> ${nextRisk > 50 ? 50 : nextRisk}%
            `;
            advanceBtn.disabled = false; // Habilitado
            advanceBtn.innerHTML = `<i class="fas fa-arrow-up me-2"></i>Cruzar Siguiente Carril`;
            cashoutBtn.classList.add('btn-primary');
            cashoutBtn.classList.remove('btn-success', 'btn-lg', 'flex-grow-1');
          }

        } else {
          startForm.classList.remove('d-none');
          gameControls.classList.add('d-none');
          chicken.style.top = '360px'; // Posición inicial
        }
      }

      // --- EVENT LISTENERS ---
      startForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const monto = parseInt(document.getElementById('montoApostar').value);
        
        if (monto < 100) {
          alert('La apuesta mínima es de $100.');
          return;
        }
        if (monto > saldoActual) {
          alert('No tienes saldo suficiente para esa apuesta.');
          return;
        }
        handleGameAction('start', monto);
      });

      advanceBtn.addEventListener('click', () => {
        handleGameAction('advance');
      });

      cashoutBtn.addEventListener('click', () => {
        if (confirm(`¿Seguro que quieres retirarte con $${gameData.winnings.toLocaleString('es-CL')}?`)) {
          handleGameAction('cashout');
        }
      });

      // Iniciar UI al cargar la página
      updateUI();
      
    }); 
  </script>

</body>
</html>