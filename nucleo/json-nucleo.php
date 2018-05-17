<?php     

    require_once('nucleo.php');
    
    if (!isset($_POST['operacion'])) return;
    
    // Agregar pedido/cliente desde la web
    if ($_POST["operacion"] == "1") {
        $nombre = (isset($_POST['nombre'])) ? stripslashes($_POST['nombre']) : '';
        $correo = (isset($_POST['correo'])) ? stripslashes($_POST['correo']) : '';
        $telefono = (isset($_POST['telefono'])) ? stripslashes($_POST['telefono']) : '';
        $pedido = (isset($_POST['pedido'])) ? stripslashes($_POST['pedido']) : '';
        crmAgregarPedidoWeb($nombre, $correo, $telefono, $pedido);
    }
    
    
    // Agregar una etapa asignada a un usuario
    if ($_POST["operacion"] == "2") {
        global $gDbDatabase;
        $id = (isset($_POST['id'])) ? stripslashes($_POST['id']) : '';
        $idUsuario = (isset($_POST['idUsuario'])) ? stripslashes($_POST['idUsuario']) : '';
        $bd = bdConectar();
        $bdTabla = $gDbDatabase . ".usuarios";
        $result = bdConsultarSQL($bd, "SELECT * FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND idUsuarios = " . $idUsuario);
        if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $bd->close();
            $nombreUsuario = $row["apellido"] . ", " . $row["nombre"];
            crmAgregarEtapa($id, 2, $idUsuario, "Pedido asignado al usuario " . $nombreUsuario, "NULL");
        }
    }
    
    // Agregar una etapa como "Esperando respuesta"
    if ($_POST["operacion"] == "10") {
        $id = (isset($_POST['id'])) ? stripslashes($_POST['id']) : '';
        crmAgregarEtapa($id, 4, "NULL", "NULL", "NULL");
    }
    
    
    // Agregar una etapa como "Finalizado con venta"
    if ($_POST["operacion"] == "11") {
        $id = (isset($_POST['id'])) ? stripslashes($_POST['id']) : '';
        crmAgregarEtapa($id, 5, "NULL", "NULL", "NULL");
    }
    
    
    // Agregar una etapa como "Finalizado sin venta"
    if ($_POST["operacion"] == "12") {
        $id = (isset($_POST['id'])) ? stripslashes($_POST['id']) : '';
        crmAgregarEtapa($id, 6, "NULL", "NULL", "NULL");
    }
    
    
    
    
    
?>
