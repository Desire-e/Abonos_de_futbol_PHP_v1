<?php
/******* 2º CONTROLADOR *******/ 
/**
 * Coordina Modelo y Vista
 * - Clase static, el controlador NO mantiene un estado, solo realiza las acciones 
 * - Recibe la petición desde index.php (parametro action del GET = metodo del controller a ejecutar)
 * - Llama al modelo
 * - Decide qué vista mostrar, redirige si es necesario
 **/


/* Carga modelos automático -- Uso de namespace + Autoload.php */
// Esta clase será conocida como Controller\AbonosController
namespace Controllers; // nombre de la carpeta

// Indica en este archivo que AbonoModel hace referencia a la clase con namespace Model\AbonoModel
// Si no se hace: Se aplica el namespace actual "Controllers" (Controllers\AbonoModel,  Autoload.php no encontrará ruta)
use Models\AbonoModel;  
use Models\TipoAbonoModel;


/* Define clase Controladora -- métodos corresponden a los action recibidos por parametros GET en index.php */
class AbonosController {

    /* Obtiene los tipos de abono */
    // Para:
    // - mostrar ticket()
    // - seleccionarlo en compra() de abonos
    // - mostrar listado() para admins logeados
    private static $tiposAbono = null;

    private static function tiposAbono(){
        if (self::$tiposAbono === null) {
            $modeloTiposAbono = new TipoAbonoModel();
            $modeloTiposAbono->listado();

            self::$tiposAbono = $modeloTiposAbono->getData();
        }
    }



    /* Crea las cookies, con los datos de la última compra (si se hacen varias, se va sobreescribiendo) */
    private static function creaCookie($nombreCookie, $valorCookie){
        $expiracion = time() + 60 * 86400;  // 2 meses
        $ruta = '/';                        // cookie válida en toda la web del dominio
        $dominio = $_SERVER['HTTP_HOST'];   // el dominio actual
        $seguridad = false;                 // no requiere HTTPS (no tengo certificado ssl/tsl)
        $solohttp = true;                   // a cookie no se puede acceder desde JS, solo desde servidor

        setcookie($nombreCookie, $valorCookie, $expiracion, $ruta, $dominio, $seguridad, $solohttp);
        // Condición de ejecución: Debe llamarse antes de la etiqueta <html>, de cualquier echo, o de 
        // cualquier otro código que genere salida, ya que establece una cabecera HTTP.
    }



    /* Muestra formulario de compra de abonos */
    static function compra() {
        // 1. Comprueba logeado        
        // Si logeado -- redirige a listado()
        if(isset($_SESSION['logueado']) && !empty($_SESSION['logueado']) && $_SESSION['logueado']=='SI'){
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=listado");
            exit;
        } // Si no logeado -- continúa
    
        
        // 2. Obtiene tipos de abono
        self::tiposAbono();
        $tiposAbono = self::$tiposAbono;


        // 3. Crea instancia modelo
        $modelo = new AbonoModel(); 
        
        // 4. Comprueba POST
        // Si viene POST vacío (1ª vez entrando a formulario) -- carga formulario vacío
        if(!filter_input_array(INPUT_POST)){
            $modelo->setTiposAbono($tiposAbono);    // modelo obtiene tipos de abono para mostrarlos en el <select>
            $datos = $modelo->getData();  // modelo da datos, inicializados vacíos por defecto
        }

        // Si viene POST con info
        else {
            $modelo->setTiposAbono($tiposAbono);
            $modelo->setData(filter_input_array(INPUT_POST)); // modelo reasigna con datos de POST            
            $modelo->save();    // guarda en BD si son válidos, si no no

            $datos = $modelo->getData();    // devuelve datos y resultado del insert

            // Insert exitoso / errores en formulario -- redirige a ticket
            if($datos['consulta'] == 'OK') {
                // recoge id recién insertado para pasarselo a ticket() por GET
                $id = $datos['valores']['id'];

                // crea cookies de los campos rellenos en la ultima compra
                self::creaCookie("nombre", $datos['valores']['nombre']);
                self::creaCookie("dni", $datos['valores']['dni']);
                self::creaCookie("nacimiento", $datos['valores']['nacimiento']);
                self::creaCookie("telefono", $datos['valores']['telefono']);
                self::creaCookie("cuentaBancaria", $datos['valores']['cuentaBancaria']);

                header("Status: 301 Moved Permanently");
                header("Location: index.php?controller=abonos&action=ticket&id=$id");
                exit();
            }
            // Si resultado de insert no exitoso / errores en formulario -- carga formulario con $datos['errores']
        }
        

        // 5. Carga la vista de compra (usando plantilla + vista concreta):
        // Variables para la plantilla
        $title = 'Formulario de compra'; // define el <title> del HTML        
        $css = ['formulario.css'];  // define los archivos css incluidos en el HTML
        $showHeader = true;     // define si el HTML de esta vista contiene un <header>

        // inicia buffer
        ob_start();
        // buffer almacena todo el contenido concreto de la view a insertar en la plantilla
        require_once 'Views/Abonos/CompraView.php';
        // cierra buffer y lo limpia
        $content = ob_get_clean();
        
        // carga la plantilla, que tiene acceso a variable $content
        // la plantilla carga lo que contiene $content (el contenido concreto de la view)
        require_once 'Views/plantillas/main.php'; 
    }

       


