<?php 
    $valores = $datos['valores'];
    $errores = $datos['errores'];
?>
<div class="container">
    <h1>Formulario de compra de abonos</h1>

    <!-- Formulario compra() -->
    <!-- Este se envía a index.php:
            se envia POST -- datos del formulario relleno (se los pasa a controlador indicado, a index.php no le interesa)
            se envia por GET -- parametros controller y action (los datos que realmente necesita index.php) 
    -->
    <form action="index.php?controller=abonos&action=compra" method="post">

        <div class="formulario">

            <!-- NOMBRE -->
            <div class="campo">
                <label>Nombre y apellidos:</label>
                <!-- 
                    1ª vez entrando, pero anteriormente se compró un abono -- autocompleta input con cookie 
                    No 1ª vez entrando y no hay cookie por compra anterior --- autocompleta valor del input con lo introducido 
                -->
                <input type="text" name="nombre"
                        value = "<?php if (isset($_COOKIE['nombre']) && !empty($_COOKIE['nombre']) 
                                        && empty($valores['nombre'])) {
                                            echo htmlspecialchars($_COOKIE['nombre']);
                                        } else echo htmlspecialchars($valores['nombre']); ?>"
                >
                <?php if (!empty($errores['nombre'])) { ?> <p class="error"> <?php echo $errores['nombre']; ?> </p> <?php } ?>
            </div>

            <!-- DNI -->
            <div class="campo">
                <label>DNI:</label>
                <input type="text" name="dni" 
                        value = "<?php if (isset($_COOKIE['dni']) && !empty($_COOKIE['dni']) 
                                        && empty($valores['dni'])) {
                                            echo htmlspecialchars($_COOKIE['dni']);
                                        } else echo htmlspecialchars($valores['dni']); ?>"                                    
                >
                <?php if (!empty($errores['dni'])){ ?> <p class="error"> <?php echo $errores['dni']; ?> </p> <?php } ?>
            </div>

            <!-- NACIMIENTO -->
            <div class="campo">
                <label>Fecha de nacimiento:</label>
                <!-- se le pasa al html el valor sin formato para que comprenda la cadena -->
                <input class="fecha" type="date" name="nacimiento"
                        value = "<?php if (isset($_COOKIE['nacimiento']) && !empty($_COOKIE['nacimiento']) 
                                        && empty($valores['nacimiento'])) {
                                            echo htmlspecialchars($_COOKIE['nacimiento']);
                                        } else echo htmlspecialchars($valores['nacimiento']); ?>"  
                >
                <?php if (!empty($errores['nacimiento'])){ ?> <p class="error"> <?php echo $errores['nacimiento']; ?> </p> <?php } ?>
            </div>

            <!-- TELÉFONO -->
            <div class="campo">
                <label>Teléfono:</label>
                <input type="text" name="telefono" 
                        value = "<?php if (isset($_COOKIE['telefono']) && !empty($_COOKIE['telefono']) 
                                        && empty($valores['telefono'])) {
                                            echo htmlspecialchars($_COOKIE['telefono']);
                                        } else echo htmlspecialchars($valores['telefono']); ?>" 
                >
                <?php if (!empty($errores['telefono'])){ ?> <p class="error"> <?php echo $errores['telefono']; ?> </p> <?php } ?>
            </div>

            <!-- CUENTA BANCARIA -->
            <div class="campo">
                <label>Cuenta bancaria:</label>
                <input type="text" name="cuentaBancaria" 
                        value = "<?php if (isset($_COOKIE['cuentaBancaria']) && !empty($_COOKIE['cuentaBancaria']) 
                                        && empty($valores['cuentaBancaria'])) {
                                            echo htmlspecialchars($_COOKIE['cuentaBancaria']);
                                        } else echo htmlspecialchars($valores['cuentaBancaria']); ?>" 
                >
                <?php if (!empty($errores['cuentaBancaria'])){ ?> <p class="error"> <?php echo $errores['cuentaBancaria']; ?> </p> <?php } ?>
            </div>

            <!-- TIPO DE ABONO -->
            <div class="campo">
                <label>Tipo de abono:</label></br>
                <select id="abono" name="abonoTipo">
                    <option value="" disabled hidden <?php if (empty($valores['abonoTipo'])){ echo 'selected'; }else echo ''; ?>>-</option>
                    <?php 
                    // por cada tipo de abono:
                    // se crea un <option>, con value la descripcion
                    // si fue seleccionado se muestra selected
                    // se imprime su descripción junto su precio
                    foreach($tiposAbono as $ta){ ?>
                    <option value="<?php echo $ta['descripcion'] ?>"
                            <?php if ($valores['abonoTipo'] === $ta['descripcion']){ echo 'selected'; } else echo ''; ?>>
                            <?php echo $ta['descripcion'] . '(' .  $ta['precio'] . '€)'; ?>
                    </option>
                    <?php } ?>
                </select>
                <?php if (!empty($errores['abonoTipo'])){ ?> <p class="error"> <?php echo $errores['abonoTipo']; ?> </p> <?php } ?>
            </div>

            <!-- TÉRMINOS -->
            <div class="terminos">
                <input type="checkbox" name="terminosCheck" 
                        <?php if (!empty($valores['terminosCheck'])) echo 'checked'; ?>>
                <label>Acepto los términos</label>
                <?php if (!empty($errores['terminosCheck'])){ ?> <p class="error"> <?php echo $errores['terminosCheck']; ?> </p> <?php } ?>
            </div>

            <!-- Si tras 5 intentos no se pudo generar un código de asiento... -->
            <?php if ($valores['asiento']===false){ ?> <p class="error"> <?php echo $errores['asiento']; ?> </p> <?php } ?>

            <!-- Botón para enviar datos desde POST (formulario) -->
            <button type="submit" name="botonComprar" value="ok">Comprar</button>
            </br>


            <!-- Manda a index.php con parámetros por GET -->
            <a href="index.php?controller=usuarios&action=login" class="btn">Iniciar sesión como administrador</a>

        </div>
    </form>
</div>