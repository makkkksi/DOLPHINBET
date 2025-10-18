<?php

class Billetera
{
    public function __construct() {}

    public function getAll()
    {
        $lista = [];
        try {
            $con = new Conexion();
            $query = "SELECT id, usuario_id, saldo, activo FROM billetera;";

            $rs = mysqli_query($con->getConnection(), $query);
            if ($rs) {
                while ($registro = mysqli_fetch_assoc($rs)) {
                    $registro['activo'] = $registro['activo'] == 1 ? true : false;

                    //debemos trabajar con el objeto
                    array_push($lista, array(
                        'id' => intval($registro['id']),
                        'usuario' => [
                            'id' => intval($registro['usuario_id'])
                        ],
                        'saldo' => intval($registro['saldo']),
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
