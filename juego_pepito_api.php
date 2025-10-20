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
$monto = (int)($_POST['monto'] ?? 0);

if ($action !== 'play') {
    echo json_encode(["success" => false, "msg" => "Acción no válida."]);
    exit;
}
if ($monto < 100) {
    echo json_encode(["success" => false, "msg" => "La apuesta mínima es de $100."]);
    exit;
}

$connObj = new Conexion();
$conn = $connObj->getConnection();

// 4. INICIAR TRANSACCIÓN
$conn->begin_transaction();
try {
    // 5. OBTENER SALDO DE LA BD (FOR UPDATE bloquea la fila)
    $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? FOR UPDATE");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $billetera = $stmt->get_result()->fetch_assoc();
    
    if (!$billetera) throw new Exception("Billetera no encontrada.");
    if ($monto > $billetera['saldo']) throw new Exception("Saldo insuficiente.");

    $billetera_id = $billetera['id'];
    $nuevo_saldo = $billetera['saldo'];

    // 6. RESTAR LA APUESTA (Pase lo que pase, el usuario paga primero)
    $nuevo_saldo -= $monto;
    $stmt_upd = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
    $stmt_upd->bind_param("ii", $nuevo_saldo, $billetera_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // 7. REGISTRAR APUESTA EN HISTORIAL (ID 2 = 'Apuesta')
    $stmt_hist = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), 2, ?, 1)");
    $stmt_hist->bind_param("ii", $billetera_id, $monto);
    $stmt_hist->execute();
    $stmt_hist->close();

    // 8. LA ESTAFA: 99% DE PROBABILIDAD DE PERDER
    $roll = rand(1, 100);
    $winnings = $monto * 2; // Pepito paga "doble"
    $user_wins = false;
    $message = "Te cago pepito compare.";

    // El 1% de probabilidad de ganar
    if ($roll === 1) { 
        $user_wins = true;
        $message = "¡Increíble! ¡Ganaste $" . number_format($winnings) . "!";

        // 9. SI GANA, DEVOLVERLE EL DINERO
        $nuevo_saldo += $winnings;
        $stmt_upd_win = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
        $stmt_upd_win->bind_param("ii", $nuevo_saldo, $billetera_id);
        $stmt_upd_win->execute();
        $stmt_upd_win->close();

        // 10. REGISTRAR GANANCIA EN HISTORIAL (ID 5 = 'Gana')
        $stmt_hist_win = $conn->prepare("INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) VALUES (?, NOW(), 5, ?, 1)");
        $stmt_hist_win->bind_param("ii", $billetera_id, $winnings);
        $stmt_hist_win->execute();
        $stmt_hist_win->close();
    }

    // 11. CONFIRMAR TRANSACCIÓN
    $conn->commit();
    
    // 12. ACTUALIZAR SESIÓN Y RESPONDER
    $_SESSION['user_saldo'] = $nuevo_saldo;

    echo json_encode([
        "success" => true, 
        "win" => $user_wins,
        "msg" => $message,
        "newSaldo" => $nuevo_saldo
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "msg" => $e->getMessage()]);
}

$connObj->closeConnection();
?>