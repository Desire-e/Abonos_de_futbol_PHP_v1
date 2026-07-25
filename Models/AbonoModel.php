<?php
/******* 3º MODELO *******/ 
/** 
 * Toda la lógica de negocio y BD.
 * Se ejecuta este modelo concreto asociado al controlador concreto que index.php 
 * llamó según el parametro controller recibido por GET.
 * 
 * - El action del controlador le dicta qué métodos ejecutar 
 * - Si se necesita, obtiene datos de controlador (ids de GET / formularios de POST) 
 *   y los valida
 * - Consulta BD
 * - Envía resultados de BD y validación a controlador, controlador decidirá la 
 *   vista / redirección
*/


namespace Models;
use Database\ConexionBD;

// Al estar la clase PDO y otras dentro del namespace Models, PHP 
// interpreta que PDO pertenece a ese namespace (Models\PDO).
// Autoload.php intenta cargar Models/PDO.php
// Solución: indicar que se usa clase global (no se le aplicará namespace)
use PDO;
use PDOException;
use Exception;
use DateTime;


class AbonoModel {    

    private $datos;    // atributo de datos a consultar / insertar ... obtenidos por controlador
    private $conexion; // atributo de conexion a BD
    private $tiposAbono; // atributo que almacena los tipos de abono existentes en la bd


