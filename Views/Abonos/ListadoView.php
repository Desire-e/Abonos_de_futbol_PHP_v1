<div class="container-listado">
    <h1>ABONOS REGISTRADOS</h1>
    <table>
        
        <!-- CAMPOS DEL REGISTRO -->
        <?php 
        if (!$registros || empty($registros)) { ?>
        <p>No se encontraron abonos registrados.<p>

        <?php } else { ?>
        <!-- tr: table row ; se va rellenando tabla en direccion hacia abajo  -->
            <!-- th: table header ; se va rellenando tabla en direccion hacia la derecha -->
            <!-- td: table data ; se va rellenando tabla en direccion hacia la derecha --> 

        <!-- CABECERAS -->
        <tr>
            <th>Tipo de abono</th>
            <th>Código de asiento</th>
            <!--
            **** Icono con forma de teléfono. Cuando se pase el cursor por encima del el icono, deberá mostrar en un "title" el teléfono de contacto.
            **** Icono con forma de banco. Cuando se pase el cursor por encima del el icono, deberá mostrar en un "title" el número de cuenta bancaria. -->
            <th>Datos del abonado</th> 
            <th>Datos del abonado especial</th> 
            <th>Importe total</th> 
        </tr>
        <?php
            foreach($registros as $ra){ 
                $id = $ra['idAbonado'];
                $fechaCompra = $ra['fechaCompra']; 
                $abonadoNomApeDni = $ra['abonado']; 
                $edad = $ra['edad'];
                $telefono = $ra['telefono'];
                $cuentaBancaria = $ra['cuentaBancaria']; 
                $idTipoAbono = $ra['idTipoAbono'];
                $codAsiento = $ra['codAsiento'];
                $precioTotal = $ra['precioTotal'];
                $descTipoAbono = $ra['descTipoAbono'];
                $precioTipoAbono = $ra['precioTipoAbono'];
        ?>
        <tr> 
            <td>
                <img class ="medalla" src= "Views/img/<?php 
                    if ($descTipoAbono === 'Tribuna'){
                        echo 'medalla-oro.png';
                    } elseif ($descTipoAbono === 'Preferencia'){
                        echo 'medalla-plata.png';
                    } elseif ($descTipoAbono === 'Fondo'){
                        echo 'medalla-bronce.png';
                    }
                ?>"/>
            </td>
            <td><?php echo htmlspecialchars($codAsiento) ?></td>
            <td>
                <p><?php echo htmlspecialchars($abonadoNomApeDni) ?></p>
                <img src="Views/img/phone-call.png" class="icono" 
                title="<?php echo htmlspecialchars($telefono) ?>"/>
                <img src="Views/img/bank-building.png" class="icono" 
                title="<?php echo htmlspecialchars($cuentaBancaria) ?>"/>
            </td>
            <td>
                <?php if($edad < 12){
                        echo 'Menor de 12 años';
                    } else if($edad > 65){
                        echo 'Jubilado';
                    } else echo 'Sin abono especial';
                ?>
            </td>
            <td><?php echo $precioTotal ?></td>
        </tr>
        
        <?php } } ?>

    </table>
    
    <a href="index.php?controller=usuarios&action=logout" class="btn">Cerrar sesión</a>
</div>