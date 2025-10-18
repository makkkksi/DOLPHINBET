<?php

include_once '../version1.php';

switch ($_method) {
    case 'GET':
        if ($_authorization === $_token_get) {
            $lista = [];
            //llamamos al archivo que contiene la clase conexion
            include_once '../conexion.php';
            include_once 'modelo.php';
            //se realiza la instancia al modelo
            $modelo = new DocumentoIdentidad();

            if ($_personaId) {
                $unico = $modelo->getByPersonaId($_personaId);
                if($unico){
                    http_response_code(200);
                    echo json_encode($unico);
                }else{
                    http_response_code(404);
                    echo json_encode(['error' => 'No Encontrado']);
                }
            } else { //todo
                http_response_code(401);
                echo json_encode(['error' => 'Faltan parámetros']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Prohibido']);
        }
        break;
    default:
        http_response_code(501);
        echo json_encode(['error' => 'No implementado']);
        break;
}