    /* Constructor */
    // 1. Inicializa $datos vacío por defecto
    // 2. Crea la conexión PDO
    public function __construct() {

        $this->datos = array();
        // indica si datos de POST son validos
        $this->datos['valido'] = true; 
        // indica msjs de error de los datos de POST
        $this->datos['errores'] = array();  

        // contiene datos de POST
        $this->datos['valores'] = array();  
        $this->datos['valores']['nombre'] = '';
        $this->datos['valores']['dni'] = '';
        $this->datos['valores']['nacimiento'] = ''; // almacena tal cual viene del html yyyy-mm-dd
        $this->datos['valores']['nacimientoFormato'] = ''; // almacena con formato concreto para imprimirlo dd/mm/yyyy
        $this->datos['valores']['telefono'] = '';
        $this->datos['valores']['cuentaBancaria'] = '';
        $this->datos['valores']['abonoTipo'] = '';
        $this->datos['valores']['terminosCheck'] ='';

        // contiene datos que serán autogenerados
        $this->datos['valores']['id'] = '';
        $this->datos['valores']['fecha'] = '';
        $this->datos['valores']['abonado'] = '';
        $this->datos['valores']['edad'] = '';
        $this->datos['valores']['asiento'] = '';
        $this->datos['valores']['precio'] = '';
        $this->datos['valores']['abonoTipoId'] = '';

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




    /* Devolver valor final de $datos al controlador */
    // llamado por compra() y ticket()
    public function getData() {
        return $this->datos;
    }


    /** Obtener los tipos de abono para validar campo 'abonoTipo' **/
    // llamado por compra() y ticket()
    public function setTiposAbono($tiposAbono){
        $this->tiposAbono = $tiposAbono;
    }


    /** Dar valores de POST a $this->datos['valores'] **/
    // llamado por compra()
    public function setData($data) {
        if(isset($data['nombre'])){
            $this->datos['valores']['nombre']=$data['nombre'];
        }

        if(isset($data['dni'])){
            $this->datos['valores']['dni']=$data['dni'];
        }

        if(isset($data['nacimiento'])){
            $this->datos['valores']['nacimiento']=$data['nacimiento'];
        }

        if(isset($data['telefono'])){
            $this->datos['valores']['telefono']=$data['telefono'];
        }

        if(isset($data['cuentaBancaria'])){
            $this->datos['valores']['cuentaBancaria']=$data['cuentaBancaria'];
        }

        if(isset($data['abonoTipo'])){
            $this->datos['valores']['abonoTipo']=$data['abonoTipo'];
        }

        if(isset($data['terminosCheck'])){
            $this->datos['valores']['terminosCheck']=$data['terminosCheck'];
        }
    }



    /** INSERT en tabla abonos **/
    // llamado por compra() 
    public function save() {
        // 1. Validar datos de POST
        $this->validar();

        // Si datos POST validos -- guardarlos en bd
        if($this->datos['valido'] === true) {

            // Valores autogenerados a los demás campos
            $this->autoSetDatos();

            try {
                $consulta = $this->conexion->prepare('INSERT INTO abonos VALUES(:id, :fecha, :abonado, :edad, :telefono, :cuenta_bancaria, :tipo, :asiento, :precio)');
                
                $consulta->bindParam(':id', $this->datos['valores']['id']);
                $consulta->bindParam(':fecha', $this->datos['valores']['fecha']);
                $consulta->bindParam(':abonado', $this->datos['valores']['abonado']);
                $consulta->bindParam(':edad', $this->datos['valores']['edad']);
                $consulta->bindParam(':telefono', $this->datos['valores']['telefono']);
                $consulta->bindParam(':cuenta_bancaria', $this->datos['valores']['cuentaBancaria']);
                $consulta->bindParam(':tipo', $this->datos['valores']['abonoTipoId']);
                $consulta->bindParam(':asiento', $this->datos['valores']['asiento']);
                $consulta->bindParam(':precio', $this->datos['valores']['precio']);
                
                $consulta->execute();

                $this->datos['consulta'] = 'OK';
            }
            catch(Exception $e) {
                $this->datos['consulta'] = 'Error';
            }
            finally {
                if (isset($consulta)) { $consulta->closeCursor(); }
                $consulta = null;
            }
        }

        // Si datos de POST invalidos
        else {
            $this->datos['consulta'] = 'Error';
        }
    }



    /* Busca registro en BD por id */
    // llamado por ticket(), que recibió $id que vino por GET tras la compra() 
    public function search($id) {
        try {
            // 1. Prepara sentencia, busca registro por id
            $consulta = $this->conexion->prepare("SELECT * FROM abonos WHERE id=:id");
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            $registro = $consulta->fetch();


            // 2. Comprueba si reistro existe
            // Si $registro existe -- obtener valores y darlos a controlador
            if($registro) {
                $this->datos['valores']['fecha'] = $registro['fecha'];
                $this->datos['valores']['edad'] = $registro['edad'];
                $this->datos['valores']['telefono'] = $registro['telefono'];
                $this->datos['valores']['asiento'] = $registro['asiento'];
                $this->datos['valores']['precio'] = $registro['precio'];
                $this->datos['valores']['abonoTipoId'] = $registro['tipo'];
    
                // Descripción del tipo de abono, asociado al registro del abonado 
                // (fk tipo en "abonos" es el id en "tipo_abonos")
                $abonoTipo = '';
                foreach($this->tiposAbono as $ta){
                    if ($ta['id'] === $registro['tipo']){
                        $this->datos['valores']['abonoTipo'] = $ta['descripcion'];
                    }
                }

                // Obtener nombre, apellidos y dni del registro 
                // (campo "abonado" con formato "Nombre Apellidos - Dni")
                $abonado = explode(" - ", $registro['abonado']);
                $this->datos['valores']['nombre'] = $abonado[0];
                $this->datos['valores']['dni'] = $abonado[1];

                $this->datos['consulta'] = 'OK';
            }
            // Si $registro no existe
            else {
                $this->datos['consulta'] = 'Error';
            }
        }
        catch(Exception $e) {
            $this->datos['consulta'] = 'Error';
        }
        finally {
            if (isset($consulta)) { $consulta->closeCursor(); }
            $consulta = null;
        }
    }


      
    /* Lista todos los registros de tabla abonos de la bd */
    // - junto los datos de su correspondiente tipo_abonos
    // - ordenados por código de asiento de la Z-A
    // llamado por listado() 
    public function listOrder() {
        try {
            $consulta = $this->conexion->prepare('SELECT a.id AS idAbonado, 
                                        a.fecha AS fechaCompra, 
                                        a.abonado AS abonado, 
                                        a.edad AS edad, 
                                        a.telefono AS telefono, 
                                        a.cuenta_bancaria AS cuentaBancaria, 
                                        a.asiento AS codAsiento,
                                        a.precio AS precioTotal,
                                        a.tipo AS idTipoAbono, ta.descripcion AS descTipoAbono, ta.precio AS precioTipoAbono
                                        FROM abonos a JOIN tipo_abonos ta ON a.tipo = ta.id
                                        ORDER BY codAsiento DESC');
            $consulta->execute();
            $registros = $consulta->fetchAll(); // obtiene todos los registros

            // Cuenta cuántos registros hay
            $numRegistros = count($registros);

            // Si devuelve array vacio (no hay abonos aun / exception) -- mensaje en vista
            if ($numRegistros <= 0){ $registros = []; }
            // Si devuelve registros (hay abonos) -- muestra en vista la tabla con los registros
            // **formato del array tras fetchAll():
            // $registros[0]['idAbonado'], $registros[0]['fechaCompra'], ... -- 1er registro, ...  


            return $registros;   
        }         
        catch(Exception $e) {
            return $registros = [];
        }
        finally {
            if (isset($consulta)) { $consulta->closeCursor(); }
            $consulta = null;
        }
    }





    /* Campos autogenerados, no los introduce el usuario */
    // llamado por save(), para obtener datos de POST(setData()) validados + datos autogenerados
    // llamado antes de validar() datos POST, pues algunos campos autogenerados requieren de datos de POST
    private function autoSetDatos(){
        // ----- id
        $UUID = $this->setUUID();
        $this->datos['valores']['id'] = $UUID;


        // ----- fecha
        // convierte a string, y da formato datetime 'YYYY-MM-DD HH:MM:SS', antes de insertar en la bd
        $fechaActual = (new DateTime()) -> format('Y-m-d H:i:s');
        $this->datos['valores']['fecha'] = $fechaActual;


        // ----- abonado
        // formato "Nombre Apellidos - Dni"
        $abonado = $this->setAbonado();
        $this->datos['valores']['abonado'] = $abonado;


        // ----- edad
        // Convierte el string a DateTime, y da formato 'd/m/Y'
        $fechaNac = DateTime::createFromFormat('d/m/Y', $this->datos['valores']['nacimientoFormato']);
        // Calcula diferencia de años completos entre fecha actual y la de nacimiento, da un DateInterval
        $edad = (new DateTime()) -> diff($fechaNac) -> y;
        $this->datos['valores']['edad'] = $edad;


        // ----- precio
        $precioTotal = $this->setPrecio($edad);
        $this->datos['valores']['precio'] = $precioTotal;


        // ----- asiento 
        $asiento = false;
        // Si devuelve false, significa que ya existe ese código de asiento en registros de la BD
        // Máximo 5 intentos, al 5º cancela compra y avisa que no hay asientos disponibles.
        for ($intento = 0; $intento < 5 && $asiento === false; $intento++) {
            $asiento = $this->setAsiento();
        }
        $this->datos['valores']['asiento'] = $asiento;
        
        // Si tras 5 intentos de asignar numero de asiento, no hay asientos disponibles --- redirige a formulario
        if (!$asiento){
            $this->datos['valido'] = false;
            $this->datos['errores']['asiento'] = 'No hay asientos disponibles.';
            return;
        } 
        // Si se asignó $asiento correctamente --- habrá insert 


        // ----- tipo 
        // Recorre los tipos de abono de la bd, hasta dar con el seleccionado por el usuario; recoge su id    
        $abonoTipoId = '';
        foreach($this->tiposAbono as $ta){
            if ($this->datos['valores']['abonoTipo'] === $ta['descripcion']){
                $abonoTipoId =  $ta['id'];
                break;
            }
        }
        $this->datos['valores']['abonoTipoId'] = $abonoTipoId;
    }


    
    
    /* Validar datos de POST */
    // llamado por save() 
    private function validar() {

        $this->datos['valido'] = true;
        $this->datos['errores'] = array();

        
        // ----- nombre
        if(empty($this->datos['valores']['nombre'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['nombre']='El campo no puede estar vacío.';
        }
        else if(!preg_match('/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/u', $this->datos['valores']['nombre'])){
            // Este patrón valida que toda la cadena contenga únicamente letras (incluyendo acentuadas), 
            // la ñ/Ñ y espacios. Sin números, símbolos ni caracteres especiales.
            $this->datos['valido'] = false;
            $this->datos['errores']['nombre'] = 'El campo solo admite letras.';
        }


        // ----- dni
        $dni = trim($this->datos['valores']['dni']);
        if(empty($dni)) {
            $this->datos['valido'] = false;
            $this->datos['errores']['dni']='El campo no puede estar vacío.';    
        } 
        else if(!empty($dni) && preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)==0) {
            // preg_match() devuelve 0 si cadena no coincide, false si el patrón es erroneo           
            $this->datos['valido'] = false;
            $this->datos['errores']['dni']='El DNI no es válido.';
        }
        else {
            $numero = intval(substr($dni, 0, 8));
            // intval(): Conversión del valor a int. Si no se hizo, da 0.
            $letra = strtoupper(substr($dni, -1));
            $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
            $letraEsperada = $letras[$numero % 23];
            if($letra !== $letraEsperada) {
                $this->datos['valido'] = false;
                $this->datos['errores']['dni'] = 'La letra del DNI no corresponde a los números.';
            }
        }


        // ----- nacimiento        
        if(empty($this->datos['valores']['nacimiento'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['nacimiento']='El campo no puede estar vacío.';
        } 
        else {
            // Pasar al formato dd/mm/yyyy (html da cadena con formato yyyy-mm-dd)...
            $nacimientoSubcad = explode('-', $this->datos['valores']['nacimiento']);  // array de subcadenas del año, mes, día.
            if (count($nacimientoSubcad) === 3) { 
                $this->datos['valores']['nacimientoFormato'] = $nacimientoSubcad[2] . '/' . $nacimientoSubcad[1] . '/' . $nacimientoSubcad[0];
                /* Ahora $this->datos['valores']['nacimientoFormato'] es una cadena "dd/mm/yyyy"
                   Y $this->datos['valores']['nacimiento'] contiene el valor original devuelto por el html */ 
            }
            if(!preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->datos['valores']['nacimientoFormato'])) {       
                $this->datos['valido'] = false;
                $this->datos['errores']['nacimiento'] = 'La fecha debe estar en formato dd/mm/yyyy.';
            }
            else if(!checkdate($nacimientoSubcad[1], $nacimientoSubcad[2], $nacimientoSubcad[0])) {     
                // checkdate(mes, dia, año): Para validar una fecha. Da true si la fecha es válida.
                $this->datos['valido'] = false;
                $this->datos['errores']['nacimiento'] = 'La fecha no es válida.';
            } else {
                // Parsear de string a DateTime, com formato d/m/Y
                $fechaNac = DateTime::createFromFormat('d/m/Y', $this->datos['valores']['nacimientoFormato']);                
                // Calcula la diferencia en años completos entre dos objetos DateTime
                $edad = (new DateTime()) -> diff($fechaNac) -> y;
                if($edad < 4 || $edad > 85) {
                    $this->datos['valido'] = false;
                    $this->datos['errores']['nacimiento'] = 'La edad debe ser mayor a 4 años y menor a 85 años.';
                }
            }
        }

        
        // ----- telefono
        if(empty($this->datos['valores']['telefono'])) {
            $this->datos['valido'] = false;
            $this->datos['errores']['telefono']='El campo no puede estar vacío.';
        }
        else if(!is_numeric($this->datos['valores']['telefono'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['telefono']='El campo solo admite números.';
        }
        else if(!preg_match('/^[679]\d{8}$/', $this->datos['valores']['telefono'])) {
            $this->datos['valido'] = false;
            $this->datos['errores']['telefono'] = 'El teléfono debe tener 9 dígitos y empezar por 6, 7 o 9.';
        }        


        // ----- cuenta bancaria
        if(empty($this->datos['valores']['cuentaBancaria'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['cuentaBancaria']='El campo no puede estar vacío.';
        } 
        else {
            $cuenta = strtoupper($this->datos['valores']['cuentaBancaria']);
            if(!preg_match('/^ES\d{2}([ -]?\d{4}){5}$/', $cuenta)) { 
                $this->datos['valido'] = false;
                $this->datos['errores']['cuentaBancaria'] = 'Formato incorrecto. Debe ser ES00-0000-0000-0000-0000-0000.';
            } else {
                $iban = str_replace([' ', '-'], '', $cuenta);  // quitar guiones y espacios
                /* str_replace(subcadBuscar, subcadReemp, cadena,contador): Busca todas las ocurrencias de una subcadena 
                   dentro de otra cadena y las reemplaza por otra subcadena 
                        contador (opcional): nº veces que se hizo el reemplazo. */ 
                $ibanReordenado = substr($iban, 4) . substr($iban, 0, 4); // mueve los 4 primeros al final
                $ibanNumerico = strtr($ibanReordenado, [
                    'A'=>'10','B'=>'11','C'=>'12','D'=>'13','E'=>'14','F'=>'15','G'=>'16',
                    'H'=>'17','I'=>'18','J'=>'19','K'=>'20','L'=>'21','M'=>'22','N'=>'23',
                    'O'=>'24','P'=>'25','Q'=>'26','R'=>'27','S'=>'28','T'=>'29','U'=>'30',
                    'V'=>'31','W'=>'32','X'=>'33','Y'=>'34','Z'=>'35'
                ]);
                if(bcmod($ibanNumerico, '97') != 1) {
                    // bcmod(): calcula el resto (módulo) de una división entre números grandes
                    $this->datos['valido'] = false;
                    $this->datos['errores']['cuentaBancaria'] = 'El IBAN no es válido.';
                }
            }
        }
        
        
        // ----- tipo de abono
        if(empty($this->datos['valores']['abonoTipo'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['abonoTipo']='El campo no puede estar vacío.';
        } 
        else {
            // los valores de columna 'descripcion' en tabla tipos_abono    
            $tiposAbonoDesc = array_column($this->tiposAbono, 'descripcion'); 
            // array_column($array, indice): en el array que almacena arrays, obtiene de cada array interno
            // el valor asociado al "indice" común. Genera un array indexado nuevo que almacena esos valores  
            // Estructura de $tiposAbono:
            // Array (
            //     [0] => Tribuna
            //     [1] => Fondo ...
            // )
            
            if(!in_array($this->datos['valores']['abonoTipo'], $tiposAbonoDesc)) {  // validar opciones 
                // si el valor de abonoTipo no está en el array $tiposAbonoDesc     
                $this->datos['valido'] = false;
                $this->datos['errores']['abonoTipo'] = 'Tipo de abono no válido.';
            }
        }


        // ----- términos aceptados (check)
        if(empty($this->datos['valores']['terminosCheck'])){
            $this->datos['valido'] = false;
            $this->datos['errores']['terminosCheck']='Debe aceptar los términos y condiciones.';
            // Los checkboxes envían su name solo si están marcados, por eso solo se valida con empty()
        }
    }




    /** ------------- FUNCIONES AUXILIARES: campos autoset ------------- **/

    /* Campo 'id' de abonos es autogenerado */
    private function setUUID(){
        // Intentar obtener 16 bytes crípticamente seguros
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $data = openssl_random_pseudo_bytes(16);
            if ($data === false) {
                // Fallback a generador menos ideal pero funcional
                $data = '';
                for ($i = 0; $i < 16; $i++) {
                    $data .= chr(mt_rand(0, 255));
                }
            }
        } else {
            // Último recurso: mt_rand()
            $data = '';
            for ($i = 0; $i < 16; $i++) {
                $data .= chr(mt_rand(0, 255));
            }
        }

        // Ajustar bits para versión 4 y variante RFC 4122
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante RFC 4122

        $hex = bin2hex($data);

        // Formatear 8-4-4-4-12
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }


    /* Campo 'abonado' con formato "Nombre Apellidos - DNI" */
    private function setAbonado(){
        $abonado = '';
        $abonadoNombre = '';
        $abonadoApellidos = '';
        
        $abonadoNombreApellidos = explode(" ", $this->datos['valores']['nombre']);
        $abonadoNombre = $abonadoNombreApellidos[0];
        for ($i = 1; $i < count($abonadoNombreApellidos); $i++){
            $abonadoApellidos .= $abonadoNombreApellidos[$i].' ';
        }
        $abonadoApellidos = trim($abonadoApellidos);
        $abonadoDni = $this->datos['valores']['dni'];
        
        return $abonado = $abonadoNombre . ' ' . $abonadoApellidos . ' - ' . $abonadoDni;
    }


    /* Calcula 'precio' total, con rebajas si las hay */
    function setPrecio($edad) {

        $rebaja = 0;
        $importeTotal = 0;
        $abonoTipo = $this->datos['valores']['abonoTipo'];  // tipo de abono seleccionado
        $precioAbono = 0;
        
        // Buscar el precio base del tipo de abono seleccionado
        foreach ($this->tiposAbono as $ta) {    // en cada iteración accede a subarray
            if ($ta['descripcion'] === $abonoTipo) {    // del cual comprueba el valor de su clave 'descripcion'
                $precioAbono = $ta['precio'];           // del cual recoge el valor de su clave 'precio'
                break;      // encontrado, sale del bucle
            }
        }

        // Calcula rebaja segun edad
        if($edad < 12) $rebaja = 80;
        if ($edad > 65) $rebaja = (50*$precioAbono)/100;

        // Aplica rebaja al precio base del abono
        $importeTotal = $precioAbono - $rebaja;
        return $importeTotal;
    }


    /* Genera campo de código de 'asiento' */
    private function setAsiento(){

        // ----- Primera letra del tipo de abono:
        $abonosLetra = array();
        // array de valores 'descripcion', tabla 'tipos_abono'
        $tiposAbonoDesc = array_column($this->tiposAbono, 'descripcion'); 
        foreach($tiposAbonoDesc as $tad) {
            // Genera array de letras de cada abono ($abonosLetra['Tribuna']='T', ...)
            $abonosLetra[$tad] = strtoupper(substr($tad, 0, 1));
        }
        $abonoTipo = $this->datos['valores']['abonoTipo']; 
        $letra = $abonosLetra[$abonoTipo] ?? '';
        

        // ----- Bloque de asientos (1-5 inclusives):
        // rand(min, max): Genera int aleatorio entre un int minimo y máximo inclusives.
        $bloque = 'B' . rand(1,5);
        


        // ----- Fila dentro del bloque (0-29 inclusives):
        // Los números de fila menores de 10 serán rellenados con 0s a la izquierda.
        $filaNum = rand(0,29);
        if ($filaNum < 10){
            $filaNumCadena = "0" . "$filaNum";
        } else 
            $filaNumCadena = "$filaNum";
        $fila = 'F' . $filaNumCadena;


        // ----- Asiento dentro de la fila (0-199 inclusives):
        $maxAsientosPorFila = 140 + ($filaNum*2);
        $asientoNum = rand(0, $maxAsientosPorFila);
        // Los números de asiento menores de 100 serán rellenados con 0s a la izquierda.
        if ($asientoNum < 10){       // por ejemplo 009
            $asientoNumCadena = "00" . "$asientoNum";
        } else if ($asientoNum < 100){ // por ejemplo 099
            $asientoNumCadena = "0" . "$asientoNum";
        } else                          // en cualquier otro caso, por ejemplo 100 
            $asientoNumCadena = "$asientoNum";
        $asiento = 'A' . $asientoNumCadena;


        $codigoAsiento = $letra . $bloque . '/' . $fila . "-" . $asiento;

        
        // Comprobar si existe el código de asiento en la BD:
        // Hace consulta de todos
        $listadoAbonos = $this->listado();
        foreach ($listadoAbonos as $a){
            if ($a['asiento'] === $codigoAsiento) {
                return false;
            }
        }
        // Si no existe, devuelve código generado:
        return $codigoAsiento;
    }


    /* Obtener todos los registros de la tabla de la BD */
    /* auxiliar para setAsiento() */
    private function listado() {
        try{
            $consulta = $this->conexion->prepare("SELECT * FROM abonos");
            $consulta->execute();

            /* $datos['consulta'] se vuelve array asociativo:
                $consulta[0] = ['id'=> ..., 'nombre'=> ...,  ] -- primer registro */
            return $consulta->fetchAll();
            
        }
        catch(Exception $e){
            $this->datos['consulta'] = 'Error';
        } 
        finally {
            if (isset($consulta)) { $consulta->closeCursor(); }
            $consulta = null;
        }
    }

}