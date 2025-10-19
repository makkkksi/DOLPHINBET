<?php
session_start();
require 'api/v1/conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $connObj = new Conexion();
    $conn = $connObj->getConnection();

    // --- PASO 1: Buscar el usuario y verificar la contraseña ---
    $stmt = $conn->prepare("SELECT id, password, persona_id FROM usuario WHERE username = ? AND activo = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (md5($password) === $user['password']) {
            
            // --- PASO 2: Contraseña correcta. Ahora buscamos nombre y saldo ---
            $usuario_id = $user['id'];
            $persona_id = $user['persona_id'];
            $nombre_completo = "Usuario"; // Valor por defecto
            $saldo_usuario = 0;

            // ¡CAMBIO! Buscar solo 'nombres'
            $stmt_nombre = $conn->prepare("SELECT nombres FROM documento_identidad WHERE persona_id = ? AND activo = 1 LIMIT 1");
            $stmt_nombre->bind_param("i", $persona_id);
            $stmt_nombre->execute();
            $result_nombre = $stmt_nombre->get_result();
            if ($result_nombre->num_rows === 1) {
                $doc = $result_nombre->fetch_assoc();
                // ¡CAMBIO! Usar solo el valor de 'nombres'
                $nombre_completo = $doc['nombres'];
            }
            $stmt_nombre->close();

            // Buscar saldo en 'billetera'
            $stmt_saldo = $conn->prepare("SELECT saldo FROM billetera WHERE usuario_id = ? AND activo = 1");
            $stmt_saldo->bind_param("i", $usuario_id);
            $stmt_saldo->execute();
            $result_saldo = $stmt_saldo->get_result();
            if ($result_saldo->num_rows === 1) {
                $billetera = $result_saldo->fetch_assoc();
                $saldo_usuario = $billetera['saldo'];
            }
            $stmt_saldo->close();

            // --- PASO 3: Guardar todo en la sesión y devolver ---
            $_SESSION['user_id'] = $usuario_id;
            $_SESSION['user_name'] = $nombre_completo;
            $_SESSION['user_saldo'] = $saldo_usuario;
            
            echo json_encode([
                "success" => true,
                "nombre" => $nombre_completo,
                "saldo" => $saldo_usuario
            ]);

        } else {
            echo json_encode(["success" => false, "msg" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "msg" => "Usuario no encontrado"]);
    }

    $stmt->close();
    $connObj->closeConnection();
}
?>