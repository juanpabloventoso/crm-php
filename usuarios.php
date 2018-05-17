<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = "Usuarios";
    $gPaginaSubtitulo   = "Usuarios que trabajan con el CRM";
    
?>

<?php
 
    require_once('interfaz/interfaz-inicio.php'); 
    $bd = bdConectar();

?>

<br />
<br />
Desde esta opción puede gestionar los usuarios que pueden iniciar sesión en el CRM.
<br />
Tenga en cuenta que los cambios realizados se aplicarán inmediatamente a cada cuenta de usuario.
<br />
<br />


<div class="div-abm">
    <b>Acciones</b>: 
    <span>
        <a onclick="popupUsuario.mostrar();">Nuevo</a> - 
        <a onclick="grd.deleteSelectedRow();">Eliminar</a>
    </span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Ver</b>: <span>
        <a onclick="grd.mostrarBajas(0);">Sólo activos</a> - 
        <a onclick="grd.mostrarBajas(1);">Mostrar eliminados</a>
    </span>
</div>


<div id="divUsuarios" class="grilla"></div>
        
<script type="text/javascript" src="<?= $gAppUrlBase ?>/js/crypto-md5.js"></script>
<script type="text/javascript">

var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "usuarios", "divUsuarios", <?= usuarioID() ?>);

   
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

    function nuevoUsuario() {
        var usuario = $("#txtNombre").val().substr(0, 1) + $("#txtApellido").val().substr(0, 49);
        usuario = usuario.replace(/\s+/g, '');
        usuario = usuario.replace(/\'+/g, '');
        usuario = usuario.replace(/-+/g, ''); 
        usuario = usuario.toLowerCase();
        usuario = usuario.replace(new RegExp("\\s", 'g'),"");
        usuario = usuario.replace(new RegExp("[àáâãäå]", 'g'),"a");
        usuario = usuario.replace(new RegExp("æ", 'g'),"ae");
        usuario = usuario.replace(new RegExp("ç", 'g'),"c");
        usuario = usuario.replace(new RegExp("[èéêë]", 'g'),"e");
        usuario = usuario.replace(new RegExp("[ìíîï]", 'g'),"i");
        usuario = usuario.replace(new RegExp("ñ", 'g'),"n");                            
        usuario = usuario.replace(new RegExp("[òóôõö]", 'g'),"o");
        usuario = usuario.replace(new RegExp("œ", 'g'),"oe");
        usuario = usuario.replace(new RegExp("[ùúûü]", 'g'),"u");
        usuario = usuario.replace(new RegExp("[ýÿ]", 'g'),"y");
        usuario = usuario.replace(new RegExp("\\W", 'g'),"");   
        var password = Math.random().toString(36).slice(-8);
        var passhash = CryptoJS.MD5(password).toString();
        var usuarioSQL = "INSERT INTO <?= $gDbDatabase ?>.usuarios (apellido, nombre, correoElectronico, idPerfiles, usuario, password, fechaAlta, idUsuariosAlta) VALUES ('" + 
        $("#txtApellido").val() + "', '" + $("#txtNombre").val() + "', '" + $("#txtCorreo").val() + "', " + $("#ddlPerfil").val() + ", '" + usuario + 
        "', '" + passhash + "', NOW(), <?= usuarioID() ?>)";
    	$.ajax({
    		url: 'interfaz/grilla/json-sql.php',
    		type: 'POST',
    		dataType: "html",
    	   		data: {
    			sql : usuarioSQL		
    		},
    		success: function (response) 
    		{ 
    		      if (response == "ok") {
                	$.ajax({
                		url: 'nucleo/json-correo.php',
                		type: 'POST',
                		dataType: "json",
                        contentType: "application/json; charset=utf-8",
            	   		data: JSON.stringify({
                			de     : "no-responder@<?= $gAppDominio ?>",
                			para   : $("#txtCorreo").val(),
                            asunto : "[Nextbyte CRM] Tu cuenta de usuario",
                            cuerpo : "<html>Hola " + $("#txtNombre").val() + ", <br /><br />" +
                                     "Te han creado una cuenta de usuario en Nextbyte CRM. Para ingresar a la aplicación, deberás utilizar estos datos:<br /><br />" +
                                     "<b>Dirección</b>: http://www.<?= $gAppDominio . "/" . $gAppUrlBase ?><br />" +
                                     "<b>Usuario</b>: " + usuario + "<br />" +
                                     "<b>Contraseña</b>: " + password + "<br /><br />" +
                                     "El usuario creador de tu cuenta es <b><?= usuarioNombreFormal() ?></b>.<br />" +
                                     "Enhorabuena. ¡Esperamos tu inicio de sesión en breve!"}),
                		success: function (response) 
                		{ 
                		      if (response == "ok") {
                                popupUsuario.ocultar(); 
                                grd.fetchGrid();
                		      }
                		},
                		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
                		async: true
                	});
    		      }
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
    		async: true
    	});
    }
    
</script>



<div id="divUsuario">
    <div class="popup-contenido" id="divUsuarioContenido">
        <form>
        <span>Apellido: </span><input type="text" id="txtApellido" value="" />
        <br />
        <br />
        <span>Nombre(s): </span><input type="text" id="txtNombre" value="" />
        <br />
        <br />
        <span>Correo electrónico: </span><input type="text" id="txtCorreo" placeholder="nombre@host.com" value="" />
        <br />
        <br />
        <span>Perfil: </span><select type="text" id="ddlPerfil">
            <?php 
                $bdTabla = $gDbDatabase . ".perfiles";
                $result = bdConsultarSQL($bd, "SELECT idPerfiles, perfil FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND idPerfiles > " . usuarioPerfil());
                while ($row = mysqli_fetch_array($result)) {
                   echo "<option value=\"" . $row["idPerfiles"] . "\">" . $row["perfil"] . "</option>";
                }                    
            ?>
        </select>
        <br />
        <br />
        </form>
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aceptar" onclick="nuevoUsuario()" />
        <input type="button" class="boton-rojo" value="Cancelar" onclick="popupUsuario.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupUsuario = new crmPopup("divUsuario");</script>





<?php require_once('interfaz/interfaz-fin.php'); ?>