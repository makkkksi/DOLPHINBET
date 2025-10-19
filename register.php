<?php
require 'api/v1/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? ''; // En tu BD será 'username'
    $password = $_POST['password'] ?? '';

    $connObj = new Conexion();
    $conn = $connObj->getConnection();

    // --- PASO 1: Validar que el email (username) no exista ---
    $stmt_check = $conn->prepare("SELECT id FROM usuario WHERE username = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["success" => false, "msg" => "Email ya registrado"]);
        $stmt_check->close();
        $connObj->closeConnection();
        exit;
    }
    $stmt_check->close();

    // --- PASO 2: Iniciar una transacción ---
    $conn->autocommit(FALSE);
    $conn->begin_transaction();

    try {
        // --- PASO 3: Insertar en 'persona' ---
        $stmt_persona = $conn->prepare("INSERT INTO persona (fecha_nacimiento, activo) VALUES (CURDATE(), 1)");
        $stmt_persona->execute();
        $persona_id = $conn->insert_id;
        $stmt_persona->close();

        // --- PASO 4: Insertar en 'documento_identidad' (MODIFICADO) ---
        
        // ¡CAMBIO! Guardamos el nombre completo en 'nombres'
        $nombres = $nombre_completo; 
        // ¡CAMBIO! Dejamos los apellidos vacíos en lugar de 'Usuario'
        $apellido_paterno_vacio = ''; 
        $apellido_materno_vacio = '';
        
        $stmt_doc = $conn->prepare(
            "INSERT INTO documento_identidad (valor, persona_id, nombres, apellido_paterno, apellido_materno, orden_apeliido_id, nacionalidad_id, genero_id, documento_tipo_id, activo) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
        );
        $valor_doc = 'RUT-' . $email;
        $id_relleno = 1; // Usamos '1' para orden_apellido, nacionalidad, etc.
        
        // ¡CAMBIO! Pasamos los valores modificados
        $stmt_doc->bind_param("sisssiiii", $valor_doc, $persona_id, $nombres, $apellido_paterno_vacio, $apellido_materno_vacio, $id_relleno, $id_relleno, $id_relleno, $id_relleno);
        $stmt_doc->execute();
        $stmt_doc->close();

        // --- PASO 5: Insertar en 'usuario' ---
        $password_md5 = md5($password);
        $stmt_usuario = $conn->prepare("INSERT INTO usuario (username, password, persona_id, activo) VALUES (?, ?, ?, 1)");
        $stmt_usuario->bind_param("ssi", $email, $password_md5, $persona_id);
        $stmt_usuario->execute();
        $usuario_id = $conn->insert_id;
        $stmt_usuario->close();

        // --- PASO 6: Insertar en 'billetera' ---
        $stmt_billetera = $conn->prepare("INSERT INTO billetera (usuario_id, saldo, activo) VALUES (?, 0, 1)");
        $stmt_billetera->bind_param("i", $usuario_id);
        $stmt_billetera->execute();
        $stmt_billetera->close();

        // --- PASO 7: Confirmar transacción ---
        $conn->commit();
        echo json_encode(["success" => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "msg" => "Error al registrar. Detalles: " . $e->getMessage()]);
    }

    $conn->autocommit(TRUE);
    $connObj->closeConnection();
}
?>