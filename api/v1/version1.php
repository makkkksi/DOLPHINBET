<?php
$_method = $_SERVER['REQUEST_METHOD'];
$_host = $_SERVER['HTTP_HOST'];
$_uri = $_SERVER['REQUEST_URI'];
$_partes = explode('/', $_uri);

// echo 'partes: ';
// var_dump($_partes);
$_parametros = explode('?',$_partes[count($_partes)-1])[1];
$_parametroID = explode('id=', $_parametros)[1];
$_personaId = explode('personaId=', $_parametros)[1];
// echo $_parametros;
// echo $_parametroID;
// echo $_personaId;

//configuración de headers
header("Access-Control-Allow-Origin: *"); // restriccion de acceso desde otros servidores
header("Access-Control-ALlow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS"); // metodos permitidos: GET: para obtener y POST para agregar uno nuevo
header("Content-Type: application/json; charset=UTF-8");

//Configuración de Authorization
$_authorization = null;
try {
    // if (isset(getallheaders()['Authorization'])) {
    //      $_authorization = getallheaders()['Authorization'];
    //     // echo 'tenemos una autorizacion';
    // } else {
    //     http_response_code(401);
    //     echo json_encode(['error' => 'No tiene autorizacion']);
    // }
} catch (Exception $e) {
    echo 'error';
}

$_token_get = 'Bearer UDP2025';
$_token_post = 'Bearer UDP2025';
$_token_patch = 'Bearer UDP2025';
$_token_disable = 'Bearer UDP2025';
$_token_put = 'Bearer UDP2025';
$_token_options = 'Bearer UDP2025';

// Tables