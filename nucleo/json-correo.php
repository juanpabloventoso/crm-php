<?php     

    require_once('nucleo.php');
    
    $de = (isset($_POST['de'])) ? stripslashes($_POST['de']) : '';
    $para = (isset($_POST['para'])) ? stripslashes($_POST['para']) : '';
    $asunto = (isset($_POST['asunto'])) ? stripslashes($_POST['asunto']) : '';
    $cuerpo = (isset($_POST['cuerpo'])) ? stripslashes($_POST['cuerpo']) : '';
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . $de . "\r\n";
    
    mail($para, $asunto, $cuerpo, $headers);

?>
