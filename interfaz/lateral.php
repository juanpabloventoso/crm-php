    <div id="divLateral">
        <br />
        <br />
        <ul>
            <li<?php if ((basename($_SERVER["SCRIPT_FILENAME"]) == 'principal.php') || (basename($_SERVER["SCRIPT_FILENAME"]) == 'index.php')) { ?> class="current"<?php } ?>><a href="principal.php">Principal</a><span></span></li>
        </ul>
        <ul>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'productos.php') { ?> class="current"<?php } ?>><a href="productos.php">Productos y servicios</a><span></span></li>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'usuarios.php') { ?> class="current"<?php } ?>><a href="usuarios.php">Usuarios</a><span></span></li>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'clientes.php') { ?> class="current"<?php } ?>><a href="clientes.php">Clientes</a><span></span></li>
        </ul>
        <ul>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'perfiles.php') { ?> class="current"<?php } ?>><a href="perfiles.php">Perfiles</a><span></span></li>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'origenes.php') { ?> class="current"<?php } ?>><a href="origenes.php">Orígenes</a><span></span></li>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'estados.php') { ?> class="current"<?php } ?>><a href="estados.php">Estados</a><span></span></li>
        </ul>
        <ul>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'consultas.php') { ?> class="current"<?php } ?>><a href="consultas.php">Consultas</a><span></span></li>
        </ul>
        <ul>
            <li<?php if (basename($_SERVER["SCRIPT_FILENAME"]) == 'opciones.php') { ?> class="current"<?php } ?>><a href="opciones.pghp">Opciones</a><span></span></li>
        </ul>
    </div>