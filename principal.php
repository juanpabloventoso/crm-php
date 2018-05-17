<?php if (isset($_POST["id"])) { 
    
    require_once('nucleo/nucleo.php');
    if (!usuarioLogueado()) return;
    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".pedidos";
    $result = bdConsultarSQL($bd, "SELECT * FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND idPedidos = " . $_POST["id"]);
    $row = mysqli_fetch_array($result);
    
?>

<form>
<span>Cliente: </span><select id="ddlCliente">
    <?php 
        $bdTabla = $gDbDatabase . ".clientes";
        $result = bdConsultarSQL($bd, "SELECT idClientes, cliente FROM " . $bdTabla . " WHERE fechaBaja IS NULL ORDER BY cliente");
        while ($xrow = mysqli_fetch_array($result)) {
           if ($xrow["idClientes"] == $row["idClientes"])
               echo "<option value=\"" . $xrow["idClientes"] . "\" selected>" . $xrow["cliente"] . "</option>";
           else
               echo "<option value=\"" . $xrow["idClientes"] . "\">" . $xrow["cliente"] . "</option>";
        }                    
    ?>
</select>
<br />
<br />
<span>Origen: </span><select id="ddlOrigen">
    <?php 
        $bdTabla = $gDbDatabase . ".origenes";
        $result = bdConsultarSQL($bd, "SELECT idOrigenes, origen FROM " . $bdTabla . " WHERE fechaBaja IS NULL ORDER BY origen");
        while ($xrow = mysqli_fetch_array($result)) {
           if ($xrow["idOrigenes"] == $row["idOrigenes"])
               echo "<option value=\"" . $xrow["idOrigenes"] . "\" selected>" . $xrow["origen"] . "</option>";
           else
               echo "<option value=\"" . $xrow["idOrigenes"] . "\">" . $xrow["origen"] . "</option>";
        }                    
    ?>
</select>
<br />
<br />
<p>Descripción del pedido: </p>
<textarea id="txtPedido"><?= $row["pedido"] ?></textarea>
</form>
<?php return; } ?>






