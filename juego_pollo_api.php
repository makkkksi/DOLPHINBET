<?php
session_start();

// 1. VERIFICAR LOGIN
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "msg" => "No autorizado"]);
    exit;
}

// 2. INCLUIR CONEXIÓN
require 'api/v1/conexion.php';

// 3. OBTENER DATOS
$usuario_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

$connObj = new Conexion();
$conn = $connObj->getConnection();

// --- LÓGICA DEL JUEGO ---
switch ($action) {
    // --- ACCIÓN: INICIAR JUEGO ---
    case 'start':
        $monto = (int)($_POST['monto'] ?? 0);

        if ($monto < 100) {
            echo json_encode(["success" => false, "msg" => "La apuesta mínima es de $100."]);
            exit;
        }

        $conn->begin_transaction();
        try {
            // Obtener saldo de la BD (FOR UPDATE bloquea la fila)
            $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? FOR UPDATE");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $billetera = $stmt->get_result()->fetch_assoc();
            
            if (!$billetera) throw new Exception("Billetera no encontrada.");
            if ($monto > $billetera['saldo']) throw new Exception("Saldo insuficiente.");

            $billetera_id = $billetera['id'];

            // 1. Restar la apuesta
            $nuevo_saldo = $billetera['saldo'] - $monto;
            $stmt_upd = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
            $stmt_upd->bind_param("ii", $nuevo_saldo, $billetera_id);
            $stmt_upd->execute();
            $stmt_upd->close();

            // 2. Registrar en historial (ID 2 = 'Apuesta')
            $stmt_hist = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), 2, ?, 1)");
            $stmt_hist->bind_param("ii", $billetera_id, $monto);
            $stmt_hist->execute();
            $stmt_hist->close();

            // 3. Crear el estado del juego en la sesión
            $gameData = [
                'active' => true,
                'bet' => $monto,
                'lane' => 0,       // Carril inicial
                'winnings' => $monto // Ganancia actual (es la apuesta)
            ];
            $_SESSION['pollo_game'] = $gameData;
            $_SESSION['user_saldo'] = $nuevo_saldo; // Actualizar saldo en sesión

            $conn->commit();
            echo json_encode(["success" => true, "gameInProgress" => true, "gameData" => $gameData]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "msg" => $e->getMessage()]);
        }
        $connObj->closeConnection();
        break;

    // --- ACCIÓN: AVANZAR CARRIL (MODIFICADO) ---
    case 'advance':
        if (!isset($_SESSION['pollo_game']) || !$_SESSION['pollo_game']['active']) {
            echo json_encode(["success" => false, "msg" => "No hay juego activo."]);
            exit;
        }

        $gameData = $_SESSION['pollo_game'];
        $currentLane = $gameData['lane'];
        
        // --- ¡NUEVA VALIDACIÓN DE LÍMITE DE CARRIL! ---
        if ($currentLane >= 5) {
            echo json_encode(["success" => false, "msg" => "Ya estás en el último carril (5). No puedes avanzar más, ¡retírate para cobrar!"]);
            $connObj->closeConnection();
            exit;
        }

        // Lógica de riesgo: 10% en carril 0, 15% en 1, 20% en 2, etc. Máx 50%.
        $risk_percent = min(20 + ($currentLane * 10), 75);
        $roll = rand(1, 100);

        // SI MUERE (El roll está dentro del porcentaje de riesgo)
        if ($roll <= $risk_percent) {
            unset($_SESSION['pollo_game']); // El juego termina, la apuesta se pierde
            echo json_encode([
                "success" => true, 
                "gameInProgress" => false, 
                "gameData" => null,
                "msg" => "¡Oh no! ¡El pollo fue atropellado! Has perdido $" . number_format($gameData['bet'])
            ]);
        
        // SI VIVE
        } else {
            $gameData['lane']++;
            $gameData['winnings'] *= 2; // Duplica la ganancia
            $_SESSION['pollo_game'] = $gameData; // Guarda el nuevo estado

            echo json_encode([
                "success" => true, 
                "gameInProgress" => true, 
                "gameData" => $gameData
            ]);
        }
        $connObj->closeConnection();
        break;

    // --- ACCIÓN: RETIRARSE (COBRAR) ---
    case 'cashout':
        if (!isset($_SESSION['pollo_game']) || !$_SESSION['pollo_game']['active']) {
            echo json_encode(["success" => false, "msg" => "No hay juego activo."]);
            exit;
        }

        $gameData = $_SESSION['pollo_game'];
        $winnings = $gameData['winnings'];

        // Si cobra en el carril 0, solo recupera su apuesta
        if ($gameData['lane'] == 0) {
            $tipo_historial_id = 4; // 'Abono' (Devolución de apuesta)
        } else {
            $tipo_historial_id = 5; // 'Gana' (según tu BD)
        }
        
        $conn->begin_transaction();
        try {
            // Obtener saldo de la BD
            $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? FOR UPDATE");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $billetera = $stmt->get_result()->fetch_assoc();
            
            if (!$billetera) throw new Exception("Billetera no encontrada.");
            $billetera_id = $billetera['id'];

            // 1. Sumar las ganancias
            $nuevo_saldo = $billetera['saldo'] + $winnings;
            $stmt_upd = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
            $stmt_upd->bind_param("ii", $nuevo_saldo, $billetera_id);
            $stmt_upd->execute();
            $stmt_upd->close();

            // 2. Registrar en historial
            $stmt_hist = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), ?, ?, 1)");
            $stmt_hist->bind_param("iii", $billetera_id, $tipo_historial_id, $winnings);
            $stmt_hist->execute();
            $stmt_hist->close();

            // 3. Actualizar sesión y terminar juego
            $_SESSION['user_saldo'] = $nuevo_saldo;
            unset($_SESSION['pollo_game']);
            
            $conn->commit();
            echo json_encode([
                "success" => true, 
                "gameInProgress" => false,
                "gameData" => null, 
                "msg" => "¡Retiro exitoso! Has ganado $" . number_format($winnings)
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "msg" => $e->getMessage()]);
        }
        $connObj->closeConnection();
        break;

    default:
        echo json_encode(["success" => false, "msg" => "Acción no válida."]);
        break;
}
?>