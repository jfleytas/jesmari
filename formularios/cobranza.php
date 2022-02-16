<?php
    $page_name="cobranza.php"; 
    
    header("Refresh:10; url=$page_name");

    require '../tablas/menu.php';
    
    require '../clases/formularios/cobranza.class.php';
    $cobranza = cobranza::singleton();

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

    $query = "select CR.nr, CR.fecha_cobranza, C.razon_social descripcion_cliente, M.descripcion moneda_descripcion, CR.total_pago 
    from cabecera_cobranza CR join clientes C on CR.nr_cliente = C.nr join moneda M on CR.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $cobranza -> rowCount($query);
    
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
        case 'fecha_cobranza':  
            $order_by = 'fecha_cobranza';  
        break;  
        case 'descripcion_cliente':  
            $order_by = 'descripcion_cliente';  
        break;
        case 'moneda_descripcion':  
            $order_by = 'moneda_descripcion';  
        break;   
        case 'total_pago':  
            $order_by = 'total_pago';  
        break;      
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $cobranza->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $cobranza->get_cobranza("select * from cabecera_cobranza order by nr");//We get the desired result from the table
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
<form class="form-horizontal" action= "cobranza.php" method="POST" name = "cobranza">
<h2>Recibos</h2>
<!-- <a href="cobranza_form.php" onclick="pop_up(this);" class = "add">Nuevo</a> -->
<a target="_blank" href="cobranza_form.php" class = "add">Nuevo</a>
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
              	echo '<th><a href="?sort=nr&sort_by='.$sort_order.'">Nr. Orden</th>';
                echo '<th><a href="?sort=fecha_cobranza&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=descripcion_cliente&sort_by='.$sort_order.'">Cliente</th>';
               	echo '<th><a href="?sort=moneda_descripcion&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th><a href="?sort=total_pago&sort_by='.$sort_order.'">Total Pago</th>';                
                echo '<th>Eliminar</th>';
                echo '<th>Imprimir</th>';
            echo '</tr>';
        echo '</thead>';
    	echo '<tbody>';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $orden = $total_row['nr'];
                echo '<td>'.$orden.'</td>';
                //echo '<td>'. $orden . '</td>';
                $fecha_cobranza = date_format(date_create($total_row['fecha_cobranza']),"d/m/Y");
                echo '<td>'. $fecha_cobranza . '</td>';
                echo '<td>'. $total_row['descripcion_cliente'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_pago = number_format(floatval($total_row['total_pago']),2,",",".");
                echo '<td class="monto">'. $total_pago . '</td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado('.$orden.')" href="'.$page_name.'?delete='.$orden.'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_cobranza.php?recibo='.$orden.'">Imprimir</a></td>';
                echo '</tr>';
                endforeach;
            }else{
                echo '<div class = "no_record">No se encontraron registros</div>';
            }
        echo '</tbody>';
	echo '</table>';
?>
</center>
</div> 
    <script language="Javascript">
        function confirmarBorrado(value)
        {
            var orden = value;
            eliminar=confirm("¿Deseas eliminar el cobranza Nro. " + orden +"?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "cobranza.php";
        }
    </script>
</form>
</body>
</html>


<?php
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $cobranza->delete_cobranza($nr);
    }
?>