<?php
    require '../tablas/menu.php';

    require '../clases/tablas/productos.class.php';
    $productos = productos::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $producto_desde=isset($_POST['producto_desde']) ? $_POST['producto_desde'] : '';
    $producto_hasta=isset($_POST['producto_hasta']) ? $_POST['producto_hasta'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $nr_deposito=isset($_POST['nr_deposito']) ? $_POST['nr_deposito'] : ''; 
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action="listado_stock.php" method="POST" name = "listado_stock">
<h2>Listado de Stock</h2>
    <b><label class="control-label">Codigo Producto</label></b>
    <input type="text" id="nr_producto" name="nr_producto" value="<?php echo $nr_producto;?>" class="boxes" autofocus><p>
    <input type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"><p>
                   
    <b><label class="control-label">Sucursal</label></b>
    <select name="nr_marca" class="boxes">
        <option value="" selected>Todos</option>
        <?php 
            $sucursal_result = $productos->query("select * from sucursal order by descripcion");//Execute the query
            foreach ($sucursal_result as $row) {
                $sucursal_nr = $row['nr'];
                echo '<option value="'.$row['nr'].'"';
                if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                    echo '>'.$row['descripcion'].'</option>';
                }?>
    </select><p>

    <b><label class="control-label">Deposito Stock</label></b>
    <select name="nr_deposito" class="boxes">
        <option value="" selected>Todos</option>
        <?php 
            $deposito_result = $productos->query("select * from depositos_stock order by descripcion");//Execute the query
            foreach ($deposito_result as $row) {
                $deposito_nr = $row['nr'];
                echo '<option value="'.$row['nr'].'"';
                if ($nr_deposito==$deposito_nr) echo 'selected="selected"';
                    echo '>'.$row['descripcion'].'</option>';
                }?>
    </select><p>

    <div class="form-actions">
        <p><p><input type="submit" class="btn-success" id="ejecutar" name="ejecutar" value ="Ejecutar">
    </div>

</form>
</body>
</html>
<?php
    if (isset($_POST['action'])){
        switch($_POST['action'])
        {
            case "Ejecutar": 
                $query = "select * from stock_deposito_sucursal "
                $nr_producto = $_POST['nr_producto'];
                if(!empty($nr_producto)) 
                {
                    $query = $query . "where nr_producto = '$nr_producto'"
                }
                $id_producto = $_POST['id_producto'];
                if(!empty($id_producto)) 
                {
                   $query = $query . "AND id_producto = '$id_producto'"
                }
                $nr_sucursal = $_POST['nr_sucursal'];
                if(!empty($nr_sucursal)) 
                {
                    $query = $query . "AND nr_sucursal = '$nr_sucursal'"
                }
                $nr_deposito = $_POST['nr_deposito'];
                if(empty($nr_deposito)) 
                {
                    $query = $query . "AND nr_deposito = '$nr_deposito'"
                }
                $qty_register = $productos -> rowCount($query);
                $listado_stock_result = $productos->query($query);//We get all the results from the table

                echo '<table class="list">';
               echo '<thead>';
                    echo '<tr>';
                        echo '<th>Producto</th>';
                        echo '<th>Sucursal</th>';
                        echo '<th>Deposito</th>';
                        echo '<th>Stock Actual</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                //Check if more than 0 records were found
                if($qty_register > 0){
                    foreach($listado_stock_result as $total_row):
                    echo '<tr>';
                    echo '<td>'. $total_row['nr_producto'] . '</td>';
                    echo '<td>'. $total_row['nr_deposito'] . '</td>';
                    echo '<td>'. $total_row['nr_sucursal'] . '</td>';
                    echo '<td>'. $total_row['stock_actual'] . '</td>';
                    echo '</tr>';
                    endforeach;
                }else{
                        echo '<div class = "no_record">No se encontraron resultados</div>';
                    }
                 echo '</tbody>';
            echo '</table>';
             break;
        }
    }
?>