<?php

    // Parámetros AJAX
    //
    // dibujarEstadisticas: Devuelve los paneles estadísticos
    
    $filtro = "";
    if (isset($_POST["filtro"])) $filtro = $_POST["filtro"];
    $rango = " AND fechaInicio >= STR_TO_DATE('" . date('d/m/Y', strtotime('-90 days')) . "', '%d/%m/%Y') AND fechaInicio <= STR_TO_DATE('" . date('d/m/Y') . "', '%d/%m/%Y')";
    if (isset($_POST["rango"])) $rango = $_POST["rango"];
    if (!isset($_POST["dibujarEstadisticas"])) {
            
        // Información de la página para la plantilla
        $gPaginaTitulo      = "Principal";
        $gPaginaSubtitulo   = "Página principal del CRM";
        
        require_once('interfaz/interfaz-inicio.php');
    
    } else {
        require_once('nucleo/nucleo.php');
    }
    
    if (!usuarioLogueado()) return;
    
    
    $sqlPedidosNuevos = " idEstados = 1 ";
    $sqlPedidosTodos = " 1 = 1 ";
    $sqlPedidosFinalizados = " idEstados BETWEEN 5 AND 6 ";
    if (intval(usuarioPerfil()) == 3) { // Vendedor
        $sqlPedidosNuevos = " idUsuarios = " . usuarioID() . " AND idEstados = 2 ";
        $sqlPedidosTodos = " idUsuarios = " . usuarioID();
        $sqlPedidosFinalizados = " idUsuarios = " . usuarioID() . " AND " . $sqlPedidosFinalizados;
    }

    $stat_consultasNuevas = 0;
    $stat_consultasTotales = 0;
    $stat_consultasFinalizadas = 0;
    $stat_crecimiento = 0;
    $stat_ganancias = 0;
    
    $bd = bdConectar();

    function recolectarEstadisticas() {
        global $bd, $gDbDatabase, $stat_consultasNuevas, $stat_consultasTotales, $stat_consultasFinalizadas, $stat_crecimiento;
        global $stat_ganancias, $sqlPedidosNuevos, $sqlPedidosTodos, $sqlPedidosFinalizados, $filtro, $rango;
        $result = bdConsultarSQL($bd, "SELECT (SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosNuevos . $filtro . $rango . " AND fechaBaja IS NULL) AS cantidad");
        $stat_consultasNuevas = mysqli_fetch_array($result)["cantidad"];
        $result = bdConsultarSQL($bd, "SELECT (SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosTodos . $filtro . $rango . " AND fechaBaja IS NULL) AS cantidad");
        $stat_consultasTotales = mysqli_fetch_array($result)["cantidad"];
        $result = bdConsultarSQL($bd, "SELECT (SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosFinalizados . $filtro . $rango . " AND fechaBaja IS NULL) AS cantidad");
        $stat_consultasFinalizadas = mysqli_fetch_array($result)["cantidad"];
        $result = bdConsultarSQL($bd, "SELECT (SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosTodos . " AND " .
        "(fechaAlta < CURDATE() - INTERVAL 30 DAY) AND fechaBaja IS NULL) AS cantidad");
        if (intval(mysqli_fetch_array($result)["cantidad"]) > 0) {
            $result = bdConsultarSQL($bd, "SELECT ((SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosTodos . " AND " .
            "(fechaAlta BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()) AND fechaBaja IS NULL) / " .
            "(SELECT COUNT(*) FROM " . $gDbDatabase . ".pedidos WHERE " . $sqlPedidosTodos . " AND " .
            "(fechaAlta < CURDATE() - INTERVAL 30 DAY) AND fechaBaja IS NULL)) AS crecimiento");
            $stat_crecimiento = round(mysqli_fetch_array($result)["crecimiento"], 2);
        }
        else
            $stat_crecimiento = "0";
        $result = bdConsultarSQL($bd, "SELECT (SELECT SUM(pr.precio) FROM " . $gDbDatabase . ".pedidos p, " . $gDbDatabase . ".productos pr WHERE " . 
        $sqlPedidosTodos . " AND p.idProductos = pr.idProductos AND p.idEstados = 5 AND " .
        "(p.fechaAlta BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()) AND p.fechaBaja IS NULL) AS ganancias");
        $stat_ganancias = round(mysqli_fetch_array($result)["ganancias"], 2);
    }

    function dibujarEstadisticas() {
        global $stat_consultasNuevas, $stat_consultasTotales, $stat_consultasFinalizadas, $stat_crecimiento, $stat_ganancias;
        echo "<div class=\"brand-item-cont\">";
        echo "<a class=\"brand-item\" style=\"background: #27A9E3;\" onclick=\"mostrarNuevas()\">";
        echo "    <p class=\"brand-texto-sup\">" . $stat_consultasNuevas . "</p>";
        echo "    <p class=\"brand-texto-med\">Consultas nuevas</p>";
        echo "</a>";
        echo "</div>";
        
        echo "<div class=\"brand-item-cont\">";
        echo "<a class=\"brand-item\" style=\"background: #FFB849;\" onclick=\"mostrarTodas()\">";
        echo "    <p class=\"brand-texto-sup\">" . $stat_consultasTotales . "</p>";
        echo "    <p class=\"brand-texto-med\">Consultas totales</p>";
        echo "</a>";
        echo "</div>";
        
        echo "<div class=\"brand-item-cont\">";
        echo "<a class=\"brand-item\" style=\"background: #FF640F;\" onclick=\"mostrarFinalizadas()\">";
        echo "    <p class=\"brand-texto-sup\">" . $stat_consultasFinalizadas . "</p>";
        echo "    <p class=\"brand-texto-med\">Consultas finalizadas</p>";
        echo "</a>";
        echo "</div>";
        
        echo "<div class=\"brand-item-cont\">";
        echo "<a class=\"brand-item\" style=\"background: #28B779;\">";
        echo "    <p class=\"brand-texto-sup\">" . $stat_crecimiento . " %</p>";
        echo "    <p class=\"brand-texto-med\">Crecimiento en 30 días</p>";
        echo "</a>";
        echo "</div>";
        
        echo "<div class=\"brand-item-cont\">";
        echo "<a class=\"brand-item\" style=\"background: #852C9A; margin-right: 0;\">";
        echo "    <p class=\"brand-texto-sup\">$ " . $stat_ganancias . ",00</p>";
        echo "    <p class=\"brand-texto-med\">Ganancias en 30 días</p>";
        echo "</a>";
        echo "</div>";

        echo "<div class=\"clear\"></div>";
    }

    recolectarEstadisticas();

    if (isset($_POST["dibujarEstadisticas"])) {
        dibujarEstadisticas();
        return;
    }
        

