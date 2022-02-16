<?php
    require '../tablas/menu.php';

    require '../clases/tablas/configuracion.class.php';
    $configuracion = configuracion::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $cantidad_f_venta=isset($_POST['cantidad_f_venta']) ? $_POST['cantidad_f_venta'] : '';
    $cantidad_f_compra=isset($_POST['cantidad_f_compra']) ? $_POST['cantidad_f_compra'] : '';
    $cantidad_recibo=isset($_POST['cantidad_recibo']) ? $_POST['cantidad_recibo'] : '';
    $deposito_stock_defecto=isset($_POST['deposito_stock_defecto']) ? $_POST['deposito_stock_defecto'] : '';
    $moneda_defecto=isset($_POST['moneda_defecto']) ? $_POST['moneda_defecto'] : '';
    $nombre_empresa=isset($_POST['nombre_empresa']) ? $_POST['nombre_empresa'] : '';

    $query = "select * from configuracion";
 
    $result = $configuracion->get_configuracion($query);//We get all the results from the table
    foreach($result as $row):
        $nr = $row['nr'];
        $nombre_empresa = $row['nombre_empresa'];
        $cantidad_f_venta = $row['cantidad_f_venta'];
        $cantidad_f_compra = $row['cantidad_f_compra'];
        $cantidad_recibo = $row['cantidad_recibo'];
        $deposito_stock_defecto = $row['deposito_stock_defecto'];
        $moneda_defecto = $row['moneda_defecto'];
    endforeach;
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
</head>
<body>
<form class="form-horizontal" action="configuracion.php" method="POST" name = "configuracion" autocomplete = "off">
<h2>Configuraciones Generales</h2>
	<div class="container">
        <div class="span10 offset1">
            <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
            <b><label class="control-label">Nombre Empresa</label></b>
            <input type="text" id="nombre_empresa" name="nombre_empresa" value="<?php echo $nombre_empresa;?>" class="boxes"></p>
            <b><label class="control-label">Cantidad por Facturas Venta</label></b>
            <input type="number" id="cantidad_f_venta" name="cantidad_f_venta" value="<?php echo $cantidad_f_venta;?>" class="boxes" min = "1" required><p>
            <b><label class="control-label">Cantidad por Facturas Compra</label></b>
            <input type="number" id="cantidad_f_compra" name="cantidad_f_compra" value="<?php echo $cantidad_f_compra;?>" class="boxes" min= "1" required><p>
            <b><label class="control-label">Cantidad por Recibo</label></b>
            <input type="number" id="cantidad_recibo" name="cantidad_recibo" value="<?php echo $cantidad_recibo;?>" class="boxes" min= "1" required><p>
            <b><label class="control-label">Deposito de Stock por defecto</label></b>
            <input type="number" id="deposito_stock_defecto" name="deposito_stock_defecto" value="<?php echo $deposito_stock_defecto;?>" class="boxes" min= "1" required><p>
            <b><label class="control-label">Moneda por defecto</label></b>
            <select name="moneda_defecto" required class="boxes">
                <?php 
                    $moneda_result = $configuracion->get_configuracion("select * from moneda");//We get all the results from the table Banco
                    foreach ($moneda_result as $row) {
                        $moneda_nr = $row['nr'];    
                        echo '<option value="'.$row['nr'].'"';
                        if ($moneda_defecto==$moneda_nr) echo 'selected="selected"';
                        echo '>'.$row['descripcion'].'</option>';
                    }?>
            </select><p>
            <div class="form-actions">
                <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Guardar">
                <a class="btn" href="menu.php">Cancelar</a>
            </div>
    	</div>
    </div>
</form>
</body>
</html>


<?php
    if (isset($_POST['action'])){
        if ($_POST['action'] == "Guardar")
        {
            $nr = $_POST['nr'];
            $cantidad_f_venta = $_POST['cantidad_f_venta'];
            if(empty($cantidad_f_venta)) 
            {
                $cantidad_f_venta = 10;
            }
            $cantidad_f_compra = $_POST['cantidad_f_compra'];
            if(empty($cantidad_f_compra)) 
            {
                $cantidad_f_compra = 10;
            }
            $cantidad_recibo = $_POST['cantidad_recibo'];
            if(empty($cantidad_recibo)) 
            {
                $cantidad_recibo = 10;
            }  
            $deposito_stock_defecto = $_POST['deposito_stock_defecto'];
            if(empty($deposito_stock_defecto)) 
            {
                $deposito_stock_defecto = 1;
            }
            $moneda_defecto = $_POST['moneda_defecto'];
            if(empty($moneda_defecto)) 
            {
                $moneda_defecto = 1;
            }
            $nombre_empresa = $_POST['nombre_empresa'];
            if(empty($nombre_empresa)) 
            {
                $nombre_empresa = '';
            }    
            
            $configuracion->update_configuracion($nr,$cantidad_f_venta,$cantidad_f_compra,$cantidad_recibo,$deposito_stock_defecto,$moneda_defecto,$nombre_empresa);
            echo "<script>window.location.href = 'configuracion.php'</script>";
        }
    }
?>