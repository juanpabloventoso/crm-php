function highlightRow(rowId, bgColor, after)
{
	var rowSelector = $("#" + rowId);
	rowSelector.css("background-color", bgColor);
    rowSelector.animate({backgroundColor: '#ffffff'});
}



function highlight(div_id, style) {
	highlightRow(div_id, style == "error" ? "#FEB692" : style == "warning" ? "#FCD494" : "#88F4C2");
}
     
var grdCurrentID = 0;     

function initCap(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function updateCellValue(bd, tabla, editableGrid, rowIndex, columnIndex, oldValue, newValue, row, onResponse, grd)
{      
	$.ajax({
		url: 'interfaz/grilla/json-modificar.php',
		type: 'POST',
		dataType: "html",
	   		data: {
			bd : bd,
			tabla : tabla,
			id: editableGrid.getRowId(rowIndex), 
			newvalue: editableGrid.getColumnType(columnIndex) == "boolean" ? (newValue ? 1 : 0) : newValue, 
			colname: editableGrid.getColumnName(columnIndex),
			coltype: editableGrid.getColumnType(columnIndex)			
		},
		success: function (response) 
		{ 
			var success = (response == "ok" || !isNaN(parseInt(response)));
            if (success)
                onResponse(rowIndex, columnIndex, oldValue, newValue);
            else 
			    editableGrid.setValueAt(rowIndex, columnIndex, oldValue);
		    highlight(row.id, success ? "ok" : "error"); 
		},
		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
		async: true
	});
}

   
function DatabaseGrid(bd, tabla, contenedor, usuarioID, from) 
{ 
    var self = this;
	this.editableGrid = new EditableGrid("Grilla", {
		enableSort: true,
      	pageSize: 25,
        tableRendered:  function() {  updatePaginator(this); },
   	    tableLoaded: function() { self.initializeGrid(this); },
		modelChanged: function(rowIndex, columnIndex, oldValue, newValue, row) {
   	    	updateCellValue(bd, tabla, this, rowIndex, columnIndex, oldValue, newValue, row, self.alModificar, self);
       	}
 	});
    this._mostrarBajas = 0;
    this._mostrarSeleccion = 1;
	this.bd = bd;
    this.tabla = tabla;
    if (!from) this.from = tabla; else this.from = from;
    this.usuarioID = usuarioID;
    this.contenedor = contenedor;
	this.fetchGrid(); 
}

DatabaseGrid.prototype.alEliminar = function() {};
DatabaseGrid.prototype.alModificar = function(rowIndex, colIndex, oldValue, newValue) {};
DatabaseGrid.prototype.alAgregar = function() {};


DatabaseGrid.prototype.mostrarBajas = function(mostrar) {
    this._mostrarBajas = mostrar;
    this.fetchGrid();
}

DatabaseGrid.prototype.mostrarSeleccion = function(mostrar) {
    this._mostrarSeleccion = mostrar;
    this.fetchGrid();
}

DatabaseGrid.prototype.fetchGrid = function()  {
    str = "interfaz/grilla/json-consultar.php?bd=" + this.bd + "&tabla=" + this.tabla + "&from=" + this.from + 
        "&seleccion=" + this._mostrarSeleccion + "&bajas=" + this._mostrarBajas;
	this.editableGrid.loadJSON(str);
};

DatabaseGrid.prototype.customRenderer = function() {};

DatabaseGrid.prototype.initializeGrid = function(grid) {
    var self = this;
	grid.setCellRenderer("action", new CellRenderer({ 
		render: function(cell, id) {                 
            cell.innerHTML+= "<input type=\"radio\" name=\"" + self.contenedor + "Action\" onclick=\"grdCurrentID = " + id + ";\" />";
		}
	})); 
	this.customRenderer();
    grid.renderGrid(this.contenedor, "grilla");
};    

DatabaseGrid.prototype.deleteRow = function(id) 
{
    var self = this;
    if ((!id) || (id < 1)) return;
    if (confirm('¿Está seguro que desea marcar la fila indicada como eliminada?')  ) {

        $.ajax({
    		url: 'interfaz/grilla/json-eliminar.php',
    		type: 'POST',
    		dataType: "html",
    		data: {
    			bd : self.bd,
    			tabla : self.tabla,
    			id: id, 
    			idUsuario: self.usuarioID 
    		},
    		success: function (response) 
    		{ 
    			if (response == "ok" ) {
    		        self.editableGrid.removeRow(id);
                    self.alEliminar(); 
    			}
                grdCurrentID = 0;
    		},
    		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
    		async: true
    	});
    }
}; 

DatabaseGrid.prototype.deleteSelectedRow = function() 
{
    this.deleteRow(grdCurrentID);
}; 

DatabaseGrid.prototype.addRow = function() 
{
    var self = this;
    $.ajax({
		url: 'interfaz/grilla/json-agregar.php',
		type: 'POST',
		dataType: "html",
		data: {
			bd : self.bd,
			tabla : self.tabla,
			idUsuario: self.usuarioID 
		},
		success: function (response) 
		{ 
			if (response == "ok" ) {
                self.fetchGrid();
                self.alAgregar(); 
			}
            else 
                alert("error");
		},
		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
		async: true
	});
}; 

function updatePaginator(grid, divId)
{
    divId = divId || "paginator";
	var paginator = $("#" + divId).empty();
	var nbPages = grid.getPageCount();

	var interval = grid.getSlidingPageInterval(20);
	if (interval == null) return;
	
	var pages = grid.getPagesInInterval(interval, function(pageIndex, isCurrent) {
		if (isCurrent) return "<span id='currentpageindex'>" + (pageIndex + 1)  +"</span>";
		return $("<a>").css("cursor", "pointer").html(pageIndex + 1).click(function(event) { grid.setPageIndex(parseInt($(this).html()) - 1); });
	});
		
	var link = $("<a class='nobg'>").html("<i class='fa fa-fast-backward'></i>");
	if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.firstPage(); });
	paginator.append(link);

	link = $("<a class='nobg'>").html("<i class='fa fa-backward'></i>");
	if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.prevPage(); });
	paginator.append(link);

	for (p = 0; p < pages.length; p++) paginator.append(pages[p]).append(" ");
	
	link = $("<a class='nobg'>").html("<i class='fa fa-forward'>");
	if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.nextPage(); });
	paginator.append(link);

	link = $("<a class='nobg'>").html("<i class='fa fa-fast-forward'>");
	if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.lastPage(); });
	paginator.append(link);
}; 
