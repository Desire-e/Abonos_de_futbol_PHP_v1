<?php 
    $valores = $datos['valores'];
    $errores = $datos['errores'];
?>

<div class="container">
    <h1>LOGIN</h1>

    <form action="index.php?controller=usuarios&action=login" method="post">
        <div class="formulario">
            <div class="campo">
                <label>Nombre de usuario:</label>
                <input type="text" name="username" value = "<?php echo htmlspecialchars($valores['username']); ?>"/>
                <?php if (!empty($errores['username'])){ ?> <p class="error"><?php echo $errores['username']; ?></p> <?php } ?>
            </div>

            <div class="campo">
                <label>Contraseña:</label>
                <input type="text" name="password" value = "<?php echo htmlspecialchars($valores['password']); ?>"/>
                <?php if (!empty($errores['password'])){ ?> <p class="error"><?php echo $errores['password']; ?></p> <?php } ?>
            </div>
            
            <button type="submit" name="login" value="ok">Acceder</button>
            </br>
        </div>
    </form>
    
    <a href="index.php?controller=abonos&action=compra" class="btn">Volver</a>
</div>