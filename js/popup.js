function crmPopup(contenedor, iniciarVisible) 
{
    var _self = this;
    $(document).ready(function() {
        _self.contenedor = document.getElementById(contenedor);
        _self.contenedor.className = "popup";
        _self.bg = document.getElementById("crmPopupBg");
        if (!_self.bg) {
            var bg = document.createElement("div");
            bg.id = "crmPopupBg";
            bg.className = "popup-bg";
            document.body.appendChild(bg);
            _self.bg = bg;
        }
        if (iniciarVisible) _self.mostrar();
    });
}

crmPopup.prototype.mostrar = function() {
    $(this.bg).fadeIn(500);
    $(this.contenedor).fadeIn(200);
}

crmPopup.prototype.ocultar = function() {
    $(this.contenedor).fadeOut(200);
    $(this.bg).fadeOut(500);
}

