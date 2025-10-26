<?php
session_start();

// 1. VERIFICAR LOGIN
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "msg" => "No autorizado"]);
    exit;
}

// 2. INCLUIR CONEXIÓN
require 'api/v1/conexion.php';

// --- FUNCIONES DEL JUEGO ---

/**
 * Crea una baraja de 52 cartas
 */
function createDeck() {
    $suits = ['C', 'S', 'D', 'H'];
    $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    $deck = [];

    foreach ($suits as $suit) {
        foreach ($values as $value) {
            $card_name = $value . '-' . $suit;
            $card_value = 0;

            if (is_numeric($value)) {
                $card_value = (int)$value;
            } elseif ($value == 'A') {
                $card_value = 11; // El As vale 11 por defecto
            } else {
                $card_value = 10; // J, Q, K valen 10
            }

            $deck[] = [
                'name' => $card_name,
                'img' => 'IMG/cards/' . $card_name . '.png',
                'value' => $card_value
            ];
        }
    }
    return $deck;
}

/**
 * Calcula el valor de una mano, manejando los Ases (1 u 11)
 */
function calculateHandValue($hand) {
    $total = 0;
    $numAces = 0;

    foreach ($hand as $card) {
        $total += $card['value'];
        if ($card['value'] == 11) {
            $numAces++;
        }
    }
    
    // Si el total es > 21 y tenemos un As, lo contamos como 1
    while ($total > 21 && $numAces > 0) {
        $total -= 10;
        $numAces--;
    }
    return $total;
}

/**
 * Realiza un pago (o devolución) a la billetera del usuario
 */
function payPlayer($conn, $usuario_id, $billetera_id, $saldo_actual, $monto_pago, $tipo_pago) {
    // tipo_pago: 5 = 'Gana', 4 = 'Abono' (para empates/push)
    
    // 1. Actualizar saldo
    $nuevo_saldo = $saldo_actual + $monto_pago;
    $stmt_upd = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
    $stmt_upd->bind_param("ii", $nuevo_saldo, $billetera_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // 2. Registrar ganancia en historial
    $stmt_hist = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), ?, ?, 1)");
    $stmt_hist->bind_param("iii", $billetera_id, $tipo_pago, $monto_pago);
    $stmt_hist->execute();
    $stmt_hist->close();
    
    return $nuevo_saldo;
}

// --- LÓGICA DEL ENDPOINT ---

$usuario_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

$connObj = new Conexion();
$conn = $connObj->getConnection();

