<?php

    // Información de la página para la plantilla
    $gPaginaTitulo      = "Historial de consultas";
    $gPaginaSubtitulo   = "Historial de consultas realizadas en el CRM";
    
    require_once('nucleo/nucleo.php');
    if (!usuarioLogueado()) return;
    require_once('interfaz/head.php');

?>

<?php 


    $bd = bdConectar();
    $bdTabla = $gDbDatabase . ".pedidosetapas";
    $result = bdConsultarSQL($bd, "SELECT * FROM " . $bdTabla . " WHERE fechaBaja IS NULL AND idPedidos = " . $_GET["id"]);
    $row = mysqli_fetch_array($result);
    
?>
    

    <div id="divPedidosEtapas" class="grilla grilla-full"></div>
    <script type="text/javascript">

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

        var grd = new DatabaseGrid("<?= $gDbDatabase ?>", "pedidosetapas", "divPedidosEtapas", <?= usuarioID() ?>, "pedidosetapas WHERE idPedidos = <?= $_GET["id"] ?> AND fechaBaja IS NULL");
        grd.mostrarSeleccion(2);
        
        grd.editableGrid.beforeRender = function() {
            grd.editableGrid.columns[0].hidden = true;
            grd.editableGrid.columns[5].hidden = true;
            grd.editableGrid.columns[6].hidden = true;
            grd.editableGrid.columns[1].editable = false; // idUsuarios 
            grd.editableGrid.columns[2].editable = false; // idEstados
            grd.editableGrid.columns[3].editable = false; // Fecha
            grd.editableGrid.columns[1].label = "Usuario"; 
            grd.editableGrid.columns[2].label = "Estado"; 
            grd.editableGrid.columns[3].label = "Fecha"; 
            grd.editableGrid.columns[5].label = "Contactar el"; 
            grd.editableGrid.columns[6].label = "Ignorar contacto"; 
            grd.editableGrid.columns[7].label = "Mas acciones..."; 
        };
        
    grd.customRenderer = function() {
        
    	grd.editableGrid.setCellRenderer("action", new CellRenderer({ 
    		render: function(cell, id) {                 
                cell.innerHTML+= "<span><a onclick=\"pedidoEliminar(" + id + ")\">Eliminar</a></span>";
    		}
    	}));
        
    };
            
    function pedidoEliminar(idPedido) {
        ejecutarSQL("UPDATE <?= $gDbDatabase ?>.pedidosetapas SET fechaBaja = NOW(), idUsuariosBaja = <?= usuarioID() ?> WHERE idPedidosEtapas = " + idPedido);
        grd.fetchGrid();
    }
    
        
    </script>
    
    
<?php require_once('interfaz/interfaz-fin.php'); ?>