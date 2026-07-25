<div class="container">
    <p>¡Venta realizada con éxito!</p>
    <h2>Ticket de Compra</h2>
    <div class="contenido">
        <!-- fecha de compra -->
        <p><strong>Fecha de compra: </strong> <?php echo htmlspecialchars($registro['valores']['fecha']) ?> </p>
        
        <!-- datos del abonado -->
        <p><strong>Nombre: </strong> <?php echo htmlspecialchars($registro['valores']['nombre']) ?> </p>
        <p><strong>DNI: </strong> <?php echo htmlspecialchars($registro['valores']['dni']) ?></p>
        <p><strong>Teléfono: </strong> <?php echo htmlspecialchars($registro['valores']['telefono']) ?> </p>
        
        <!-- tipo de abono -->
        <p><strong>Tipo de abono: </strong> <?php echo htmlspecialchars($registro['valores']['abonoTipo']) ?> </p>
        
        <!-- codigo asiento -->
        <p><strong>Código de asiento: </strong> <?php echo htmlspecialchars($registro['valores']['asiento']) ?> </p>

        <!-- precio total + indicar si tiene tarifa especial -->
        <p><strong>IMPORTE: </strong> <?php echo htmlspecialchars($registro['valores']['precio']) ?> </p>
        <?php
        if($registro['valores']['edad'] < 12 ){ ?>
        <p>* Tarifa especial <?php echo 'Niños/as menores de 12 años: Rebaja de 80€.' ?> </p>
        <?php } ?>
        <?php
        if($registro['valores']['edad'] > 65 ){ ?>
        <p>* Tarifa especial <?php echo 'Jubilados y mayores de 65 años: Rebaja del 50%.' ?> </p>
        <?php } ?>

        <a href="index.php?controller=abonos&action=compra" class="btn">Volver</a>

    </div>
</div>