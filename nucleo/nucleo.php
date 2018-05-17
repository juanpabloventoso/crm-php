<?php
    
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    session_start();
    
    require_once('opciones.php');
    require_once('bd.php');
    require_once('funciones.php');





// -------------------- Funciones de acceso al usuario --------------------- //

function usuarioID() {
    if (!isset($_SESSION["usuarioID"])) return null;
    return $_SESSION["usuarioID"];
}

function usuarioNombre() {
    if (!isset($_SESSION["usuarioNombre"])) return null;
    return $_SESSION["usuarioNombre"];
}

function usuarioPerfil() {
    if (!isset($_SESSION["usuarioPerfil"])) return null;
    return $_SESSION["usuarioPerfil"];
}

function usuarioNombreFormal() {
    if (!isset($_SESSION["usuarioNombreFormal"])) return null;
    return $_SESSION["usuarioNombreFormal"];
}

function usuarioImagen() {
    global $gAppUrlBase;
    $file = "imagenes/usuarios/" . usuarioID() . ".jpg";
    if (file_exists($file)) return $gAppUrlBase . "/" . $file; 
    $file = "imagenes/usuarios/" . usuarioID() . ".png";
    if (file_exists($file)) return $gAppUrlBase . "/" . $file; 
    return $gAppUrlBase . "/imagenes/usuario.png";
}

function usuarioLogueado() {
    return isset($_SESSION["usuarioID"]);
}

function iniciarSesion($idUsuario, $nombre, $apellido) {
    global $gDbDatabase;
    $_SESSION["usuarioID"] = $idUsuario;
    if (strcmp($nombre, "") != 0)
        $_SESSION["usuarioNombre"] = $nombre;
    else
        $_SESSION["usuarioNombre"] = $apellido;
    $_SESSION["usuarioNombreFormal"] = trim($nombre . " " . $apellido);
    $bd = bdConectar();
    $result = bdConsultarSQL($bd, "SELECT idPerfiles FROM " . $gDbDatabase . ".usuarios WHERE idUsuarios = " . $idUsuario);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $bd->close();
    $_SESSION["usuarioPerfil"] = $row["idPerfiles"];
}

function cerrarSesion() {
    unset($_SESSION['usuarioID']);
    unset($_SESSION['usuarioNombre']);
    unset($_SESSION['usuarioNombreFormal']);
    unset($_SESSION['usuarioPerfil']);
}





// -------------- Funciones generales de manejo de strings ----------------- //

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0)
        return true;
    return (substr($haystack, -$length) === $needle);
}







?>