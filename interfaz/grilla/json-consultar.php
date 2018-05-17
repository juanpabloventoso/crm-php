<?php     

require_once('../../nucleo/nucleo.php');
require_once('editablegrid.php');            


/**
 * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
 * It will be used to generate possible values for some columns.
*/
function fetch_pairs($mysqli, $query){
	if (!($res = $mysqli->query($query)))return FALSE;
	$rows = array();
	while ($row = $res->fetch_assoc()) {
		$first = true;
		$key = $value = null;
		foreach ($row as $val) {
			if ($first) { $key = $val; $first = false; }
			else { $value = $val; break; } 
		}
		$rows[$key] = $value;
	}
	return $rows;
}

// Database connection
$mysqli = bdConectar();

                    
$bd = (isset($_GET['bd'])) ? stripslashes($_GET['bd']) . '.' : '';
$tabla = (isset($_GET['tabla'])) ? stripslashes($_GET['tabla']) : '';
$from = (isset($_GET['from'])) ? stripslashes($_GET['from']) : '';
$condicion = (isset($_GET['condicion'])) ? stripslashes($_GET['condicion']) : ' 1 = 1 ';
$bajas = ((isset($_GET['bajas'])) && (strcmp($_GET['bajas'], "1") == 0)) ? '' : ' AND fechaBaja IS NULL ';
$seleccion = $_GET['seleccion'];
if (strpos(strtolower($from), "where") === false) $from .= " WHERE 1 = 1 ";

$types = array(
    "1" => "boolean", "2" => "integer",
    "3" => "integer", "4" => "float",
    "5" => "float", "6" => "float",
    "10" => "date", "12" => "datetime",
    "252" => "string", "253" => "string",
    "254" => "string"
);


$grid = new EditableGrid();
$grid->init($tabla);
$result = $mysqli->query('SELECT * FROM ' . $bd . $from . ' AND ' . $condicion . $bajas);
$fieds = $result->fetch_fields();
$ucTabla = ucfirst($tabla);
if (strcmp($tabla, "pedidosetapas") == 0)
    $ucTabla = "PedidosEtapas";

if (strcmp($seleccion, "1") == 0) $grid->addColumn('action', '&nbsp;', 'html', NULL, false, 'id' . $ucTabla);  
foreach ($fieds as $field) {
    if (endsWith($field->name, "Alta")) continue;
    if (strcmp($field->name, "password") == 0) continue;
    if ((strcmp($bajas, "") != 0) && (endsWith($field->name, "Baja"))) continue;
    $titulo = ucfirst($field->name);
    if (endsWith($field->name, "cion")) $titulo = str_replace("cion", "ción", $titulo);
    $titulo = str_replace("2", " 2", $titulo);
    $titulo = str_replace("Fecha", "Fecha de ", $titulo);
    $titulo = str_replace("Descripcion", "Descripción", $titulo);
    $titulo = preg_replace("#(.*)Id(.*?)es(.*)#is", '$2', $titulo);
    $titulo = str_replace("Client", "Cliente", $titulo);
    $titulo = str_replace("Clientee", "Cliente", $titulo);
    $titulo = str_replace("CorreoElectronico", "Correo electrónico", $titulo);
    $titulo = str_replace("Telefono", "Teléfono", $titulo);
    $titulo = str_replace("IdUsuariosBaja", "Usuario de baja", $titulo);
    $tipo = "";
    foreach($types as $x => $valor) {
        if ($field->type == $x)
            $tipo = $valor;
    }
    if (startsWith($field->name, "id")) {
        if ($field->flags & MYSQLI_PRI_KEY_FLAG) 
            continue; //$grid->addColumn($field->name, "ID", $tipo, NULL, false);
        else
        {
            $tablaFK = str_replace('id', '', $field->name);
            $grid->addColumn($field->name, $titulo, $tipo,
            fetch_pairs($mysqli, 'SELECT * FROM ' . $bd . $tablaFK . " WHERE fechaBaja IS NULL"), true);
        }
    }
    else
        if (strcmp($field->type, "10") == 0)
            $grid->addColumn($field->name, $titulo, $tipo);
        else
            $grid->addColumn($field->name, $titulo, $tipo);
}
if (strcmp($seleccion, "2") == 0) $grid->addColumn('action', '&nbsp;', 'html', NULL, false, 'id' . $ucTabla);

$mysqli->close();
                                                                       
// send data to the browser
$grid->renderJSON($result);

?>