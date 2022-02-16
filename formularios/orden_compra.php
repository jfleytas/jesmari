<?php
    $page_name="orden_compra.php"; 

    //header("Refresh:10; url=$page_name");

    require '../tablas/menu.php';
    
    require '../clases/formularios/orden_compra.class.php';
    $orden_compra = orden_compra::singleton();

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

    $query = "select OC.nr, OC.fecha_orden, P.descripcion descripcion_proveedor, M.descripcion moneda_descripcion, OC.total_orden 
    from cabecera_orden_compra OC join proveedor P on OC.nr_proveedor = P.nr join moneda M on OC.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $orden_compra -> rowCount($query);
    
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
        case 'fecha_orden':  
            $order_by = 'fecha_orden';  
        break;  
        case 'descripcion_proveedor':  
            $order_by = 'descripcion_proveedor';  
        break;
        case 'moneda_descripcion':  
            $order_by = 'moneda_descripcion';  
        break;   
        case 'total_factura':  
            $order_by = 'total_factura';  
        break;      
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $orden_compra->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $orden_compra->get_orden_compra("select * from cabecera_orden_compra order by nr");//We get the desired result from the table
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
<form class="form-horizontal" action= "orden_compra.php" method="POST" name = "orden_compra">
<h2>Orden de Compra</h2>
<script>
    function pop_up(url){
        window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no') 
    }

    function VerificarFactura(value){
        var orden_estado = value;
        $.ajax({
            type: "POST",
            url: "../ajax/orden_compra_ajax.php",
            data: {"orden_estado":orden_estado},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                var factura = data[1];
                //var ingreso = data[2];
                if (factura == 1){
                    alert("La Orden de Compra Nro. " + orden_estado + " ya fue facturada");
                    window.location.href = "orden_compra.php";
                }else{
                    url = "factura_compra_form.php?orden="+orden_estado;
                    window.location.href=url;
                }
            }
        });
    }

    function VerificarIngreso(value){
        var orden_ingreso_estado = value;
        $.ajax({
            type: "POST",
            url: "../ajax/orden_compra_ajax.php",
            data: {"orden_ingreso_estado":orden_ingreso_estado},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                var ingreso = data[1];
                if (ingreso == 1){
                    alert("La Orden de Compra Nro. " + orden_ingreso_estado + " ya fue ingresada");
                    window.location.href = "orden_compra.php";
                }else{
                    url = "ingreso_proveedor_form.php?orden="+orden_ingreso_estado;
                    window.location.href=url;
                }
            }
        });
    }
</script>
<!-- <a href="orden_compra_form.php" onclick="pop_up(this);" class = "add">Nuevo</a> -->
<a target="_blank" href="orden_compra_form.php" class = "add">Nuevo</a>
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
                echo '<th><a href="?sort=fecha_orden&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=descripcion_proveedor&sort_by='.$sort_order.'">Proveedor</th>';
               	echo '<th><a href="?sort=moneda_descripcion&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th><a href="?sort=total_orden&sort_by='.$sort_order.'">Total General </th>';
              	echo '<th>Facturar</th>';
                echo '<th>Ingresar</th>';                
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
                $fecha_orden = date_format(date_create($total_row['fecha_orden']),"d/m/Y");
                echo '<td>'. $fecha_orden . '</td>';
                echo '<td>'. $total_row['descripcion_proveedor'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_orden = number_format(floatval($total_row['total_orden']),2,",",".");
                echo '<td class="monto">'. $total_orden . '</td>';
                echo '<td class = "buttons-column"><a href="#" title="Facturar" class = "Facturar" onclick="VerificarFactura('.$orden.')">Facturar</a></td>';
                echo '<td class = "buttons-column"><a href="#" title="Ingresar" class = "Facturar" onclick="VerificarIngreso('.$orden.')">Ingresar</a></td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado('.$orden.')" href="'.$page_name.'?delete='.$orden.'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_orden_compra.php?orden_compra='.$orden.'">Imprimir</a></td>';
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
            eliminar=confirm("¿Deseas eliminar la Orden de Compra Nro. " + orden +"?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "orden_compra.php";
        }
    </script>
</form>
</body>
</html>


<?php
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $orden_compra->delete_orden_compra($nr);
    }
?>