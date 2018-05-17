<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = "Perfiles";
    $gPaginaSubtitulo   = "Perfiles de usuarios del CRM";
    
?>

<?php require_once('interfaz/interfaz-inicio.php'); ?>

<br />
<br />
Desde esta opción puede gestionar los perfiles de usuarios que utilizan el CRM.
<br />
Tenga en cuenta que los cambios realizados se aplicarán inmediatamente a cada usuario registrado bajo el perfil indicado.
<br />
<br />


<div class="div-abm">
    <b>Acciones</b>: 
    <span>
        <a onclick="grd.addRow();">Nuevo</a> - 
        <a onclick="grd.deleteSelectedRow();">Eliminar</a>
    </span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Ver</b>: <span>
        <a onclick="grd.mostrarBajas(0);">Sólo activos</a> - 
        <a onclick="grd.mostrarBajas(1);">Mostrar eliminados</a>
    </span>
</div>


<div id="divPerfiles" class="grilla"></div>        
<script type="text/javascript">var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "perfiles", "divPerfiles", <?= usuarioID() ?>);</script>


<?php require_once('interfaz/interfaz-fin.php'); ?>