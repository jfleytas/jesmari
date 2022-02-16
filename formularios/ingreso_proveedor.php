<?php
    $page_name="ingreso_proveedor.php"; 

    header("Refresh:10; url=$page_name");

    require '../tablas/menu.php';
    
    require '../clases/formularios/ingreso_proveedor.class.php';
    $ingreso_proveedor = ingreso_proveedor::singleton();

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

    $query = "select IP.nr, IP.orden_compra, IP.fecha_ingreso, P.descripcion descripcion_proveedor, M.descripcion moneda_descripcion, total_ingreso from cabecera_ingreso_proveedor IP join proveedor P on IP.nr_proveedor = P.nr join moneda M on IP.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $ingreso_proveedor -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $ingreso_proveedor->param = "&search=$searchstring";
        $query = $query . " WHERE nr LIKE '%$searchstring%' OR nr_proveedor LIKE '%$searchstring%'";
        $qty_register = $ingreso_proveedor -> rowCount($query);
    }
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
        case 'fecha_ingreso':  
            $order_by = 'fecha_ingreso';  
        break;  
        case 'descripcion_proveedor':  
            $order_by = 'descripcion_proveedor';  
        break;
        case 'moneda_descripcion':  
            $order_by = 'moneda_descripcion';  
        break;   
        case 'total_ingreso':  
            $order_by = 'total_ingreso';  
        break;      
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $ingreso_proveedor->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $ingreso_proveedor->get_ingreso_proveedor("select * from cabecera_ingreso_proveedor");//We get the desired result from the table
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
<!-- <link rel="stylesheet" type="text/css" href="../css/estilo.pagination.css" /> -->
</head>
<body>
<form class="form-horizontal" action= "ingreso_proveedor.php" method="POST" name = "ingreso_proveedor">
<h2>Ingreso Proveedor</h2>
<script>
    function pop_up(url){
        window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no') 
    }
</script>
<!-- <a href="ingreso_proveedor_form.php" onclick="pop_up(this);" class = "add">Nuevo</a> 
<a target="_blank" href="ingreso_proveedor_form.php" class = "add">Nuevo</a>-->
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
                echo '<th><a href="?sort=nr&sort_by='.$sort_order.'">Nr. Ingreso</th>';
                echo '<th><a href="?sort=nr&sort_by='.$sort_order.'">Nr. Orden Compra</th>';
                echo '<th><a href="?sort=fecha_ingreso&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=descripcion_proveedor&sort_by='.$sort_order.'">Proveedor</th>';
                echo '<th><a href="?sort=moneda_descripcion&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th><a href="?sort=total_ingreso&sort_by='.$sort_order.'">Total General</th>';
                echo '<th>Eliminar</th>';
                echo '<th>Imprimir</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                echo '<td>'. $total_row['nr'] . '</td>';
                echo '<td>'. $total_row['orden_compra'] . '</td>';
                $fecha_orden = date_format(date_create($total_row['fecha_ingreso']),"d/m/Y");
                echo '<td>'. $fecha_orden . '</td>';
                echo '<td>'. $total_row['descripcion_proveedor'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_ingreso = number_format(floatval($total_row['total_ingreso']),2,",",".");
                echo '<td class="monto">'. $total_ingreso . '</td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado()" href="'.$page_name.'?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_ingreso_proveedor.php?ingreso_proveedor='.$total_row['nr'].'">Imprimir</a></td>';
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
        function confirmarBorrado()
        {
            eliminar=confirm("¿Deseas eliminar este Ingreso Proveedor?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "ingreso_proveedor.php";
        }
    </script>
</form>
</body>
</html>


<?php
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $ingreso_proveedor->delete_ingreso_proveedor($nr);
    }
?>