?>

<div id="divEstadisticas">
    <?php dibujarEstadisticas(); ?>
</div>

<br />

<div id="divPedidos" class="grilla grilla-full"></div>
<script type="text/javascript">


    var idPedidoActual = 0;
    var idEstadoActual = 0;
    var filtroActual = "";
    var rangoActual = "";
    var seccionActual = 0;
    var fromActual = "";

    function ejecutarSQL(sql) {
    	$.ajax({
    		url: 'interfaz/grilla/json-sql.php',
    		type: 'POST',
    		dataType: "html",
    	   		data: {
    			sql : sql		
    		},
    		success: function (response) 
    		{ 
                grd.fetchGrid();
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
    		async: true
    	});
    }

    function actualizarEstadisticas() {
    	$.ajax({
    		url: 'principal.php',
    		type: 'POST',
    		dataType: "html",
    	   	data: { dibujarEstadisticas : '1', filtro: filtroActual, rango: rangoActual },
    		success: function (response) { 
    		  $("#divEstadisticas").html(response);
            },
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
    		async: true
    	});
    }
    
    function mostrarNuevas() {
        seccionActual = 0;
        leerPedidos();
    }
    
    function mostrarTodas() {
        seccionActual = 1;
        leerPedidos();
    }
    
    function mostrarFinalizadas() {
        seccionActual = 2;
        leerPedidos();
    }
    
    function armarFrom() {
        if (seccionActual == 0)
            fromActual = "qpedidos WHERE <?= $sqlPedidosNuevos ?> AND fechaBaja IS NULL" + filtroActual + rangoActual;
        else
            if (seccionActual == 1)
                fromActual = "qpedidos WHERE <?= $sqlPedidosTodos ?> AND fechaBaja IS NULL" + filtroActual + rangoActual;
            else
                fromActual = "qpedidos WHERE <?= $sqlPedidosFinalizados ?> AND fechaBaja IS NULL" + filtroActual + rangoActual;
    }
    
    function leerPedidos() {
        armarFrom();
        grd.from = fromActual;
        grd.fetchGrid();
    }

    function verCliente(idCliente) {
    	$.ajax({
    		url: 'clientes.php',
    		type: 'POST',
    		dataType: "html",
    	   	data: { id : idCliente },
    		success: function (response) { 
    		  $("#divClienteContenido").html(response);
              popupCliente.mostrar(); 
            },
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
    		async: true
    	});
    }
    
    function pedidoAsignarAsignar(idPedidoUsuario) {
        ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET idUsuarios = " + idPedidoUsuario + " WHERE idPedidos = " + idPedidoActual);
        popupAsignar.ocultar();
    }
    
    function pedidoAsignarCancelar() {
        ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET idEstados = " + idEstadoActual + " WHERE idPedidos = " + idPedidoActual);
        popupAsignar.ocultar();
    }
    
    function pedidoEsperar(idPedido) {
    	$.ajax({
    		url: 'nucleo/json-nucleo.php',
    		type: 'POST',
    		dataType: "html",
    	   	data: { operacion : '10' },
    		success: function (response) { grd.editableGrid.refresh(); },
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
    		async: true
    	});
    }
    
    function pedidoEditar(idPedido) {
        idPedidoActual = idPedido;
    	$.ajax({
    		url: 'principal.php',
    		type: 'POST',
    		dataType: "html",
    	   	data: { id : idPedido },
    		success: function (response) { 
    		  $("#divPedidoContenido").html(response);
              popupPedido.mostrar(); 
            },
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
    		async: true
    	});
    }
    
    function agregarEvento(idPedido) {
        idPedidoActual = idPedido;
        popupEvento.mostrar(); 
    }
    
    function guardarEvento() {
        ejecutarSQL("INSERT INTO <?= $gDbDatabase ?>.pedidosetapas (idPedidos, idUsuarios, idEstados, fecha, descripcion, fechaAlta, idUsuariosAlta) VALUES " +
        "(" + idPedidoActual + ", <?= usuarioID() ?>, (SELECT idEstados FROM <?= $gDbDatabase ?>.pedidos WHERE idPedidos = " + idPedidoActual + "), NOW(), '" +
        $('#txtEvento').val() + "', NOW(), <?= usuarioID() ?>)");
        $("#txtEvento").val("");
        popupEvento.ocultar(); 
    }
    
    function pedidoHistoria(idPedido) {
        $("#frmHistoria").attr("src", "historial.php?id=" + idPedido);
        popupHistoria.mostrar(); 
    }
    
    function pedidoEliminar(idPedido) {
        ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET fechaBaja = NOW(), idUsuariosBaja = <?= usuarioID() ?> WHERE idPedidos = " + idPedido);
        leerPedidos();
        actualizarEstadisticas();
    }
    
    function guardarPedido() {
        ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET idClientes = " + $('#ddlCliente').val() + ", idOrigenes = " + $('#ddlOrigen').val() +
        ", pedido = '" + $('#txtPedido').val() + "' WHERE idPedidos = " + idPedidoActual);
        popupPedido.ocultar(); 
    }
    
    function mostrarFiltros() {
        popupFiltros.mostrar();
    }
    
    function aplicarFiltros() {
        filtroActual = "";        
        <?php 
            $bdTabla = $gDbDatabase . ".estados";
            $result = bdConsultarSQL($bd, "SELECT idEstados FROM " . $bdTabla . " WHERE fechaBaja IS NULL ORDER BY idEstados");
            while ($xrow = mysqli_fetch_array($result)) {
            echo "if ($(\"#chkEstado" . $xrow["idEstados"] . "\").prop('checked')) filtroActual += \"" . $xrow["idEstados"] . ",\"; \n";
            }                    
        ?>
        filtroActual = filtroActual.replace(/\,$/, "");
        if (filtroActual != "")
            filtroActual = " AND idEstados IN (" + filtroActual + ")";
        rangoActual = " AND fechaInicio >= STR_TO_DATE('" + $("#txtFecha1").val() + "', '%d/%m/%Y') AND fechaInicio <= STR_TO_DATE('" + $("#txtFecha2").val() + "', '%d/%m/%Y')";
        popupFiltros.ocultar();
    }
    
    function actualizarPedidos() {
        aplicarFiltros();
        leerPedidos();
        actualizarEstadisticas();
    }
    
    
