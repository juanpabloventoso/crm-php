<?php require_once('nucleo/nucleo.php'); ?>
<?php

    // Iniciar sesión     
    if ((isset($_POST['usuario'])) && (isset($_POST['password']))) {
        $resultado = 0;
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        if ((strpos($usuario, "'") !== false) || (strpos($password, "'") !== false)) $resultado = 1;
        $bd = bdConectar();
        $result = bdConsultarSQL($bd, "SELECT * FROM " . $gDbDatabase . ".usuarios WHERE " .
        "usuario LIKE '" . $usuario . "' AND password = '" . $password . "'");
        if ($result->num_rows == 1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $bd->close();
            if ($row["fechaBaja"] != "")
                $resultado = 2;
            else {
                $resultado = 0;
                iniciarSesion($row["idUsuarios"], $row["nombre"], $row["apellido"]);
            }
        } else {
            $resultado = 1;
        }
        if ($resultado != 0) cerrarSesion();
        echo $resultado;
        return;
    }

    // Cerrar sesión     
    if (isset($_GET['cerrarSesion'])) {
        cerrarSesion();
    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="MSThemeCompatible" content="Yes" />
    <link rel="icon" type="image/png" href="<?= $gAppUrlBase ?>/favicon.png" />
    <meta name="robots" content="noindex" />
    <meta name="twitter:site" content="@<?= $gRedesTwitter ?>" />
    <meta name="twitter:creator" content="@<?= $gRedesTwitter ?>" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta property="og:type" content="Application" />
    <meta name="viewport" content="width=1030" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= $gAppUrlBase ?>/favicon.ico" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css" />
    <link href="<?= $gAppUrlBase ?>/css/base.css?v=2" rel="stylesheet" type="text/css" />
    <title>Iniciar sesión | <?= $gAppNombre ?></title>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/crypto-md5.js"></script>
</head>
<body style="background-image: url(imagenes/bg/bg1.jpg)">
    <div class="div-login-cont">
    <div id="divLogin">
        <div class="div-login-cabecera">
            <div class="div-logo">
                <a href="<?= $gAppUrlBase ?>/"><img src="<?= $gAppUrlBase ?>/imagenes/logo/logo-cabecera.png" alt="Logo" /></a>
            </div>
        </div>
        <div class="div-login-cont">
            <form id="frmLogin" method="post" action="login.php">
                <br />
                <input type="text" id="txtUsuario" required placeholder="Nombre de usuario" />
                <br />
                <br />
                <input type="password" id="txtPassword" required placeholder="Contraseña" />
                <br />
                <br />
                <input type="submit" value="Iniciar sesión" onclick="event.preventDefault(); loginSeguro();" />
                <br />
                <span id="lblError"></span>
            </form>
        </div>
    </div>
    </div>

<script type="text/javascript">    
    function loginSeguro() {
        var usuario = $(txtUsuario).val();
        var password = $(txtPassword).val();
        if ((usuario == "") || (password == "")) {
            $(lblError).html("Ingrese su usuario y contraseña.");
            return;
        }
        $(lblError).html("Iniciando sesión...");
        var passhash = CryptoJS.MD5(password).toString();
    	$.ajax({
    		url: 'login.php',
    		type: 'POST',
    		dataType: "html",
    	   		data: {
    			usuario : usuario,
    			password : passhash			
    		},
    		success: function (response) 
    		{ 
    			var success = response == "0"; 
    			if (!success) {
    			     if (response == "2")
                        $(lblError).html("Su usuario ha sido dado de baja.");
                     else
                        $(lblError).html("Nombre de usuario o contraseña incorrectos.");
                    $(txtUsuario).val("");
                    $(txtPassword).val("");
    			}
                else 
                    window.location = "principal.php";
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { 
    		    $(lblError).html("Error al iniciar sesión. Por favor contacte al Soporte Técnico.")
                $(txtUsuario).val("");
                $(txtPassword).val("");
            },
    		async: true
    	});
    }
</script>      
          
</body>
</html>