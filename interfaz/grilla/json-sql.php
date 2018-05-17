<?php     

    require_once('../../nucleo/nucleo.php');
    
    // Database connection
    $mysqli = bdConectar();                                   
    
    // Get all parameters provided by the javascript
    $sql = $_POST['sql'];

    // This very generic. So this script can be used to update several tables.
    $return = false;
    if ( $stmt = $mysqli->prepare($sql)) {
    	$return = $stmt->execute();
    	$stmt->close();
    	
    }             
    $mysqli->close();        
    echo $return ? "ok" : "error";
                
?>