</script>



<div id="divAsignar">
    <div class="popup-contenido">
        Asignar la consulta a:
        <br />
        <select id="ddlAsignar">
            <?php 
                $bdTabla = $gDbDatabase . ".usuarios";
                $result = bdConsultarSQL($bd, "SELECT idUsuarios, apellido, nombre FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND " . 
                    "idPerfiles BETWEEN 2 AND 3 AND idUsuarios <> " . usuarioID() . " ORDER BY apellido");
                while ($row = mysqli_fetch_array($result)) {
                   echo "<option value=\"" . $row["idUsuarios"] . "\">" . $row["apellido"] . ", " . $row["nombre"] . "</option>";
                }                    
            ?>
        </select>
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aceptar" onclick="pedidoAsignarAsignar($('#ddlAsignar').val());"></input>
        <input type="button" class="boton-rojo" value="Cancelar" onclick="pedidoAsignarCancelar();"></input>
    </div>
</div>
<script type="text/javascript">var popupAsignar = new crmPopup("divAsignar");</script>



    
<div id="divPedido">
    <div class="popup-contenido" id="divPedidoContenido">
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aceptar" onclick="guardarPedido()" />
        <input type="button" class="boton-rojo" value="Cancelar" onclick="popupPedido.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupPedido = new crmPopup("divPedido");</script>




