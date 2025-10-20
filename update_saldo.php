<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "msg" => "No autorizado"]);
    exit;
}


require 'api/v1/conexion.php';


$usuario_id = $_SESSION['user_id'];
$tipo = $_POST['tipo'] ?? ''; // 'abonar' o 'retirar'
$monto = (int)($_POST['monto'] ?? 0); // (int) para seguridad

if ($monto <= 0) {
    echo json_encode(["success" => false, "msg" => "El monto debe ser positivo."]);
    exit;
}

$connObj = new Conexion();
$conn = $connObj->getConnection();


$conn->autocommit(FALSE);
$conn->begin_transaction();

try {

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


    if ($tipo === 'abonar') {
        $nuevo_saldo = $saldo_actual_db + $monto;
        $tipo_historial_id = 4; // 'Abono' (según tu BD)
    
    } elseif ($tipo === 'retirar') {
        // Doble verificación de saldo (la más importante)
        if ($monto > $saldo_actual_db) {
            throw new Exception("Saldo insuficiente.");
        }
        $nuevo_saldo = $saldo_actual_db - $monto;
        $tipo_historial_id = 3; // 'Retiro' (según tu BD)

    } else {
        throw new Exception("Tipo de transacción no válido.");
    }

    $stmt_update = $conn->prepare("UPDATE billetera SET saldo = ? WHERE id = ?");
    $stmt_update->bind_param("ii", $nuevo_saldo, $billetera_id);
    $stmt_update->execute();
    $stmt_update->close();


    $stmt_hist = $conn->prepare(
        "INSERT INTO billetera_historial (billetera_id, fecha, tipo_id, monto, activo) 
         VALUES (?, NOW(), ?, ?, 1)"
    );
    $stmt_hist->bind_param("iii", $billetera_id, $tipo_historial_id, $monto);
    $stmt_hist->execute();
    $stmt_hist->close();


    $conn->commit();

    $_SESSION['user_saldo'] = $nuevo_saldo;

    echo json_encode(["success" => true, "nuevoSaldo" => $nuevo_saldo]);

} catch (Exception $e) {

    $conn->rollback();
    echo json_encode(["success" => false, "msg" => $e->getMessage()]);
}





$conn->autocommit(TRUE);
$connObj->closeConnection();
?>