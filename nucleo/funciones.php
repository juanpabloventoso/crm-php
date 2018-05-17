<?php
    


// -------------- Funciones de creación de los objetos del CRM ----------------- //


// Agrega un cliente (si ya existe uno con el mismo correo no lo agrega)
function crmAgregarCliente($nombre, $correo, $telefono)
{
    global $gDbDatabase;
    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".clientes";
    $result = bdConsultarSQL($bd, "SELECT * FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND correoElectronico LIKE '" . $correo . "'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $bd->close();
        return $row["idClientes"];
    }
    $usuarioID = usuarioID();
    if (!isset($usuarioID)) $usuarioID = 1;
    if (bdEscribirSQL($bd, "INSERT INTO " . $bdTabla . " (cliente, correoElectronico, telefono, fechaAlta, idUsuariosAlta) " . 
    " VALUES ('" . $nombre . "', '" . $correo . "', '" . $telefono . "', NOW(), " . $usuarioID . ")")) {
        $id = bdMaximoID($bd, $gDbDatabase, "clientes");
        $bd->close();
        return $id;
    }
    $bd->close();
    return 0;
}

// Agrega un pedido para un cliente ya creado
function crmAgregarPedido($idCliente, $idOrigen, $pedido)
{
    global $gDbDatabase;
    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".pedidos";
    $usuarioID = usuarioID();
    if (!isset($usuarioID)) $usuarioID = 1;
    if (bdEscribirSQL($bd, "INSERT INTO " . $bdTabla . " (idClientes, idOrigenes, pedido, fechaInicio, fechaAlta, idUsuariosAlta) " .
    "VALUES (" . $idCliente . ", " . $idOrigen . ", '" . $pedido . "', NOW(), NOW(), " . $usuarioID . ")"))
        return bdMaximoID($bd, $gDbDatabase, "pedidos");
    $bd->close();
    return 0;
}


// Agrega un pedido y un cliente asociado ingresado desde la web (formulario de contacto o similar)
function crmAgregarPedidoWeb($nombre, $correo, $telefono, $pedido)
{
    $idCliente = crmAgregarCliente($nombre, $correo, $telefono);
    return crmAgregarPedido($idCliente, 1, $pedido);
}

// Agrega una etapa para un pedido existente
function crmAgregarEtapa($idPedido, $idEstado, $idUsuario, $descripcion, $fechaProxima)
{
    global $gDbDatabase;
    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".pedidosetapas";
    $usuarioID = usuarioID();
    if (!isset($usuarioID)) $usuarioID = 1;
    if (bdEscribirSQL($bd, "INSERT INTO " . $bdTabla . " (idPedidos, idUsuarios, idEstados, fecha, descripcion, fechaProxima, fechaProximaIgnorar, fechaAlta, idUsuariosAlta) " .
    "VALUES (" . $idPedido . ", " . $idUsuario . ", " . $idEstado . ", NOW(), '" . $descripcion . "', " . $fechaProxima . ", 0, NOW(), " . $usuarioID . ")"))
        return bdMaximoID($bd, $gDbDatabase, "pedidosetapas");
    $bd->close();
    return 0;
}




?>