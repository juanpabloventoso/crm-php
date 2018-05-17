<?php require_once('head.php'); ?>

    <div id="divCabecera">
        <div class="div-cabecera-izq">
            <div class="div-logo">
                <a href="<?= $gAppUrlBase ?>/"><img src="<?= $gAppUrlBase ?>/imagenes/logo/logo-cabecera.png" alt="Logo" /></a>
            </div>
        </div>
        <div class="div-cabecera-der">
            <div class="div-notificaciones">
            </div>
        
            <div class="div-usuario">
       			<a href="<?= $gAppUrlBase ?>/perfil.php"><img src="<?= usuarioImagen() ?>" alt="<?= usuarioNombre() ?>" /></a>
                <li>
                    <a href="<?= $gAppUrlBase ?>/perfil.php" class="a-usuario"><?= usuarioNombre() ?></a>
                    <ul>
                        <li><a href="<?= $gAppUrlBase ?>/perfil.php">Mi perfil</a></li>
                        <li><a href="<?= $gAppUrlBase ?>/login.php?cerrarSesion=1">Cerrar sesión</a></li>
                    </ul>
                </li>
            </div>
            
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>