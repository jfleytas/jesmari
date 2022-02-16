<?php
    $page_name="transferencia_stock.php"; 

    header("Refresh:10; url=$page_name");

    require '../tablas/menu.php';
    
    require '../clases/formularios/transferencia_stock.class.php';
    $transferencia_stock = transferencia_stock::singleton();

    //Declaring variables
    $orden=isset($_POST['orden']) ? $_POST['orden'] : '';
    
    //Pagination query
    //$limit=$_POST['limit']; // Read the limit value from query string.
    $limit=(isset($_POST['limit'])) ? $_POST['limit'] : 5; //If limit value is not available then let us use a default value
    
    // If there is a selection or value of limit then the list box should show that value , so we have to lock that options
    if(isset($_GET['limit']))
    {
    switch($limit)
    {
        case 10:
        $select5="";
        $select10="selected";
        $select50="";
        break;

        case 50:
        $select5="";
        $select10="";
        $select50="selected";
        break;

        case 5:
        $select5="selected";
        $select10="";
        $select50="";
        break;
    }
    } 

    @$start=$_GET['start'];

    // You can keep the below line inside the above form, if you want when user selection of number of 
    // records per page changes, it should not return to first page. 
    echo '<input type=hidden name=start value=$start>';

    $offset = ($start - 0); 

    $actual = $offset + $limit; 
    $back = $offset - $limit; 
    $next = $offset + $limit; 

    $query = "select TS.nr, TS.fecha_transferencia, TS.nr_sucursal, S.descripcion descripcion_sucursal, TS.nr_deposito_origen, DS1.descripcion deposito_origen, TS.nr_deposito_destino, DS2.descripcion deposito_destino, TS.total_transferencia 
    from cabecera_transferencia_stock TS full join sucursal S on TS.nr_sucursal = S.nr join depositos_stock DS1 on TS.nr_deposito_origen = DS1.nr join depositos_stock DS2 on TS.nr_deposito_destino = DS2.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $transferencia_stock -> rowCount($query);
    
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'nr';  
              
    $sort_order = 'desc';  
    if(isset($_GET['sort_by']))  
    {  
        if($_GET['sort_by'] == 'asc')  
        {  
            $sort_order = 'desc';  
        } else{  
            $sort_order = 'asc';  
        }  
    }  
              
    switch($sort)  
    {  
        case 'nr':  
            $order_by = 'nr';  
        break; 
        case 'fecha_transferencia':  
            $order_by = 'fecha_transferencia';  
        break;  
        case 'descripcion_sucursal':  
            $order_by = 'descripcion_sucursal';  
        break;
        case 'deposito_origen':  
            $order_by = 'deposito_origen';  
        break;   
         case 'deposito_destino':  
            $order_by = 'deposito_destino';  
        break; 
        case 'total_transferencia':  
            $order_by = 'total_transferencia';  
        break;      
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $transferencia_stock->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $transferencia_stock->get_transferencia_stock("select * from cabecera_transferencia_stock order by nr");//We get the desired result from the table
        foreach($result as $row):
            /*$nr = $row['nr'];
            $fecha = $row['fecha'];
            $cotizacion_compra = $row['cotizacion_compra'];
            $cotizacion_venta = $row['cotizacion_venta'];
            $nr_moneda = $row['nr_moneda'];*/
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<!--<link rel="stylesheet" type="text/css" href="../css/estilo.pagination.css" />-->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action= "transferencia_stock.php" method="POST" name = "transferencia_stock" autocomplete = "off">
<h2>Transferencia Stock</h2>
<script>
    
</script>
<!-- <a href="transferencia_stock_form.php" onclick="pop_up(this);" class = "add">Nuevo</a> -->
<a target="_blank" href="transferencia_stock_form.php" class = "add">Nuevo</a>
<center>
    <!--<p class="left-style">Nro. de registros por pag.:<select name="limit" id= "limit" onchange="this.form.submit();">
        <?php 
        echo '<option value="5"'.$select5.'>5</option>';
        echo '<option value="10"'.$select10.'>10</option>';
        echo '<option value="50"'.$select50.'>50</option>';
        ?>
    </select>
    Busqueda: 
    <input type="text" id="searchstring" name="searchstring" class="right-style" placeholder="Busqueda" value="<?php echo $searchstring ?>">
    <input type="submit" id="search" name="search" value="Buscar"></p>
-->
<div>
<?php
    echo '<table class="list">';
	   echo '<thead>';
            echo '<tr>';
              	echo '<th><a href="?sort=nr&sort_by='.$sort_order.'">Nr. Transferencia</th>';
                echo '<th><a href="?sort=fecha_transferencia&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=descripcion_sucursal&sort_by='.$sort_order.'">Sucursal</th>';
               	echo '<th><a href="?sort=deposito_origen&sort_by='.$sort_order.'">Deposito Stock Origen</th>';
                echo '<th><a href="?sort=deposito_destino&sort_by='.$sort_order.'">Deposito Stock Destino</th>';
                echo '<th><a href="?sort=total_transferencia&sort_by='.$sort_order.'">Total General </th>';             
                echo '<th>Eliminar</th>';
                echo '<th>Imprimir</th>';
            echo '</tr>';
        echo '</thead>';
    	echo '<tbody>';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $transferencia = $total_row['nr'];
                echo '<td>'. $transferencia . '</td>';
                $fecha_transferencia = date_format(date_create($total_row['fecha_transferencia']),"d/m/Y");
                echo '<td>'. $fecha_transferencia . '</td>';
                echo '<td>'. $total_row['descripcion_sucursal'] . '</td>';
                echo '<td>'. $total_row['deposito_origen'] . '</td>';
                echo '<td>'. $total_row['deposito_destino'] . '</td>';
                $total_transferencia = number_format(floatval($total_row['total_transferencia']),2,",",".");
                echo '<td class="monto">'. $total_transferencia . '</td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado('.$transferencia.')" href="'.$page_name.'?delete='.$transferencia.'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_transferencia_stock.php?transferencia_stock='.$transferencia.'">Imprimir</a></td>';
                echo '</tr>';
                endforeach;
            }else{
                echo '<div class = "no_record">No se encontraron registros</div>';
            }
        echo '</tbody>';
	echo '</table>';

    //Pagination
    /*echo "<table align = 'center' width='50%'><tr><td  align='left' width='30%'>";
    //If our variable $back is equal to 0 or more then only we will display the link to move back
    if($back >=0) 
    { 
        print "<a href='?start=$back&limit=$limit'><font face='Verdana' size='2'>ANT.</font></a>"; 
    } 
    //Let us display the page links at  center. We will not display the current page as a link
    echo "</td><td align=center width='30%'>";
    $i=0;
    $l=1;
    for($i=0;$i < $qty_register;$i=$i+$limit)
    {
        if($i <> $offset)
        {
            echo " <a href='?start=$i&limit=$limit'><font face='Verdana' size='2'>$l</font></a> ";
        }else{ 
            echo "<font face='Verdana' size='4' color=red>$l</font>";
        }//Current page is not displayed as link and given font color red
        $l=$l+1;
    }

    echo "</td><td  align='right' width='30%'>";
    //If we are not in the last page then Next link will be displayed. Here we check that
    if($actual < $qty_register)
    { 
        print "<a href='?start=$next&limit=$limit'><font face='Verdana' size='2'>SIG.</font></a>";
    } 
    echo "</td></tr></table>";*/
?>
</center>
</div> 
    <script language="Javascript">
        function confirmarBorrado(value)
        {
            var transferencia = value;
            eliminar=confirm("¿Deseas eliminar la Transferencia de Stock Nro. " + transferencia +"?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "transferencia_stock.php";
        }
    </script>
</form>
</body>
</html>


<?php
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $transferencia_stock->delete_transferencia_stock($nr);
    }
?>