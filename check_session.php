<?php
session_start();

// Comprobar si el usuario está logueado
if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && isset($_SESSION['user_saldo'])) {
    // El usuario está logueado, devolver sus datos
    echo json_encode([
        "success" => true,
        "nombre" => $_SESSION['user_name'],
        "saldo" => $_SESSION['user_saldo']
    ]);
} else {
    // El usuario no está logueado
    echo json_encode(["success" => false]);
}
?>