    /* Mostrar ticket() tras la compra() */
    // recibe el id recién autogenerado en compra(), por GET
    static function ticket() {
        if(isset($_SESSION['logueado']) && !empty($_SESSION['logueado']) && $_SESSION['logueado']=='SI'){
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=listado");
            exit;
        }


        self::tiposAbono();
        $tiposAbono = self::$tiposAbono;

        // Recibe id por GET tras la compra, para que AbonoModel busque el registro recién creado
        $id = $_GET['id'] ?? null;
        // Si no, recarga formulario compra
        if (!$id) { 
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=compra");
            exit;
        }

        
        self::tiposAbono();
        $tiposAbono = self::$tiposAbono;

        $modelo = new AbonoModel();
        $modelo->setTiposAbono($tiposAbono);
        $modelo->search($id); // Dice a modelo que haga select del registro
        $registro = $modelo->getData(); // Recupera los datos del registro de la bd

        if($registro['consulta'] == 'Error') {
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=compra");
            exit;
        }
     

        $title = "Ticket de compra";        
        $css = ['ticket.css'];  
        $showHeader = true;

        ob_start();
        require_once 'Views/Abonos/TicketView.php';
        $content = ob_get_clean();
        require_once 'Views/plantillas/main.php'; 
    }
    



    /* Mostrar listado() de tabla abono a los admin logeados */
    static function listado() {
        if(!isset($_SESSION['logueado']) || empty($_SESSION['logueado']) || $_SESSION['logueado']=='NO'){
            // Carga la vista de alerta
            $title = "Protegido";       
            $css = ['alertaAcceso.css'];  
            $showHeader = false;

            ob_start();
            require_once 'Views/Abonos/AlertaAccesoView.php';
            $content = ob_get_clean();
            require_once 'Views/plantillas/main.php'; 
            
            exit;
        }


        self::tiposAbono();
        $tiposAbono = self::$tiposAbono;

        $modelo = new AbonoModel();
        $modelo->setTiposAbono($tiposAbono);
        $registros = $modelo->listOrder();       
        // - se obtiene listado de abonos + su tipo_abono, ordenado por asiento Z-A
        //   $registros[0]['idAbonado'], $registros[0]['fechaCompra'], ... -- 1er registro, ...
        // - si no hay registros / hay error de consulta, se obtiene $registros = []
        

        // Haya o no registros de abonos:
        // Carga la vista del listado
        $title = "Listado de Abonos";        
        $css = ['listado.css'];  
        $showHeader = false;

        ob_start();
        require_once("Views/Abonos/ListadoView.php");
        $content = ob_get_clean();
        require_once 'Views/plantillas/main.php'; 
    }


}