<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/depositos_stock.class.php';
    $depositos = depositos_stock::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_deposito=isset($_POST['id_deposito']) ? $_POST['id_deposito'] : '';
    $descripcion=isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';

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

    $query = "select D.nr nr, D.id_deposito id_deposito, D.descripcion descripcion, D.nr_sucursal nr_sucursal, S.descripcion sucursal_descripcion from depositos_stock D join sucursal S on D.nr_sucursal = S.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $depositos -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $depositos->param = "&search=$searchstring";
        $query = $query . " WHERE id_deposito LIKE '%$searchstring%' OR descripcion LIKE '%$searchstring%' OR sucursal_descripcion LIKE '%$searchstring%'";
        $qty_register = $depositos -> rowCount($query);
    }
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'descripcion';  
              
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
        case 'id_deposito':  
            $order_by = 'id_deposito';  
        break;  
        case 'descripcion':  
            $order_by = 'descripcion';  
        break;   
        case 'sucursal_descripcion':  
            $order_by = 'sucursal_descripcion';  
        break;
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $depositos->get_depositos_stock($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $depositos->get_depositos_stock("select D.nr nr, D.id_deposito id_deposito, D.descripcion descripcion, D.nr_sucursal nr_sucursal, S.descripcion sucursal_descripcion from depositos_stock D join sucursal S on D.nr_sucursal = S.nr where D.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_deposito = $row['id_deposito'];
            $descripcion = $row['descripcion'];
            $nr_sucursal = $row['nr_sucursal'];
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
</head>
<body>
<form class="form-horizontal" action="depositos_stock.php" method="POST" name = "depositos" autocomplete = "off">
<h2>Depositos</h2>
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
                echo '<th><a href="?sort=id_deposito&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=descripcion&sort_by='.$sort_order.'">Descripcion</th>';
                echo '<th><a href="?sort=sucursal_descripcion&sort_by='.$sort_order.'">Sucursal</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_deposito'] . '</td>';
            echo '<td>'. $total_row['descripcion'] . '</td>';
            echo '<td>'. $total_row['sucursal_descripcion'] . '</td>';
            echo '<td><a href="depositos_stock.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="depositos_stock.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Depositos de Stock</caption><hr><p>
			<a href="depositos_stock.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_deposito" name="id_deposito" value="<?php echo $id_deposito;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Descripcion</label></b>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo $descripcion;?>" class="boxes" required><p>
                    <b><label class="control-label">Sucursal</label></b>
                    <select name="nr_sucursal" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $sucursal_result = $depositos->get_sucursal();//We get all the results from the table Sucursal
                            foreach ($sucursal_result as $row) {
                            $sucursal_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="depositos_stock.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="depositos_stock.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "depositos_stock.php";
        }
    </script>

</form>
</body>
</html>


<?php
    if (isset($_POST['action'])){
    switch($_POST['action'])
    {
        case "Crear": 
            $id_deposito = $_POST['id_deposito'];
            $descripcion = $_POST['descripcion'];
            $nr_sucursal = $_POST['nr_sucursal'];
            
            $depositos->insert_depositos_stock($id_deposito,$descripcion,$nr_sucursal);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_deposito = $_POST['id_deposito'];
            $descripcion = $_POST['descripcion'];
            $nr_sucursal = $_POST['nr_sucursal'];
            
            $depositos->update_depositos_stock($nr,$id_deposito,$descripcion,$nr_sucursal);
            echo "<script>window.location.href = 'depositos_stock.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $depositos->delete_depositos_stock($nr);
    }
?>