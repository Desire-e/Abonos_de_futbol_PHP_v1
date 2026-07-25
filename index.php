<?php
/******* 1º ENRUTADOR *******/ 
/**
 * El punto de entrada del sistema
 * - Decide qué controlador y qué acción ejecutar según la URL.
 * - Evita que el usuario acceda directamente a modelos o vistas.
 **/


session_start();


/* Carga utilidades globales */
require_once("Autoload.php"); // función para no hacer más require_once de las clases


/* 2. Petición de Cliente por GET */
// Recibe y lee params de GET (controller  y action)
$controller = filter_input(INPUT_GET, 'controller');
$action = filter_input(INPUT_GET, 'action');
$id = filter_input(INPUT_GET, 'id') ?? null;

switch($controller) {

    /* 3. Si el controlador existe: */
    case 'abonos':
        // require_once("Controllers/UsuariosController.php");
        
        // Con namespace + autoload (sin require_once):
        // Debo usar nombre de la clase con namespace (\\ para escapar el \ en string), 
        // para que al llamar a AbonosController desde method_exists() etc, autoload 
        // lo pueda cargar usando su namespace como ruta
        $controllerClass = "Controllers\\AbonosController";

        
        /* Valida que action sea un método existente en el controller */
        if(method_exists($controllerClass, $action)) {
            
            switch($action){
                case 'compra':
                    $controllerClass::compra();
                break;
                case 'ticket':
                    $controllerClass::ticket($id);
                break;
                case 'listado':
                    $controllerClass::listado();
                break;
                default:
                    // Si...
                    // - controller pasado existe 
                    // - action es un método que existe en controller, pero uno de los indicados
                    header("Status: 301 Moved Permanently");
                    header("Location: index.php?controller=abonos&action=compra");
                    exit();
                break;
            }
        } 
        // Si action no es metodo existente en controller
        else { 
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=compra");
            exit();
        }
    break;

    ////////////////////////////////////////////////////////////////////////////////////////

    case 'usuarios':
        
        // require_once("Controllers/UsuariosController.php");
        $controllerClass = "Controllers\\UsuariosController";
        
        if(method_exists($controllerClass, $action)) {            
            
            switch($action){
                case 'login':
                    $controllerClass::login();
                break;
                case 'logout':
                    $controllerClass::logout();
                break;
                default:
                    header("Status: 301 Moved Permanently");
                    header("Location: index.php?controller=usuarios&action=login");
                    exit();
                break;
            }
        } 
        else {
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=usuarios&action=login");
            exit();
        }
    break;

    ////////////////////////////////////////////////////////////////////////////////////////

    /* 3.2 Si el controlador indicado no existe: */
    default:
        header("Status: 301 Moved Permanently");
        header("Location: index.php?controller=abonos&action=compra");
        exit();
    break;
}