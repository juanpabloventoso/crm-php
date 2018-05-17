<?php

    require_once('opciones.php');

    function bdConectar() {
        global $gDbHost, $gDbUsuario, $gDbPassword;
        $mysqli = mysqli_init();
        $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        $mysqli->real_connect($gDbHost, $gDbUsuario, $gDbPassword);
        return $mysqli; 
    }

    function bdConsultarSQL($bd, $sql) {
        return $bd->query($sql);
    }
    
    function bdMaximoID($bd, $database, $tabla) {
        $result = bdConsultarSQL($bd, "SELECT MAX(id" . ucfirst($tabla) . ") AS id FROM " . $database . "." . $tabla);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row["id"];
    }

    function bdEscribirSQL($bd, $sql) {
        $sqli = $bd->prepare($sql);
    	return $sqli->execute();
    }

?>