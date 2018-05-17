<?php if (isset($_POST["id"])) { 
    
    require_once('nucleo/nucleo.php');
    if (!usuarioLogueado()) return;
    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".clientes";
    $result = bdConsultarSQL($bd, "SELECT * FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND idClientes = " . $_POST["id"]);
    $row = mysqli_fetch_array($result);
    
?>
<form>
<span>Cliente: </span><input type="text" value="<?= $row["cliente"] ?>" />
<br />
<br />
<span>Sexo: </span><input type="text" value="<?= $row["sexo"] ?>" style="width: 100px" />
<br />
<br />
<span>Identificación: </span><input type="text" value="<?= $row["identificacion"] ?>" />
<br />
<br />
<span>Teléfono 1: </span><input type="text" value="<?= $row["telefono"] ?>" style="width: 200px" />
<br />
<br />
<span>Teléfono 2: </span><input type="text" value="<?= $row["telefono2"] ?>" style="width: 200px" />
<br />
<br />
<span>Correo electrónico 1: </span><input type="text" value="<?= $row["correoElectronico"] ?>" />
<br />
<br />
<span>Correo electrónico 2: </span><input type="text" value="<?= $row["correoElectronico2"] ?>" />
<br />
<br />
<p>Información adicional: </p>
<textarea><?= $row["descripcion"] ?></textarea>
</form>
<?php return; } ?>




<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = "Clientes";
    $gPaginaSubtitulo   = "Clientes que realizaron consultas en el CRM";
    
?>

<?php require_once('interfaz/interfaz-inicio.php'); ?>

<br />
<br />
Desde esta opción puede gestionar los clientes que realizaron consultas a través del CRM.
<br />
Tenga en cuenta que los cambios realizados se aplicarán a todas las consultas que estén vinculadas a cada cliente.
<br />
<br />


<div class="div-abm">
    <b>Acciones</b>: 
    <span>
        <a onclick="popupCliente.mostrar()">Nuevo</a> - 
        <a onclick="grd.deleteSelectedRow();">Eliminar</a>
    </span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Ver</b>: <span>
        <a onclick="grd.mostrarBajas(0);">Sólo activos</a> - 
        <a onclick="grd.mostrarBajas(1);">Mostrar eliminados</a>
    </span>
</div>


<div id="divClientes" class="grilla"></div>        
<script type="text/javascript">

    var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "clientes", "divClientes", <?= usuarioID() ?>);
    
    function ejecutarSQL(sql) {
    	$.ajax({
    		url: 'interfaz/grilla/json-sql.php',
    		type: 'POST',
    		dataType: "html",
    	   		data: {
    			sql : sql		
    		},
    		success: function (response) 
    		{ 
                grd.fetchGrid();
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
    		async: true
    	});
    }

    function nuevoCliente() {
        var clienteSQL = "INSERT INTO <?= $gDbDatabase ?>.clientes (cliente, sexo, identificacion, telefono, telefono2, correoElectronico, " +
        "correoElectronico2, descripcion, fechaAlta, idUsuariosAlta) VALUES ('" + $("#txtNombre").val() + "', '" + $("#txtSexo").val() + "', '" +
        $("#txtIdentificacion").val() + "', '" + $("#txtTelefono1").val() + "', '" + $("#txtTelefono2").val() + "', '" + $("#txtCorreo1").val() + "', '" +
        $("#txtCorreo2").val() + "', '" + $("#txtDescripcion").val() + "', NOW(), <?= usuarioID() ?>)";
    	$.ajax({
    		url: 'interfaz/grilla/json-sql.php',
    		type: 'POST',
    		dataType: "html",
    	   		data: {
    			sql : clienteSQL		
    		},
    		success: function (response) 
    		{ 
    		      if (response == "ok") {
                    popupCliente.ocultar(); 
                    grd.fetchGrid();
    		      }
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
    		async: true
    	});
    }
    
    
</script>



    
<div id="divCliente">
    <div class="popup-contenido" id="divClienteContenido">
        <form>
        <span>Nombre completo: </span><input type="text" id="txtNombre" value="" />
        <br />
        <br />
        <span>Sexo: </span><input type="text" id="txtSexo" value="Masculino" style="width: 100px" />
        <br />
        <br />
        <span>Identificación: </span><input type="text" id="txtIdentificacion" value="" />
        <br />
        <br />
        <span>Teléfono 1: </span><input type="text" id="txtTelefono1" value="" style="width: 200px" />
        <br />
        <br />
        <span>Teléfono 2: </span><input type="text" id="txtTelefono2" value="" style="width: 200px" />
        <br />
        <br />
        <span>Correo electrónico 1: </span><input type="text" id="txtCorreo1" placeholder="nombre@host.com" value="" />
        <br />
        <br />
        <span>Correo electrónico 2: </span><input type="text" id="txtCorreo2" placeholder="nombre@host.com" value="" />
        <br />
        <br />
        <p>Información adicional: </p>
        <textarea id="txtDescripcion"></textarea>
        </form>
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aceptar" onclick="nuevoCliente()" />
        <input type="button" class="boton-rojo" value="Cancelar" onclick="popupCliente.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupCliente = new crmPopup("divCliente");</script>





<?php require_once('interfaz/interfaz-fin.php'); ?>