<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/precio_producto_proveedor.class.php';
    $precio_producto_proveedor = precio_producto_proveedor::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $nr_proveedor=isset($_POST['nr_proveedor']) ? $_POST['nr_proveedor'] : '';
    $id_proveedor=isset($_POST['id_proveedor']) ? $_POST['id_proveedor'] : '';
    $descripcion_proveedor=isset($_POST['descripcion_proveedor']) ? $_POST['descripcion_proveedor'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $precio=isset($_POST['precio']) ? $_POST['precio'] : '';

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

    $query = "select LPP.nr, LPP.nr_producto, P.descripcion descripcion_producto, LPP.nr_proveedor, PR.descripcion descripcion_proveedor, LPP.nr_moneda, M.descripcion descripcion_moneda, LPP.precio from precio_producto_proveedor LPP join productos P on LPP.nr_producto = P.nr join proveedor PR on LPP.nr_proveedor = PR.nr join moneda M on LPP.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $precio_producto_proveedor -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $precio_producto_proveedor->param = "&search=$searchstring";
        $query = $query . " WHERE descripcion_producto LIKE '%$searchstring%' OR descripcion_proveedor LIKE '%$searchstring%'";
        $qty_register = $precio_producto_proveedor -> rowCount($query);
    }
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'descripcion_producto';  
              
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
        case 'descripcion_proveedor':  
            $order_by = 'descripcion_proveedor';  
        break;  
        case 'descripcion_producto':  
            $order_by = 'descripcion_producto';  
        break;   
        case 'descripcion_moneda':  
            $order_by = 'descripcion_moneda';  
        break; 
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $precio_producto_proveedor->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $precio_producto_proveedor->query("select LPP.nr, LPP.nr_producto, P.id_producto, P.descripcion descripcion_producto, LPP.nr_proveedor, PR.id_proveedor, PR.descripcion descripcion_proveedor, LPP.nr_moneda, M.descripcion descripcion_moneda, LPP.precio 
        from precio_producto_proveedor LPP join productos P on LPP.nr_producto = P.nr join proveedor PR on LPP.nr_proveedor = PR.nr join moneda M on LPP.nr_moneda = M.nr where LPP.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $nr_producto = $row['nr_producto'];
            $id_producto = $row['id_producto'];
            $descripcion_producto = $row['descripcion_producto'];
            $nr_proveedor = $row['nr_proveedor'];
            $id_proveedor = $row['id_proveedor'];
            $descripcion_proveedor = $row['descripcion_proveedor'];
            $nr_moneda = $row['nr_moneda'];
            $precio = $row['precio'];
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
<form class="form-horizontal" action="precio_producto_proveedor.php" method="POST" name = "precio_producto_proveedor" autocomplete = "off">
<h2>Precio por Producto/Proveedor</h2>
<!-- AJAX section -->
<script>
    function getProveedor(){
        var proveedor = document.getElementById("id_proveedor").value;
        $.ajax({
            type: "POST",
            url: "../ajax/precio_producto_proveedor_ajax.php",
            data: {"proveedor":proveedor},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                $('#nr_proveedor').val(data[0]);
                $('#id_proveedor').val(data[1]);
                $('#descripcion_proveedor').val(data[2]);
            }
        });
    }

    function getProducto(){
        var producto = document.getElementById("id_producto").value;
        //alert(producto);
        $.ajax({
            type: "POST",
            url: "../ajax/precio_producto_proveedor_ajax.php",
            data: {"producto":producto},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                $('#nr_producto').val(data[0]);
                $('#id_producto').val(data[1]);
                $("#descripcion_producto").val(data[2]);
            }
        });
    }

    //Proveedor
    // AJAX call for autocomplete 
    $(document).ready(function(){
        $("#id_proveedor").keyup(function(){
            $.ajax({
                type: "POST",
                url: "../ajax/precio_producto_proveedor_ajax.php",
                data: {"buscar_proveedor":$(this).val()},
                beforeSend: function(){
                    $("#id_proveedor").css("background","#FFF no-repeat 165px");
                },
                success: function(data){
                    $("#proveedor-suggestion-box").show();
                    $("#proveedor-suggestion-box").html(data);
                    $("#id_proveedor").css("background","#FFF");
                }
            });
        });
    });

    //To select proveedor
    function selectProveedor(val) {
        $("#id_proveedor").val(val);
        $("#proveedor-suggestion-box").hide();
        getProveedor();
    }

    //Producto
    // AJAX call for autocomplete 
    $(document).ready(function(){
        $("#id_producto").keyup(function(){
            $.ajax({
                type: "POST",
                url: "../ajax/precio_producto_proveedor_ajax.php",
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
                echo '<th><a href="?sort=descripcion_producto&sort_by='.$sort_order.'">Producto</th>';
                echo '<th><a href="?sort=descripcion_proveedor&sort_by='.$sort_order.'">Proveedor</th>';
                echo '<th><a href="?sort=descripcion_moneda&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th><a href="?sort=precio&sort_by='.$sort_order.'">Precio</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['descripcion_producto'] . '</td>';
            echo '<td>'. $total_row['descripcion_proveedor'] . '</td>';
            echo '<td>'. $total_row['descripcion_moneda'] . '</td>';
            $valor_precio = number_format(floatval($total_row['precio']),2,",",".");
            echo '<td class="monto">'. $valor_precio . '</td>';
            echo '<td><a href="precio_producto_proveedor.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="precio_producto_proveedor.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Precio por Producto / Proveedor</caption><hr><p>
			<a href="precio_producto_proveedor.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Producto</label></b>
                    <p hidden><input type="text" id="nr_producto" name="nr_producto" value="<?php echo $nr_producto;?>"></p>
                    <div class="ProductoSearch">
                        <input type="text" id="id_producto" name="id_producto" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                        <input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>" readonly disabled>
                        <div id="producto-suggestion-box"></div>
                    </div><p>
                    <b><label class="control-label">Proveedor</label></b>
                    <p hidden><input type="text" id="nr_proveedor" name="nr_proveedor" value="<?php echo $nr_proveedor;?>"></p>
                    <div class="ProveedorSearch">
                        <input type="text" id="id_proveedor" name="id_proveedor" placeholder="Buscar..." value="<?php echo $id_proveedor;?>" onchange="getProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                        <input type="text" id="descripcion_proveedor" name="descripcion_proveedor" value="<?php echo $descripcion_proveedor;?>"  readonly disabled>
                        <div id="proveedor-suggestion-box"></div>
                    </div><p>
                    <b><label class="control-label">Moneda</label></b>
                    <select name="nr_moneda" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $moneda_result = $precio_producto_proveedor->get_moneda();//We get all the results from the table Moneda
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_moneda==$moneda_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Precio</label></b>
                    <input type="number" id="precio" name="precio" step="any" value="<?php echo $precio;?>" class="boxes" required><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="precio_producto_proveedor.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="precio_producto_proveedor.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "precio_producto_proveedor.php";
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
            $nr_producto = $_POST['nr_producto'];
            $nr_proveedor = $_POST['nr_proveedor'];
            $nr_moneda = $_POST['nr_moneda'];
            $precio = $_POST['precio'];

            $precio_producto_proveedor->insert_precio_producto_proveedor($nr_producto,$nr_proveedor,$nr_moneda,$precio);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $nr_producto = $_POST['nr_producto'];
            $nr_proveedor = $_POST['nr_proveedor'];
            $nr_moneda = $_POST['nr_moneda'];
            $precio = $_POST['precio'];

            $precio_producto_proveedor->update_precio_producto_proveedor($nr,$nr_producto,$nr_proveedor,$nr_moneda,$precio);
            echo "<script>window.location.href = 'precio_producto_proveedor.php'</script>";
        break;
    }
    }
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $precio_producto_proveedor->delete_precio_producto_proveedor($nr);
    }
?>