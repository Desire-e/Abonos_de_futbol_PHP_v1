<?php

namespace Models;

use Database\ConexionBD;

use PDO;
use PDOException;
use Exception;
use DateTime;

class UsuarioModel {
    
    private $conexion;    
    private $datos;    
    
    public function __construct() {
        $this->datos = array();
        
        // indica si datos de POST son validos
        $this->datos['valido'] = true; 
        // indica msjs de error de los datos de POST
        $this->datos['errores'] = array();
        $this->datos['valores'] = array();     
        $this->datos['valores']['username'] = '';
        $this->datos['valores']['password'] = '';
        // contiene datos obtenidos de consulta sql (select)
        // indica error de consulta (insert, updates...)
        // indica error en datos POST
        $this->datos['consulta'] = array();    

        // crea conexion a bd con singleton
        try {
            $this->conexion = ConexionBD::getInstancia()->getConexion();
        }
        catch(Exception $e) {
            $this->datos['consulta'] = 'Error';
        }
    }



    public function setData($data) {    
        if(isset($data['username'])) {
            $this->datos['valores']['username']=$data['username'];
        }

        if(isset($data['password'])) {
            $this->datos['valores']['password']=$data['password'];
        }
    }
    
    

    public function getData() {
        return $this->datos;
    }



    /* Autentifiación de usuario */
    // - Valida el POST (campos vacios)
    // - Valida que existe en BD
    // - Valida que passwords coinciden 
    public function autentificate() {
        
        // Valida datos de POST 
        $this->validar();
        
        if($this->datos['valido'] === true) {
            try {
                // Busca registro por username
                $consulta = $this->conexion->prepare("SELECT * FROM usuarios WHERE username = :u");
                $consulta->bindParam(':u', $this->datos['valores']['username']);
                $consulta->execute();
                
                $registro = $consulta->fetch();


                // Valida que existe registro en BD y password:
                // Si $registro existe en BD, y password coincide --- OK
                if($registro && password_verify($this->datos['valores']['password'], $registro['password'])) {
                    // $this->datos['valores']['password'] = $registro['password'];
                    // $this->datos['valores']['username'] = $registro['username'];
                    $this->datos['consulta'] = 'OK';                     
                }
                // Si $registro existe en BD, y password NO coincide --  Error
                // Si $registro NO existe en BD --- Error
                else {
                    $this->datos['valido'] = false ;
                    $this->datos['errores']['password'] = "Autentificación inválida. Usuario y/o contraseña no son correctos";
                    $this->datos['consulta'] = 'Error';
                }

            }
            catch(Exception $e){
                $this->datos['consulta'] = 'Error';
            }
            finally {
                    if (isset($consulta)) { $consulta->closeCursor(); }
                    $consulta = null;
            }
        }
        else {
            $this->datos['consulta'] = 'Error';
        }
    }



    /* Valida POST */
    private function validar() {
        $this->datos['valido'] = true;
        $this->datos['errores'] = array();
                
        if(empty($this->datos['valores']['username'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['username']='El campo no puede estar vacío.';
        }

        if(empty($this->datos['valores']['password'])) {
            $this->datos['valido'] = false;
            $this->datos['errores']['password']='El campo no puede estar vacío.';
        }
    }



    /* Inicia sesión */
    // crea y define variables de sesión
    public function login($datos){
        $_SESSION['logueado'] = 'SI';
        $_SESSION['username'] = $datos['username'];
    }
}