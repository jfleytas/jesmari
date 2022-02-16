<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/cotizacion.class.php';
    $cotizacion = cotizacion::singleton();

    $page_name="cotizacion.php"; 

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $fecha=isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : '';
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';

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

    $query = "select C.nr nr, C.fecha fecha, C.cotizacion_compra cotizacion_compra, C.cotizacion_venta cotizacion_venta, C.nr_moneda nr_moneda_cotizacion, M.nr nr_moneda, M.descripcion moneda_descripcion from cotizacion C join moneda M on C.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $cotizacion -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $cotizacion->param = "&search=$searchstring";
        $query = $query . " WHERE fecha LIKE '%$searchstring%' OR moneda_descripcion LIKE '%$searchstring%'";
        $qty_register = $cotizacion -> rowCount($query);
    }
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'Fecha';  
              
    $sort_order = 'asc';  
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
        case 'Fecha':  
            $order_by = 'fecha';  
        break;  
        case 'moneda_descripcion':  
            $order_by = 'moneda_descripcion';  
        break;
        case 'cotizacion_compra':  
            $order_by = 'cotizacion_compra';  
        break;   
        case 'cotizacion_venta':  
            $order_by = 'cotizacion_venta';  
        break;      
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $cotizacion->get_cotizacion($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $cotizacion->get_cotizacion("select C.nr nr, C.fecha fecha, C.cotizacion_compra cotizacion_compra, C.cotizacion_venta cotizacion_venta, C.nr_moneda nr_moneda_cotizacion, M.nr nr_moneda, M.descripcion moneda_descripcion from cotizacion C join moneda M on C.nr_moneda = M.nr where C.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $fecha = $row['fecha'];
            $cotizacion_compra = $row['cotizacion_compra'];
            $cotizacion_venta = $row['cotizacion_venta'];
            $nr_moneda = $row['nr_moneda'];
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<link rel="stylesheet" type="text/css" href="../css/estilo.pagination.css" />
</head>
<body>
<form class="form-horizontal" action= "cotizacion.php" method="POST" name = "cotizacion" autocomplete = "off">
<h2>Cotizacion</h2>
<a href="#modal" class = "add">Nuevo</a>
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
              	echo '<th><a href="?sort=Fecha&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=moneda_descripcion&sort_by='.$sort_order.'">Moneda</th>';
               	echo '<th><a href="?sort=cotizacion_compra&sort_by='.$sort_order.'">Cotizacion Compra</th>';
                echo '<th><a href="?sort=cotizacion_venta&sort_by='.$sort_order.'">Cotizacion Venta</th>';
              	echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
    	echo '<tbody>';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $fecha1 = date_format(date_create($total_row['fecha']),"d/m/Y");
                echo '<td>'. $fecha1 . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $valor_compra = number_format($total_row['cotizacion_compra'],2,",",".");
                echo '<td>'. $valor_compra . '</td>';
                $valor_venta = number_format($total_row['cotizacion_venta'],2,",",".");
                echo '<td>'. $valor_venta . '</td>';
                echo '<td class = "buttons-column"><a href="'.$page_name.'?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado()" href="'.$page_name.'?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
	<div id="modal" class="modalstyle">
		<div class="modalbox movedown">
            <caption align="center">Cotizacion</caption><hr><p>
			<a href="cotizacion.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Fecha</label></b>
                    <?php if (!empty($nr)){
                        $fecha = date('Y-m-d', strtotime($fecha));
                    ?>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha;?>" class="boxes" required><p>
                    <?php }else{ ?>
                        <input type="date" id="fecha" name="fecha" value="<?php echo date("Y-m-d");?>" class="boxes" required>
                        <?php
                            //@$formato_fecha=string date ( string $fecha [, int $timestamp = time() ] );
                            @$formato_fecha= strtotime($fecha);
                            //@$formato_fecha=date("Y-m-d H:i:s",time());
                            $fecha2 = $formato_fecha;
                            echo $fecha2;
                        ?><p>
                    <?php }?>
                    <b><label class="control-label">Moneda</label></b>
                    <select name="nr_moneda" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $moneda_result = $cotizacion->get_monedas();//We get all the results from the table
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_moneda==$moneda_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Cotizacion Compra</label></b>
                    <input type="number" id="cotizacion_compra" name="cotizacion_compra" min="0" step="any" value="<?php echo $cotizacion_compra;?>" class="boxes" required><p>
                    <b><label class="control-label">Cotizacion Venta</label></b>
                    <input type="number" id="cotizacion_venta" name="cotizacion_venta" min="0" step="any" value="<?php echo $cotizacion_venta;?>" class="boxes" required><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="cotizacion.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="cotizacion.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }?>
                    </div>
                    </form>
                </div>
            </div> 
		</div>
	</div>

    <script language="Javascript">
        function confirmarBorrado()
        {
            eliminar=confirm("¿Deseas eliminar este registro?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "cotizacion.php";
        }
    </script>

</form>
</body>
</html>


<?php
    if (isset ($_POST['action']))
    {
        switch($_POST['action'])
        {
            case "Crear": 
                $fecha = $_POST['fecha'];
                $cotizacion_compra = $_POST['cotizacion_compra'];
                $cotizacion_venta = $_POST['cotizacion_venta'];
                $nr_moneda = $_POST['nr_moneda'];

                $cotizacion->insert_cotizacion($fecha,$cotizacion_compra,$cotizacion_venta,$nr_moneda);
            break;

            case "Modificar": 
                $nr = $_POST['nr'];
                $fecha = $_POST['fecha'];
                $cotizacion_compra = $_POST['cotizacion_compra'];
                $cotizacion_venta = $_POST['cotizacion_venta'];
                $nr_moneda = $_POST['nr_moneda'];

                $cotizacion->update_cotizacion($nr,$fecha,$cotizacion_compra,$cotizacion_venta,$nr_moneda);
                echo "<script>window.location.href = 'cotizacion.php'</script>";
            break;
        }
    }
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $cotizacion->delete_cotizacion($nr);
    }
?>