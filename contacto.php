
<?php require_once('nucleo/nucleo.php'); ?>

<br />
<br />
<div style="text-align: center;">
<div style="margin: 0 auto; width: 400px; border: solid 1px #ccc; background: #f1f1f1; padding: 10px;">
    <input type="text" id="nombre" name="nombre" placeholder="Nombre" style="width: 300px" value="Alberto" />
    <br />
    <br />
    <input type="email" id="correo" name="correo" placeholder="Correo electrónico" style="width: 300px" value="alberto@nisman.com" />
    <br />
    <br />
    <input type="phone" id="telefono" name="telefono" placeholder="Teléfono" style="width: 300px" value="4200-5544" />
    <br />
    <br />
    <textarea id="descripcion" name="descripcion" placeholder="Comentarios" style="width: 300px; height: 100px">Ninguno en particular</textarea>
    <br />
    <br />
    <input type="button" value="Enviar" onclick="crmEnviarPedido()" />
    
    
    <script type="text/javascript">
    function crmEnviarPedido() {
        var url     = "nucleo/json-nucleo.php";
        var params  = "operacion=1&" +
        "nombre="      + document.getElementById("nombre").value + "&" +
        "correo="      + document.getElementById("correo").value + "&" + 
        "telefono="    + document.getElementById("telefono").value + "&" +
        "pedido="      + document.getElementById("descripcion").value;
        var http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(params);
    }
    </script>
    
    
</div>
</div>