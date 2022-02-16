<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/cuentas_bancarias.class.php';
    $cuentas_bancarias = cuentas_bancarias::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $nro_cuenta=isset($_POST['nro_cuenta']) ? $_POST['nro_cuenta'] : '';
    $nr_banco=isset($_POST['nr_banco']) ? $_POST['nr_banco'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $descripcion=isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

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

    $query = "select C.nro_cuenta nro_cuenta, C.descripcion Cta_descripcion, C.nr_banco nr_banco, B.descripcion banco_descripcion, C.nr_moneda nr_moneda, M.descripcion moneda_descripcion, C.nr nr from cuentas_bancarias C join banco B on C.nr_banco = B.nr join moneda M on C.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $cuentas_bancarias -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $cuentas_bancarias->param = "&search=$searchstring";
        $query = $query . " WHERE nro_cuenta LIKE '%$searchstring%' OR cta_descripcion LIKE '%$searchstring%' OR banco_descripcion LIKE '%$searchstring%'";
        $qty_register = $cuentas_bancarias -> rowCount($query);
    }

    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'cta_descripcion';  
              
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
        case 'nro_cuenta':  
            $order_by = 'nro_cuenta';  
        break;  
        case 'cta_descripcion':  
            $order_by = 'cta_descripcion';  
        break;
        case 'banco_descripcion':  
            $order_by = 'banco_descripcion';  
        break;       
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $cuentas_bancarias->get_cuentas_bancarias($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $cuentas_bancarias->get_cuentas_bancarias("select C.nro_cuenta nro_cuenta, C.descripcion Cta_descripcion, C.nr_banco nr_banco, B.descripcion banco_descripcion, C.nr_moneda nr_moneda, M.descripcion moneda_descripcion, C.nr nr from cuentas_bancarias C join banco B on C.nr_banco = B.nr join moneda M on C.nr_moneda = M.nr where C.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $nro_cuenta = $row['nro_cuenta'];
            $nr_banco = $row['nr_banco'];
            $nr_moneda = $row['nr_moneda'];
            $descripcion = $row['cta_descripcion'];
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
<form class="form-horizontal" action="cuentas_bancarias.php" method="POST" name = "cuentas_bancarias" autocomplete = "off">
<h2>Cuentas Bancarias</h2>
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
                echo '<th><a href="?sort=nro_cuenta&sort_by='.$sort_order.'">Nro. Cuenta</th>';
                echo '<th><a href="?sort=cta_descripcion&sort_by='.$sort_order.'">Descripcion</th>';
                echo '<th><a href="?sort=banco_descripcion&sort_by='.$sort_order.'">Banco</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['nro_cuenta'] . '</td>';
            echo '<td>'. $total_row['cta_descripcion'] . '</td>';
            echo '<td>'. $total_row['banco_descripcion'] . '</td>';
            echo '<td><a href="cuentas_bancarias.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="cuentas_bancarias.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Cuenta Bancaria</caption><hr><p>
			<a href="cuentas_bancarias.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Nro. Cuenta</label></b>
                    <input type="text" id="nro_cuenta" name="nro_cuenta" value="<?php echo $nro_cuenta;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Banco</label></b>
                    <select name="nr_banco" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $banco_result = $cuentas_bancarias->get_bancos();//We get all the results from the table Banco
                            foreach ($banco_result as $row) {
                            $banco_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_banco==$banco_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Moneda</label></b>
                    <select name="nr_moneda" required class="boxes">
                            <option value="" selected>Seleccione:</option>
                        <?php 
                            $moneda_result = $cuentas_bancarias->get_monedas();//We get all the results from the table Banco
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_moneda==$moneda_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Descripcion</label></b>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo $descripcion;?>" class="boxes" required><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="cuentas_bancarias.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="cuentas_bancarias.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "cuentas_bancarias.php";
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
            $nro_cuenta = $_POST['nro_cuenta'];
            $nr_banco = $_POST['nr_banco'];
            $nr_moneda = $_POST['nr_moneda'];
            $descripcion = $_POST['descripcion'];

            $cuentas_bancarias->insert_cuentas_bancarias($nro_cuenta,$nr_banco,$nr_moneda,$descripcion);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $nro_cuenta = $_POST['nro_cuenta'];
            $nr_banco = $_POST['nr_banco'];
            $nr_moneda = $_POST['nr_moneda'];
            $descripcion = $_POST['descripcion'];

            $cuentas_bancarias->update_cuentas_bancarias($nr,$nro_cuenta,$nr_banco,$nr_moneda,$descripcion);
            echo "<script>window.location.href = 'cuentas_bancarias.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $cuentas_bancarias->delete_cuentas_bancarias($nr);
    }
?>