<div id="divCliente">
    <div class="popup-contenido" id="divClienteContenido">
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-rojo" value="Cerrar" onclick="popupCliente.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupCliente = new crmPopup("divCliente");</script>




<div id="divEvento">
    <div class="popup-contenido" id="divEventoContenido">
        <form>
        <p>Descripción del evento: </p>
        <textarea id="txtEvento" style="width: 600px;"></textarea>
        </form>    
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aceptar" onclick="guardarEvento()" />
        <input type="button" class="boton-rojo" value="Cancelar" onclick="popupEvento.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupEvento = new crmPopup("divEvento");</script>



<div id="divHistoria">
    <div class="popup-contenido" id="divHistoriaContenido">
        Eventos asociados a la consulta:
        <br />
        <iframe id="frmHistoria" style="width: 800px; height: 300px"></iframe>
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-rojo" value="Cerrar" onclick="popupHistoria.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupHistoria = new crmPopup("divHistoria");</script>


<div id="divFiltros">
    <div class="popup-contenido" id="divFiltrosContenido">
        Filtrar estados:
        <br />
        <br />
        <?php 
            $bdTabla = $gDbDatabase . ".estados";
            $result = bdConsultarSQL($bd, "SELECT idEstados, estado FROM " . $bdTabla . " WHERE fechaBaja IS NULL ORDER BY idEstados");
            while ($xrow = mysqli_fetch_array($result)) {
            echo "<input type=\"checkbox\" id=\"chkEstado" . $xrow["idEstados"] . "\" checked>" . $xrow["estado"] . "</input><br />";
            }                    
        ?>
    </div>
    <div class="popup-botonera">
        <input type="button" class="boton-verde" value="Aplicar" onclick="actualizarPedidos();" />
        <input type="button" class="boton-rojo" value="Cerrar" onclick="popupFiltros.ocultar(); " />
    </div>
</div>
<script type="text/javascript">var popupFiltros = new crmPopup("divFiltros");</script>



<div id="divFecha" style="position: absolute; top: 22px; right: 11px;">
    Ver desde el&nbsp;
    <input type="text" id="txtFecha1" style="width: 120px;" value="<?= date('d/m/Y', strtotime("-90 days")) ?>" onchange="actualizarPedidos();" />
    &nbsp;al&nbsp;
    <input type="text" id="txtFecha2" style="width: 120px;" value="<?= date('d/m/Y') ?>" onchange="actualizarPedidos();" />
</div>

