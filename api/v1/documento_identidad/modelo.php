<?php

class DocumentoIdentidad
{
    public function __construct() {}

    public function getByPersonaId($_personaId)
    {
        $lista = [];
        try {
            $con = new Conexion();
            $query = "
                    SELECT 
                        docid.id id, 
                        docid.valor valor,
                        docid.nombres nombres,
                        docid.apellido_paterno apellido_paterno,
                        docid.apellido_materno apellido_materno,
                        docid.orden_apeliido_id orden_apellido_id,
                        apor.nombre orden_apellido_nombre,
                        docid.nacionalidad_id nacionalidad_id,
                        naci.nombre nacionalidad_nombre,
                        docid.genero_id genero_id,
                        gene.nombre genero_nombre,
                        docid.documento_tipo_id tipo_id,
                        tipo.nombre tipo_nombre,
                        docid.activo activo
                    FROM documento_identidad docid
                        INNER JOIN apellido_orden apor ON docid.orden_apeliido_id = apor.id
                        INNER JOIN nacionalidad naci ON (docid.nacionalidad_id = naci.id)
                        INNER JOIN genero gene ON (docid.genero_id = gene.id)
                        INNER JOIN documento_identidad_tipo tipo ON (docid.documento_tipo_id = tipo.id)
                    ";

            $rs = mysqli_query($con->getConnection(), $query);
            if ($rs) {
                while ($registro = mysqli_fetch_assoc($rs)) {
                    $registro['activo'] = $registro['activo'] == 1 ? true : false;

                    //debemos trabajar con el objeto
                    array_push($lista, array(
                        'id' => intval($registro['id']),
                        'valor' => $registro['valor'],
                        'nombres' => $registro['nombres'],
                        'apellidos' => [
                            'materno' => $registro['apellido_materno'],
                            'paterno' => $registro['apellido_paterno'],
                            'orden' => [
                                'id' => intval($registro['orden_apellido_id']),
                                'nombre' => $registro['orden_apellido_nombre']
                            ]
                        ],
                        'nacionalidad' => [
                            'id' => intval($registro['nacionalidad_id']),
                            'nombre' => $registro['nacionalidad_nombre']
                        ],
                        'genero' => [
                            'id' => intval($registro['genero_id']),
                            'nombre' => $registro['genero_nombre']
                        ],
                        'tipo' => [
                            'id' => intval($registro['tipo_id']),
                            'nombre' => $registro['tipo_nombre']
                        ],
                        'activo' => $registro['activo']
                    ));
                }
                mysqli_free_result($rs);
            }
            $con->closeConnection();
        } catch (\Throwable $th) {
            return $lista;
        }
        return $lista;
    }
}
