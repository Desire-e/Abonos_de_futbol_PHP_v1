<?php
/** SINGLETON 
 * 
 * Se crea instancia unica que contiene un atributo de conexión (la conexion a bd con PDO())
 **/


namespace Database;
use PDO;
use PDOException;
use Exception;

require_once("config.php"); // credenciales de conexion a bd

class ConexionBD {

    /* Instancia de ConexionBD: */
    // - static: pertenece a la clase, no a un objeto
    // - guardará el único objeto ConexionBD existente
    private static $instancia = null;


    /* Atributo de $instancia, contiene conexion a la BD */
    private $conexion = null;
    

    /* Constructor private: */
    // - realiza la conexión automáticamente al crearse la $instancia
    // - $instancia solo se puede crear con getInstancia()
    private function __construct() {
        try {
            $this->conexion = new PDO('mysql:host=' . BD_HOST . ';dbname=' . BD_NAME, BD_USER, BD_PASSWORD);
        }
        catch(Exception $e){ }
    }
    

    /* Getter de la instancia */
    // la crea (1ª entrada) / la devuelve
    public static function getInstancia() {
        if (self::$instancia == null) {
            self::$instancia = new ConexionBD();
        } 
        return self::$instancia;
    }

    /** Getter del atributo conexion que contiene la instancia**/
    public function getConexion() { 
        return $this->conexion;
    }


}