<script type="text/javascript">
    $("#txtFecha1").datepicker();
    $("#txtFecha2").datepicker();
    
    
    
    
    aplicarFiltros();
    armarFrom();
    var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "pedidos", "divPedidos", <?= usuarioID() ?>, fromActual);
    grd.mostrarSeleccion(2);
    
    grd.alModificar = function(rowIndex, colIndex, oldValue, newValue) {
        
        var fetch = true;
        // Acciones automáticas al cambiar de estado
        if (colIndex == 8) {
            
            // Nuevo
            if (newValue ==  1) {
                
                <?php // Si el usuario es vendedor no puede cambiar el estado del pedido a "Nuevo"
                if (intval(usuarioPerfil()) != 3) { ?>
                ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET idUsuarios = NULL, fechaFin = NULL, fechaProxima = NULL WHERE idPedidos = " + grd.editableGrid.getRowId(rowIndex));
                <?php } else { ?>
                ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidos SET idEstados = 2 WHERE idPedidos = " + grd.editableGrid.getRowId(rowIndex));
                <?php } ?>
                fetch = false;
            }
            
            // Asignado
            if (newValue ==  2) {
                <?php if (intval(usuarioPerfil()) != 3) { ?>
                idPedidoActual = grd.editableGrid.getRowId(rowIndex);
                idEstadoActual = oldValue;
                popupAsignar.mostrar();
                <?php } ?>
                fetch = false;
            }
            
        }
        if (fetch) grd.fetchGrid();
        actualizarEstadisticas();
                
    }
    
    grd.editableGrid.beforeRender = function() {
        grd.editableGrid.columns[10].hidden = true;
        grd.editableGrid.columns[9].hidden = true;
        grd.editableGrid.columns[6].hidden = true;
        grd.editableGrid.columns[12].label = "Mas acciones..."; 
        grd.editableGrid.columns[0].hidden = true; 
        grd.editableGrid.columns[1].label = "Cliente"; 
        grd.editableGrid.columns[1].editable = false; // idClientes 
        grd.editableGrid.columns[2].editable = false; // Consulta 
        grd.editableGrid.columns[3].editable = false; // idOrigenes 
        grd.editableGrid.columns[4].label = "Producto/servicio asociado"; 
        grd.editableGrid.columns[5].editable = false; // fechaInicio 
        grd.editableGrid.columns[5].label = "Recibido"; 
        grd.editableGrid.columns[7].label = "Asignado a"; 
        grd.editableGrid.columns[8].label = "Estado"; 
        grd.editableGrid.columns[11].label = "Última acción";
    };
    
    grd.editableGrid.tableRendered = function() {
        filtro = "<a onclick=\"mostrarFiltros()\"><img src=\"<?= $gAppUrlBase ?>/imagenes/grilla/flecha-abajo-blanca.png\" " +
            "alt=\"Filtrar...\" style=\"float: right; border: 0; margin: 6px; width: 13px;\" /></a>";
        $(".editablegrid-idEstados").append(filtro);
    };
    
    grd.customRenderer = function() {
        
    	grd.editableGrid.setCellRenderer("action", new CellRenderer({ 
    		render: function(cell, id) {                 
                cell.innerHTML+= "<span><a onclick=\"pedidoEditar(" + id + ")\">Editar</a></span> | <a onclick=\"agregarEvento(" + id + ")\">Agregar evento</a></span> | " +
                "<span><a onclick=\"pedidoHistoria(" + id + ")\">Ver eventos</a></span> | <span><a onclick=\"pedidoEliminar(" + id + ")\">Eliminar</a></span>";
    		}
    	}));
        
    	grd.editableGrid.setCellRenderer("idClientes", new CellRenderer({ 
    		render: function(cell, data) {                 
                cell.innerHTML+= "<a onclick=\"verCliente(" + data + ")\">" + grd.editableGrid.columns[1].optionValuesForRender[data] + "</a>";
    		}
    	}));
        
    	grd.editableGrid.setCellRenderer("pedido", new CellRenderer({ 
    		render: function(cell, data) {                 
                cell.innerHTML+= data;
                cell.style.maxWidth = "400px";
    		}
    	}));
        
    	grd.editableGrid.setCellRenderer("idEstados", new CellRenderer({ 
    		render: function(cell, data) {                 
                var bgColor = "#D6EBF4"; // Nueva
                if (data == "2") bgColor = "#FCE6C4"; // Asignada
                if (data == "3") bgColor = "#FCE6C4"; // Contacto pendiente
                if (data == "4") bgColor = "#FCE6C4"; // Esperando respuesta
                if (data == "5") bgColor = "#ADF3D3"; // Finalizada con venta
                if (data == "6") bgColor = "#FCD1D1"; // Finalizada sin venta
                cell.innerHTML+= "<div style='font-weight: 600; padding: 5px; background-color: " + bgColor + "'>" + grd.editableGrid.columns[8].optionValuesForRender[data] + "</div>";
                cell.style.minWidth = "100px";
    		}
    	}));
        
         
    };
        
</script>

<?php 

    $bd->close();
    require_once('interfaz/interfaz-fin.php'); 
    
?>