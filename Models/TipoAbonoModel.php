<?php

namespace Models;

use Database\ConexionBD;

use PDO;
use PDOException;
use Exception;
use DateTime;

class TipoAbonoModel {

    private $conexion;
    private $tiposAbono;


    public function __construct() {
        $this->tiposAbono = array();        

        try {
            $this->conexion = ConexionBD::getInstancia()->getConexion();
        }
        catch(Exception $e) {
            $this->datos['consulta'] = 'Error';
        }
    }



    /* Devolver valor final de $tiposAbono a controlador */
    public function getData() {
        return $this->tiposAbono;
    }


            
    /* Obtener todos los registros de la tabla de la bd */
    public function listado() {
        try{
            $consulta = $this->conexion->prepare("SELECT * FROM tipo_abonos");
            $consulta->execute();

            $this->tiposAbono = $consulta->fetchAll();
        }
        catch(Exception $e){
            $this->tiposAbono = 'Error';
        }
        finally {
            if (isset($consulta)) { $consulta->closeCursor(); }
            $consulta = null;
        }
    } 
    
}