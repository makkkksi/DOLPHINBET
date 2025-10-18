<?php

class Persona
{
    public function __construct() {}
    
    public function getAll()
    {
        $lista = [];
        $con = new Conexion();
        $query = "
                SELECT 
                    per.id persona_id,
                    per.activo persona_activo
                FROM persona per
                ";
        
        $rs = mysqli_query($con->getConnection(), $query);
        if ($rs) {
            while ($registro = mysqli_fetch_assoc($rs)) {
                $registro['persona_activo'] = $registro['persona_activo'] == 1 ? true : false;
                
                include_once '../documento_identidad/modelo.php';

                $documentoIdentidad = new DocumentoIdentidad();
                
                $listaDocumentoIdentidad = [];
                try {
                    $listaDocumentoIdentidad = $documentoIdentidad->getByPersonaId($registro['persona_id']);
                } catch (\Throwable $th) {
                    $listaDocumentoIdentidad = [];
                }

                //debemos trabajar con el objeto
                array_push($lista, array(
                    'id' => $registro['persona_id'],
                    'documento_identidad' => $listaDocumentoIdentidad,
                    'activo' => $registro['persona_activo']
                ));
            }
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $lista;
    }

    public function getById($_id)
    {
        try {
            $lista = $this->getAll();
            foreach ($lista as $buscado) {
                if ($buscado['id'] == $_id) {
                    return $buscado;
                }
            }
        } catch (\Throwable $th) {
            return null;
        }
        return null;
    }
}