 <?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/detalle_lista_precios.class.php';
    $detalle_lista_precios = detalle_lista_precios::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $nr_lista_precios=isset($_POST['nr_lista_precios']) ? $_POST['nr_lista_precios'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $precio=isset($_POST['precio']) ? $_POST['precio'] : '';
    $costo_producto=isset($_POST['costo_producto']) ? $_POST['costo_producto'] : '';

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

    $query = "select DLP.nr nr, DLP.nr_lista_precios nr_lista_precios, LP.descripcion descripcion_lista_precios, DLP.nr_producto nr_producto, P.id_producto, P.descripcion descripcion_producto, DLP.precio precio, LP.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda from detalle_lista_precios DLP 
    join lista_de_precios LP on DLP.nr_lista_precios = LP.nr join productos P on DLP.nr_producto = P.nr join moneda M on LP.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $detalle_lista_precios -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $detalle_lista_precios->param = "&search=$searchstring";
        $query = $query . " WHERE descripcion_lista_precios LIKE '%$searchstring%' OR descripcion_producto LIKE '%$searchstring%'";
        $qty_register = $detalle_lista_precios -> rowCount($query);
    }
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'descripcion_lista_precios';  
              
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
        case 'descripcion_producto':  
            $order_by = 'descripcion_producto';  
        break;  
        case 'id_producto':  
            $order_by = 'id_producto';  
        break;   
        case 'descripcion_lista_precios':  
            $order_by = 'descripcion_lista_precios';  
        break;   
        case 'precio':  
            $order_by = 'precio';  
        break; 
        case 'descripcion_moneda':  
            $order_by = 'descripcion_moneda';  
        break; 
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $detalle_lista_precios->get_detalle_lista_precios($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $detalle_lista_precios->get_detalle_lista_precios("select DLP.nr nr, DLP.nr_lista_precios nr_lista_precios, LP.descripcion descripcion_lista_precios, DLP.nr_producto nr_producto, P.id_producto id_producto, P.descripcion descripcion_producto, DLP.precio precio, CP.cpp from detalle_lista_precios DLP join lista_de_precios LP on DLP.nr_lista_precios = LP.nr join productos P on DLP.nr_producto = P.nr left join costos_productos CP on P.nr = CP.nr_producto where DLP.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $nr_lista_precios = $row['nr_lista_precios'];
            $nr_producto = $row['nr_producto'];
            $id_producto = $row['id_producto'];
            $descripcion_producto = $row['descripcion_producto'];
            $precio = $row['precio'];
            $costo_producto = $row['cpp'];
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action="detalle_lista_precios.php" method="POST" name = "detalle_lista_precios" autocomplete = "off">
<h2>Precios</h2>
<a href="#modal" class = "add">Nuevo</a>
<!-- AJAX section -->
<script>
    function getProducto(){
        var producto = document.getElementById("id_producto").value;
        //alert(producto);
        $.ajax({
            type: "POST",
            url: "../ajax/detalle_lista_precios_ajax.php",
            data: {"producto":producto},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                $('#nr_producto').val(data[0]);
                $('#id_producto').val(data[1]);
                $("#descripcion_producto").val(data[2]);
                $("#costo_producto").val(data[3]);
            }
        });
    }

    //Producto
    // AJAX call for autocomplete 
    $(document).ready(function(){
        $("#id_producto").keyup(function(){
            $.ajax({
                type: "POST",
                url: "../ajax/detalle_lista_precios_ajax.php",
                data: {"buscar_producto":$(this).val()},
                beforeSend: function(){
                    $("#id_producto").css("background","#FFF no-repeat 165px");
                },
                success: function(data){
                    $("#producto-suggestion-box").show();
                    $("#producto-suggestion-box").html(data);
                    $("#id_producto").css("background","#FFF");
                }
            });
        });
    });

    //To select Producto
    function selectProducto(val) {
        $("#id_producto").val(val);
        $("#producto-suggestion-box").hide();
        getProducto();
    }
</script>

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
                echo '<th><a href="?sort=descripcion_lista_precios&sort_by='.$sort_order.'">Lista de Precios</th>';
                echo '<th><a href="?sort=id_producto&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=descripcion_producto&sort_by='.$sort_order.'">Descripcion</th>';
                echo '<th><a href="?sort=precio&sort_by='.$sort_order.'">Precio</th>';
                echo '<th><a href="?sort=descripcion_moneda&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['descripcion_lista_precios'] . '</td>';
            echo '<td>'. $total_row['id_producto'] . '</td>';
            echo '<td>'. $total_row['descripcion_producto'] . '</td>';
            $valor_precio = number_format($total_row['precio'],2,",",".");
            echo '<td class="monto">'. $valor_precio . '</td>';
            echo '<td>'. $total_row['descripcion_moneda'] . '</td>';
            echo '<td><a href="detalle_lista_precios.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="detalle_lista_precios.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Precios</caption><hr><p>
			<a href="detalle_lista_precios.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Lista de Precios</label></b>
                    <select name="nr_lista_precios" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $lista_precios_result = $detalle_lista_precios->get_lista_precios();//We get all the results from the table Lista de Precios
                            foreach ($lista_precios_result as $row) {
                            $lista_precios_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_lista_precios==$lista_precios_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Producto</label></b>
                    <p hidden><input type="text" id="nr_producto" name="nr_producto" value="<?php echo $nr_producto;?>"></p>
                    <div class="ProductoSearch">
                        <input type="text" id="id_producto" name="id_producto" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                        <input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>" readonly disabled>
                        <div id="producto-suggestion-box"></div>
                    </div><p>
                    <b><label class="control-label">Precio</label></b>
                    <input type="number" id="precio" name="precio" step="any" value="<?php echo $precio;?>" class="boxes" required><p>
                    <b><label class="control-label">Costo Gs.(sin IVA)</label></b>
                    <input type="number" id="costo_producto" name="costo_producto" step="any" value="<?php echo $costo_producto;?>" class="boxes" readonly disabled><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="detalle_lista_precios.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="detalle_lista_precios.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "detalle_lista_precios.php";
        }
    </script>

</form>
</body>
</html>


<?php
    if (isset ($_POST['action'])){
    switch($_POST['action'])
    {
        case "Crear": 
            $nr_lista_precios = $_POST['nr_lista_precios'];
            $nr_producto = $_POST['nr_producto'];
            $precio = $_POST['precio'];

            $detalle_lista_precios->insert_detalle_lista_precios($nr_lista_precios,$nr_producto,$precio);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $nr_lista_precios = $_POST['nr_lista_precios'];
            $nr_producto = $_POST['nr_producto'];
            $precio = $_POST['precio'];

            $detalle_lista_precios->update_detalle_lista_precios($nr,$nr_lista_precios,$nr_producto,$precio);
            echo "<script>window.location.href = 'detalle_lista_precios.php'</script>";
        break;
    }
    }
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $detalle_lista_precios->delete_detalle_lista_precios($nr);
    }
?>