<?php     

    require_once('../../nucleo/nucleo.php');
    
    // Database connection
    $mysqli = bdConectar();                                   
    
    // Get all parameters provided by the javascript
    $colname = $mysqli->real_escape_string(strip_tags($_POST['colname']));
    $id = $mysqli->real_escape_string(strip_tags($_POST['id']));
    $coltype = $mysqli->real_escape_string(strip_tags($_POST['coltype']));
    $value = $mysqli->real_escape_string(strip_tags($_POST['newvalue']));
    $bd = $mysqli->real_escape_string(strip_tags($_POST['bd']));
    $tabla = $mysqli->real_escape_string(strip_tags($_POST['tabla']));
                                                    
    // Here, this is a little tips to manage date format before update the table
    if ($coltype == 'date') {
       if ($value === "") 
      	 $value = NULL;
       else {
          $date_info = date_parse_from_format('d/m/Y', $value);
          $value = "{$date_info['year']}-{$date_info['month']}-{$date_info['day']}";
       }
    }         
                 
    // This very generic. So this script can be used to update several tables.
    $return = false;
    if ( $stmt = $mysqli->prepare("UPDATE " . $bd . '.' . $tabla . " SET " . $colname . " = ? WHERE id" . ucfirst($tabla) . " = ?")) {
    	$stmt->bind_param("si", $value, $id);
    	$return = $stmt->execute();
    	$stmt->close();
    	
    }             
    $mysqli->close();        
    echo $return ? "ok" : "error";
                
?>