switch ($action) {
    // --- ACCIÓN: APOSTAR E INICIAR JUEGO ---
    case 'bet':
        $monto = (int)($_POST['monto'] ?? 0);
        if ($monto < 100) {
            echo json_encode(["success" => false, "msg" => "La apuesta mínima es $100."]);
            exit;
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? FOR UPDATE");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $billetera = $stmt->get_result()->fetch_assoc();
            
            if (!$billetera) throw new Exception("Billetera no encontrada.");
            if ($monto > $billetera['saldo']) throw new Exception("Saldo insuficiente.");

            $billetera_id = $billetera['id'];
            $nuevo_saldo = $billetera['saldo'] - $monto;

            // 1. Restar la apuesta
            $stmt_upd = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
            $stmt_upd->bind_param("ii", $nuevo_saldo, $billetera_id);
            $stmt_upd->execute();
            $stmt_upd->close();

            // 2. Registrar apuesta (ID 2 = 'Apuesta')
            $stmt_hist = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), 2, ?, 1)");
            $stmt_hist->bind_param("ii", $billetera_id, $monto);
            $stmt_hist->execute();
            $stmt_hist->close();

            // 3. Crear y barajar mazo
            $deck = createDeck();
            shuffle($deck);

            // 4. Repartir cartas
            $playerHand = [array_pop($deck), array_pop($deck)];
            $dealerHand = [array_pop($deck), array_pop($deck)];
            $playerScore = calculateHandValue($playerHand);

            // 5. Guardar estado del juego en la sesión
            $_SESSION['blackjack_game'] = [
                'deck' => $deck,
                'playerHand' => $playerHand,
                'dealerHand' => $dealerHand,
                'bet' => $monto,
                'status' => 'playing'
            ];
            $_SESSION['user_saldo'] = $nuevo_saldo; // Actualizar saldo en sesión

            $conn->commit();

            // 6. Comprobar Blackjack inmediato
            if ($playerScore == 21) {
                // El jugador tiene Blackjack, el juego termina.
                // Se paga 3 a 2 (1.5x la apuesta, más la apuesta original = 2.5x)
                // (Simplifiquemos a 2x por ahora, como el pollo)
                $payout = $monto * 2;
                $nuevo_saldo = payPlayer($conn, $usuario_id, $billetera_id, $nuevo_saldo, $payout, 5); // 5 = Gana
                $_SESSION['user_saldo'] = $nuevo_saldo;
                unset($_SESSION['blackjack_game']);

                 echo json_encode([
                    "success" => true,
                    "gameState" => [
                        "status" => "blackjack",
                        "msg" => "¡BLACKJACK! ¡Ganas $" . number_format($payout) . "!",
                        "playerHand" => $playerHand,
                        "dealerHand_visible" => $dealerHand, // Mostrar ambas
                        "playerScore" => $playerScore
                    ]
                ]);
            } else {
                // 6. Devolver estado inicial del juego
                echo json_encode([
                    "success" => true,
                    "gameState" => [
                        "status" => "playing",
                        "playerHand" => $playerHand,
                        "dealerHand_visible" => [$dealerHand[0], ['img' => 'IMG/cards/BACK.png']], // Una oculta
                        "playerScore" => $playerScore
                    ]
                ]);
            }

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "msg" => $e->getMessage()]);
        }
        break;

    // --- ACCIÓN: PEDIR CARTA ---
    case 'hit':
        if (!isset($_SESSION['blackjack_game'])) {
            echo json_encode(["success" => false, "msg" => "No hay juego activo."]);
            exit;
        }

        $game = $_SESSION['blackjack_game'];
        
        $newCard = array_pop($game['deck']);
        $game['playerHand'][] = $newCard;
        $playerScore = calculateHandValue($game['playerHand']);
        
        $_SESSION['blackjack_game'] = $game; // Guardar mazo y mano

        if ($playerScore > 21) {
            unset($_SESSION['blackjack_game']); // Juego terminado
            echo json_encode([
                "success" => true, 
                "status" => "playerBust",
                "msg" => "¡Te pasaste! (Bust). Pierdes $" . number_format($game['bet']),
                "newCard" => $newCard,
                "playerScore" => $playerScore
            ]);
        } else {
            echo json_encode([
                "success" => true, 
                "status" => "playing",
                "newCard" => $newCard,
                "playerScore" => $playerScore
            ]);
        }
        break;

    // --- ACCIÓN: PLANTARSE ---
    case 'stand':
        if (!isset($_SESSION['blackjack_game'])) {
            echo json_encode(["success" => false, "msg" => "No hay juego activo."]);
            exit;
        }

        $game = $_SESSION['blackjack_game'];
        $bet = $game['bet'];
        
        $playerScore = calculateHandValue($game['playerHand']);
        $dealerHand = $game['dealerHand'];
        $dealerScore = calculateHandValue($dealerHand);

        // Lógica del Dealer: Pide carta hasta 17 o más
        while ($dealerScore < 17) {
            $dealerHand[] = array_pop($game['deck']);
            $dealerScore = calculateHandValue($dealerHand);
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? FOR UPDATE");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $billetera = $stmt->get_result()->fetch_assoc();
            $saldo_actual_db = $billetera['saldo'];
            $billetera_id = $billetera['id'];

            $status = "";
            $msg = "";

            // Determinar ganador
            if ($dealerScore > 21) {
                $status = "dealerBust";
                $msg = "¡Dealer se pasa! Ganas $" . number_format($bet * 2);
                $nuevo_saldo = payPlayer($conn, $usuario_id, $billetera_id, $saldo_actual_db, $bet * 2, 5); // 5 = Gana
                $_SESSION['user_saldo'] = $nuevo_saldo;
            } elseif ($playerScore > $dealerScore) {
                $status = "playerWin";
                $msg = "¡Ganas! $" . number_format($bet * 2);
                $nuevo_saldo = payPlayer($conn, $usuario_id, $billetera_id, $saldo_actual_db, $bet * 2, 5); // 5 = Gana
                $_SESSION['user_saldo'] = $nuevo_saldo;
            } elseif ($dealerScore > $playerScore) {
                $status = "dealerWin";
                $msg = "Dealer gana. Pierdes $" . number_format($bet);
                // No hay pago, la apuesta ya se cobró
            } else {
                $status = "push";
                $msg = "¡Empate! (Push). Se te devuelve tu apuesta.";
                $nuevo_saldo = payPlayer($conn, $usuario_id, $billetera_id, $saldo_actual_db, $bet, 4); // 4 = Abono
                $_SESSION['user_saldo'] = $nuevo_saldo;
            }
            
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "msg" => $e->getMessage()]);
            exit;
        }
        
        unset($_SESSION['blackjack_game']); // Terminar el juego

        echo json_encode([
            "success" => true,
            "status" => $status,
            "msg" => $msg,
            "dealerHand" => $dealerHand, // Enviar mano completa del dealer
            "dealerScore" => $dealerScore
        ]);
        break;

    default:
        echo json_encode(["success" => false, "msg" => "Acción no válida."]);
        break;
}

$connObj->closeConnection();
?>