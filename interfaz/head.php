<?php if (!usuarioLogueado()) header("location: login.php"); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="MSThemeCompatible" content="Yes" />
    <link rel="icon" type="image/png" href="<?= $gAppUrlBase ?>/favicon.png" />
    <meta name="robots" content="noindex" />
    <meta name="twitter:site" content="@<?= $gRedesTwitter ?>" />
    <meta name="twitter:creator" content="@<?= $gRedesTwitter ?>" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta property="og:type" content="Application" />
    <meta name="viewport" content="width=1030" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= $gAppUrlBase ?>/favicon.ico" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css" />
    <link href="<?= $gAppUrlBase ?>/css/base.css?v=2" rel="stylesheet" type="text/css" />
    <title><?= $gPaginaTitulo ?> | <?= $gAppNombre ?></title>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid_renderers.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid_editors.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid_validators.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid_utils.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/editablegrid_charts.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/datagrid.js"></script>
    <script type="text/javascript" src="<?= $gAppUrlBase ?>/js/popup.js"></script>
</head>
<body>
<script>
 $.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '<Ant',
 nextText: 'Sig>',
 currentText: 'Hoy',
 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
 dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
 weekHeader: 'Sm',
 dateFormat: 'dd/mm/yy',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };
 $.datepicker.setDefaults($.datepicker.regional['es']);
$(function () {
$("#fecha").datepicker();
});
</script>
