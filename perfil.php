<?php require_once('nucleo/nucleo.php'); ?>

<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = usuarioNombreFormal();
    $gPaginaSubtitulo   = "Perfil de usuario de " . usuarioNombre();
    
?>

<?php require_once('interfaz/interfaz-inicio.php'); ?>

<br />

<img src="<?= usuarioImagen() ?>" alt="<?= usuarioNombre() ?>" />


<?php require_once('interfaz/interfaz-fin.php'); ?>