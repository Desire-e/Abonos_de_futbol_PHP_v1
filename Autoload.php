<?php
/** AUTOLOAD 
 * 
 *  Para cargar archivos de clases de manera automática sin require_once manual 
 * 
 * Flujo:
 * - Archivo intenta instanciar / usar una clase de otro archivo
 * - Al no estar cargado (con require_once) -- no existe
 * - Llama a spl_autoload_register() para intentar cargarlo
 **/


// Este solo funciona bien si el autoload está en la raíz del proyecto
spl_autoload_register(
    // Recibe el nombre de clase (incluyendo namespace) que se intenta usar y no se encontró cargada
    function ($class) {
        // dirname(__FILE__) devuelve la ruta absoluta del archivo donde está el autoload
        // reemplaza los \ del namespace por /
        require_once dirname(__FILE__) . "/" . str_replace("\\", "/", $class) . ".php";
    }
);