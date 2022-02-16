<?php
    require '../tablas/menu.php';
    
    require '../clases/formularios/factura_venta.class.php';
    $factura_venta = factura_venta::singleton();

    $page_name="listado_factura_venta_p.php"; 

    $fecha_inicio=isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin=isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_informes.css" />
</head>
<body>
<form class="form-horizontal" action= "listado_factura_venta_p.php" method="POST" name = "listado_factura_venta_p" autocomplete="off">
<h2>Listado Factura Venta</h2>

<center>
    <b><label class="control-label">Rango de Fechas:</label></b><p><p>

    <b><label class="control-label">Fecha Inicio</label></b>
    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>
    <b><label class="control-label">Fecha Fin</label></b>
    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>
    <p align="center"><input type="submit" class="btn-success" id="action" name="action" value ="Listar">

    <?php
        if (isset($_POST['action']))
        {
            @$fecha_inicio = $_POST['fecha_inicio'];
            @$fecha_fin = $_POST['fecha_fin'];
            echo '<script>window.location.assign("listado_factura_venta.php?fecha_inicio='.$fecha_inicio.'&fecha_fin='.$fecha_fin.'")</script>';
        }
    ?>
</center>
</form>
</body>
</html>