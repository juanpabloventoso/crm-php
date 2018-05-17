<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = "Estados";
    $gPaginaSubtitulo   = "Estados de las consultas del CRM";
    
?>

<?php require_once('interfaz/interfaz-inicio.php'); ?>

<br />
<br />
Desde esta opción puede gestionar los estados que pueden tomar las consultas registradas a través del CRM.
<br />
Tenga en cuenta que los cambios realizados se aplicarán a todas las consultas que utilicen cada estado.
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

<div id="divEstados" class="grilla"></div>        
<script type="text/javascript">var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "estados", "divEstados", <?= usuarioID() ?>);</script>


<?php require_once('interfaz/interfaz-fin.php'); ?>