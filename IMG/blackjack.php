<?php
session_start();

// 1. BLOQUE DE PROTECCIÓN
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?error=not_logged_in');
    exit;
}

// 2. RECUPERAR DATOS DEL USUARIO
$saldo_actual = $_SESSION['user_saldo'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DolphinBet - Blackjack</title>
  <link rel="icon" type="image/svg+xml" href="IMG/ico.svg">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
  
  <style>
    .blackjack-table {
      background-image: url('IMG/mesa_blackjack.png');
      background-size: contain; 
      background-position: center;
      background-repeat: no-repeat;
      padding: 2rem;
      min-height: 500px; 
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    
    .betting-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-grow: 1; 
    }
    
    .hand {
      min-height: 120px;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .hand-label {
      font-size: 1.2rem;
      font-weight: bold;
      color: white;
      text-shadow: 0 2px 2px rgba(0,0,0,0.5);
    }
    
    .score-box {
      background: rgba(0,0,0,0.4);
      color: white;
      padding: 5px 15px;
      border-radius: 10px;
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
    }
    
    .card-img {
      width: 80px; 
      height: auto;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.3);
      margin-left: -40px; 
    }
    
    .hand > .card-img:first-child {
      margin-left: 0; 
    }
  </style>
</head>
<body class="perfil-page">

  <?php include 'nav.html'; ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="card shadow-lg card-profile">
          <div class="card-header">
            <h4 class="mb-0 text-center fw-bold text-white">Blackjack</h4>
          </div>
          <div class="card-body p-0 p-md-3">
            
            <div id="gameArea" class="blackjack-table" style="display: flex;">
              
              <div class="dealer-area text-center" id="dealerArea_div" style="display: none;">
                <div class="d-inline-flex align-items-center mb-2 p-2" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                  <span class="hand-label me-3">DEALER</span>
                  <span id="dealerScore" class="score-box">?</span>
                </div>
                <div id="dealerHand" class="hand">
                </div>
              </div>

              <div id="bettingArea" class="betting-container">
                <div class="p-4 text-center" style="background: rgba(0,0,0,0.7); border-radius: 15px; max-width: 400px;">
                  <h4 class="text-white">Coloca tu apuesta</h4>
                  <p class="text-white-50">Saldo: $<?php echo number_format($saldo_actual, 0, ',', '.'); ?></p>
                  <form id="betForm">
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="montoApostar" placeholder="Mín: 100" min="100" max="<?php echo $saldo_actual; ?>" required>
                      <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-2"></i>Repartir
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="player-area text-center" id="playerArea_div" style="display: none;">
                <div id="playerHand" class="hand mb-2">
                </div>
                <div class="d-inline-flex align-items-center p-2" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                  <span class="hand-label me-3">JUGADOR</span>
                  <span id="playerScore" class="score-box">0</span>
                </div>
              </div>
            </div>

            <div id="gameControls" class="card-footer" style="display: none;">
              <div id="gameMessage" class="alert alert-info text-center" style="display: none;"></div>
              <div class="d-grid gap-2 d-md-flex justify-content-center">
                <button id="hitBtn" class="btn btn-warning btn-lg">
                  <i class="fas fa-plus-circle me-2"></i>Pedir (Hit)
                </button>
                <button id="standBtn" class="btn btn-primary btn-lg">
                  <i class="fas fa-hand-paper me-2"></i>Plantarse (Stand)
                </button>
                <button id="playAgainBtn" class="btn btn-success btn-lg" style="display: none;">
                  <i class="fas fa-redo me-2"></i>Jugar Otra Vez
                </button>
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
      
      // --- SONIDO ---
      // 1. Precargar todos los sonidos
      const dealSound = new Audio('audio/repartir.mp3');
      const hitSound = new Audio('audio/hit.mp3');
      const winSound = new Audio('audio/ganar.mp3');
      const loseSound = new Audio('audio/perder.mp3');
      // --- FIN SONIDO ---
      
      const bettingArea = document.getElementById('bettingArea');
      const gameArea = document.getElementById('gameArea');
      const gameControls = document.getElementById('gameControls');
      const betForm = document.getElementById('betForm');
      const montoInput = document.getElementById('montoApostar');
      
      const dealerArea_div = document.getElementById('dealerArea_div');
      const playerArea_div = document.getElementById('playerArea_div');
      
      const dealerHandEl = document.getElementById('dealerHand');
      const playerHandEl = document.getElementById('playerHand');
      const dealerScoreEl = document.getElementById('dealerScore');
      const playerScoreEl = document.getElementById('playerScore');
      
      const hitBtn = document.getElementById('hitBtn');
      const standBtn = document.getElementById('standBtn');
      const playAgainBtn = document.getElementById('playAgainBtn');
      const gameMessage = document.getElementById('gameMessage');
      
      const cardBackImg = 'IMG/cards/BACK.png';
      const saldoActual = <?php echo $saldo_actual; ?>;

      // --- 1. INICIAR EL JUEGO (APOSTAR) ---
      betForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const monto = parseInt(montoInput.value);

        if (monto < 100) {
          alert('La apuesta mínima es de $100.');
          return;
        }
        if (monto > saldoActual) {
          alert('No tienes saldo suficiente.');
          return;
        }

        betForm.querySelector('button').disabled = true;
        montoInput.disabled = true;
        gameMessage.style.display = 'none';

        const result = await handleGameAction('bet', monto);
        
        if (result.success) {
          // --- SONIDO ---
          // 2. Tocar sonido de repartir
          dealSound.play();
          // --- FIN SONIDO ---
          bettingArea.style.display = 'none'; 
          startGameUI(result.gameState);
        } else {
          alert('Error: ' + result.msg);
          betForm.querySelector('button').disabled = false;
          montoInput.disabled = false;
        }
      });

      // --- 2. PEDIR CARTA (HIT) ---
      hitBtn.addEventListener('click', async () => {
        const result = await handleGameAction('hit');
        if (!result.success) {
          alert(result.msg); return;
        }
        
        // --- SONIDO ---
        // 3. Tocar sonido de carta (hit)
        hitSound.play();
        // --- FIN SONIDO ---
        
        addCardToHand(result.newCard, playerHandEl);
        playerScoreEl.textContent = result.playerScore;
        
        if (result.status === 'playerBust') {
          // --- SONIDO ---
          // 4. Si pierde (Bust), llama a endGame (que tiene el sonido de perder)
          // Esperamos un poco para que el sonido de "hit" termine
          setTimeout(() => {
            endGame(result.msg, false); // false = no ganó
          }, 500); // 500ms de espera
          // --- FIN SONIDO ---
        }
      });
      
      // --- 3. PLANTARSE (STAND) ---
      standBtn.addEventListener('click', async () => {
        hitBtn.disabled = true;
        standBtn.disabled = true;
        gameMessage.textContent = 'Dealer está jugando...';
        gameMessage.style.display = 'block';
        gameMessage.className = 'alert alert-info text-center';

        const result = await handleGameAction('stand');
        if (!result.success) {
          alert(result.msg); 
          hitBtn.disabled = false;
          standBtn.disabled = false;
          return;
        }
        
        renderHand(result.dealerHand, dealerHandEl);
        dealerScoreEl.textContent = result.dealerScore;
        
        let playerWon = (result.status === 'dealerBust' || result.status === 'playerWin');
        
        // --- SONIDO ---
        // 5. Llama a endGame, que decidirá si toca sonido de ganar o perder
        endGame(result.msg, playerWon);
        // --- FIN SONIDO ---
      });
      
      // --- 4. JUGAR OTRA VEZ ---
      playAgainBtn.addEventListener('click', () => {
        window.location.reload(); 
      });
      
      // --- FUNCIÓN DE API ---
      async function handleGameAction(action, monto = 0) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('monto', monto);
        
        try {
          const response = await fetch('juego_blackjack_api.php', {
            method: 'POST',
            body: formData
          });
          return await response.json();
        } catch (err) {
          return { success: false, msg: 'Error de conexión: ' + err.message };
        }
      }
      
      // --- FUNCIONES DE UI ---
      function startGameUI(state) {
        gameControls.style.display = 'block';
        dealerArea_div.style.display = 'block'; 
        playerArea_div.style.display = 'block'; 
        
        hitBtn.style.display = 'inline-block';
        standBtn.style.display = 'inline-block';
        playAgainBtn.style.display = 'none';
        
        renderHand(state.dealerHand_visible, dealerHandEl);
        renderHand(state.playerHand, playerHandEl);
        
        dealerScoreEl.textContent = '?';
        playerScoreEl.textContent = state.playerScore;
        
        // Comprobar Blackjack inmediato
        if (state.status === 'blackjack') {
          // --- SONIDO ---
          // 6. Llama a endGame si hay blackjack (sonido de ganar)
          endGame(state.msg, true); 
          // --- FIN SONIDO ---
        }
      }
      
      function renderHand(hand, element) {
        element.innerHTML = ''; 
        hand.forEach(card => {
          addCardToHand(card, element);
        });
      }
      
      function addCardToHand(card, element) {
        const img = document.createElement('img');
        img.src = card.img; 
        img.classList.add('card-img');
        element.appendChild(img);
      }
      
      // --- FUNCIÓN DE FIN DE JUEGO (MODIFICADA) ---
      function endGame(msg, playerWon) {
        
        // --- SONIDO ---
        // 7. Toca el sonido correspondiente
        if (playerWon) {
          winSound.play();
        } else {
          loseSound.play();
        }
        // --- FIN SONIDO ---
        
        hitBtn.style.display = 'none';
        standBtn.style.display = 'none';
        playAgainBtn.style.display = 'inline-block';
        
        gameMessage.textContent = msg;
        gameMessage.className = playerWon ? 'alert alert-success text-center' : 'alert alert-danger text-center';
        gameMessage.style.display = 'block';
      }

    });
  </script>

</body>
</html>