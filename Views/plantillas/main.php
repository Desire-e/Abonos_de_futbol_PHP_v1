<!-- PLANTILLAS -->
<!-- 
Para las estructuras repetitivas de HTML.
Así las Views solo definirán el contenido del body
-->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="Views/css/base.css">
    <?php
    // Carga CSS concreto de cada vista
    if (!empty($css)) {
        foreach ($css as $file) { 
            echo '<link rel="stylesheet" href="Views/css/' . $file . '">'; 
        }
    }
    ?>
</head>

<body>

<!-- Algunas Views no tienen un header. Desde el Controller que las carga, 
 se crea una variable $showHeader que indica si habrá o no header  -->
<?php if (!empty($showHeader)) { ?>
    <header>
        <img src="Views/img/almeria.png">
    </header>
<?php } ?>

<?php echo $content; ?>

</body>
</html>
