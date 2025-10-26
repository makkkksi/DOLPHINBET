<?php
session_start();

// 1. VERIFICAR QUE EL USUARIO ESTÉ LOGUEADO
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "msg" => "No autorizado"]);
    exit;
}

// 2. INCLUIR CONEXIÓN
require 'api/v1/conexion.php';

// 3. OBTENER DATOS
$usuario_id = $_SESSION['user_id'];
$tipo = $_POST['tipo'] ?? ''; // 'abonar' o 'retirar'
$monto = (int)($_POST['monto'] ?? 0); // (int) para seguridad

if ($monto <= 0) {
    echo json_encode(["success" => false, "msg" => "El monto debe ser positivo."]);
    exit;
}

// ===== INICIO DE CAMBIO (PHP) =====
// 4. VALIDACIÓN DE LÍMITE DE ABONO (La más importante)
if ($tipo === 'abonar' && $monto > 100000) {
    echo json_encode(["success" => false, "msg" => "El monto máximo para abonar es $100.000."]);
    exit;
}
// ===== FIN DE CAMBIO (PHP) =====

$connObj = new Conexion();
$conn = $connObj->getConnection();

// 5. INICIAR TRANSACCIÓN (O todo o nada)
$conn->autocommit(FALSE);
$conn->begin_transaction();

try {
    // 6. OBTENER DATOS ACTUALES DE LA BILLETERA
    $stmt = $conn->prepare("SELECT id, saldo FROM billetera WHERE usuario_id = ? AND activo = 1 FOR UPDATE");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Billetera no encontrada.");
    }

    $billetera = $result->fetch_assoc();
    $billetera_id = $billetera['id'];
    $saldo_actual_db = (int)$billetera['saldo'];
    $stmt->close();

    $nuevo_saldo = 0;
    $tipo_historial_id = 0;

    // 7. LÓGICA DE ABONO O RETIRO
    if ($tipo === 'abonar') {
        $nuevo_saldo = $saldo_actual_db + $monto;
        $tipo_historial_id = 4; // 'Abono' (según tu BD)
    
    } elseif ($tipo === 'retirar') {
        if ($monto > $saldo_actual_db) {
            throw new Exception("Saldo insuficiente.");
        }
        $nuevo_saldo = $saldo_actual_db - $monto;
        $tipo_historial_id = 3; // 'Retiro' (según tu BD)

    } else {
        throw new Exception("Tipo de transacción no válido.");
    }

    // 8. ACTUALIZAR SALDO EN LA BILLETERA
    $stmt_update = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
    $stmt_update->bind_param("ii", $nuevo_saldo, $billetera_id);
    $stmt_update->execute();
    $stmt_update->close();

    // 9. INSERTAR EN EL HISTORIAL
    $stmt_hist = $conn->prepare(
        "INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) 
         VALUES (?, NOW(), ?, ?, 1)"
    );
    $stmt_hist->bind_param("iii", $billetera_id, $tipo_historial_id, $monto);
    $stmt_hist->execute();
    $stmt_hist->close();

    // 10. SI TODO SALIÓ BIEN, CONFIRMAR
    $conn->commit();

    // 11. ACTUALIZAR LA SESIÓN
    $_SESSION['user_saldo'] = $nuevo_saldo;

    echo json_encode(["success" => true, "nuevoSaldo" => $nuevo_saldo]);

} catch (Exception $e) {
    // 12. SI ALGO FALLÓ, REVERTIR
    $conn->rollback();
    echo json_encode(["success" => false, "msg" => $e->getMessage()]);
}

// 13. CERRAR CONEXIÓN
$conn->autocommit(TRUE);
$connObj->closeConnection();
?>