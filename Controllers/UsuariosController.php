<?php

namespace Controllers;
use Models\UsuarioModel;  

class UsuariosController {        

    /* Formulario de inicio de sesion */
    static function login() {
        if(isset($_SESSION['logueado']) && !empty($_SESSION['logueado']) && $_SESSION['logueado']=='SI'){
            header("Status: 301 Moved Permanently");
            header("Location: index.php?controller=abonos&action=listado");
            exit;
        }

        $modelo = new UsuarioModel();

        // Si POST vacio -- muestra vista
        if(!filter_input_array(INPUT_POST)){
            $datos = $modelo->getData();
        } 
        // Si POST con info
        else{
            $modelo->setData(filter_input_array(INPUT_POST));
            $modelo->autentificate();       // Valida POST y autentificacion en BD
            $datos = $modelo->getData();
            
            // Si autentifiación y POST válida -- crea variables de sesion y redirige a listado()
            if($datos['consulta'] == 'OK'){
                $modelo->login($datos);
                
                header("Status: 301 Moved Permanently");
                header("Location: index.php?controller=abonos&action=listado");
                exit;
            }
            // Si POST / autentificación con errorres -- muestra vista con errores
        }
        
        $title = "Inicio de sesion";
        $css = ['formulario.css'];  
        $showHeader = true;

        ob_start();
        require_once 'Views/Usuarios/LoginView.php';
        $content = ob_get_clean();
        require_once 'Views/plantillas/main.php'; 
    }    



    /** Botón de cerrar sesión **/
    // No comprueba login. Haya sesión activa o no, se destruye
    // No necesita comprobar datos por parte de modelo    
    static function logout() {
        // cierra la sesión
        session_unset();
        session_destroy();

        // redirige a vista de login()
        header("Status: 301 Moved Permanently");
        header("Location: index.php?controller=usuarios&action=login");
        exit;
    }
    

}