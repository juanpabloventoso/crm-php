<?php     

    require_once('../../nucleo/nucleo.php');
    
    // Database connection
    $mysqli = bdConectar();                                   
    
    // Get all parameter provided by the javascript
    $idUsuario = $mysqli->real_escape_string(strip_tags($_POST['idUsuario']));
    $bd = $mysqli->real_escape_string(strip_tags($_POST['bd']));
    $tabla = $mysqli->real_escape_string(strip_tags($_POST['tabla']));
    
    
    // This very generic. So this script can be used to update several tables.
    $return = false;
    if ($stmt = $mysqli->prepare("INSERT INTO " . $bd . '.' . $tabla . " (fechaAlta, idUsuariosAlta) VALUES (NOW(), " . $idUsuario . ")")) {
    	$return = $stmt->execute();
    	$stmt->close();
    }             
    $mysqli->close();
    echo $return ? "ok" : "error";
          
?>
