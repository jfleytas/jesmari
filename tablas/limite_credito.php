<?php
    require '../tablas/menu.php';

    require '../clases/tablas/limite_credito.class.php';
    $limite_credito = limite_credito::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $nr_cliente=isset($_POST['nr_cliente']) ? $_POST['nr_cliente'] : '';
    $id_cliente=isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $razon_social=isset($_POST['razon_social']) ? $_POST['razon_social'] : '';
    $credito_maximo=isset($_POST['credito_maximo']) ? $_POST['credito_maximo'] : '0';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $total_ventas=isset($_POST['total_ventas']) ? $_POST['total_ventas'] : '0';
    $total_nota_credito=isset($_POST['total_nota_credito']) ? $_POST['total_nota_credito'] : '0';
    $total_recibo=isset($_POST['total_recibo']) ? $_POST['total_recibo'] : '0';
    $saldo=isset($_POST['saldo']) ? $_POST['saldo'] : '0';
    $credito_disponible=isset($_POST['credito_disponible']) ? $_POST['credito_disponible'] : '0';

    //Get the configuration for Compras forms
    $configuracion_result = $limite_credito->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

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

    $query = "select CC.nr, CC.nr_cliente, C.id_cliente, C.razon_social, CC.nr_moneda, M.id_moneda id_moneda, CC.limite_credito, CC.total_ventas, CC.total_nota_credito, CC.total_recibos, CC.saldo from cuenta_cliente CC join clientes C on CC.nr_cliente = C.nr join Moneda M on CC.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $limite_credito -> rowCount($query);


    //
    /*@$key=$_GET['key'];
    $array = array();
    $query1= $query . " WHERE nr_cliente LIKE '%{$key}%' OR nr_moneda LIKE '%{$key}%' OR nr_moneda_marca LIKE '%{$key}%'";
    echo $query1;
    $test = $limite_credito->query($query1);


    foreach($test as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['nr_cliente'] . '</td>';
            echo '<td>'. $total_row['nr_moneda'] . '</td>';
            echo '<td>'. $total_row['nr_moneda_tipo'] . '</td>';
            echo '</tr>';
    endforeach;


    while($row=pg_fetch_assoc($test))
    {
      $array[] = $row['nr_cliente'];
    }
    echo json_encode($array);*/





    /*if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $limite_credito->param = "&search=$searchstring";
        $query = $query . " WHERE nr_cliente LIKE '%$searchstring%' OR nr_moneda LIKE '%$searchstring%' OR nr_moneda_marca LIKE '%$searchstring%'";
        $qty_register = $limite_credito -> rowCount($query);
    }*/

    if (!empty($_REQUEST['searchstring']))
    {
        //$searchstring = mysql_real_escape_string($_REQUEST['searchstring']); 
        $qty_register = $limite_credito -> rowCount($query);
        $query = $query . " WHERE nr_cliente LIKE '%$searchstring%' OR nr_moneda LIKE '%$searchstring%'";
    }


    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'id_cliente';  
              
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
        case 'id_cliente':  
            $order_by = 'id_cliente';  
        break;  
        case 'razon_social':  
            $order_by = 'razon_social';  
        break;   
        case 'id_moneda':  
            $order_by = 'id_moneda';  
        break;
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $limite_credito->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $limite_credito->query("select CC.nr, CC.nr_cliente, C.id_cliente, C.razon_social, CC.nr_moneda, M.id_moneda id_moneda, CC.limite_credito, CC.total_ventas, CC.total_nota_credito, CC.total_recibos, CC.saldo, CC.credito_disponible from cuenta_cliente CC join clientes C on CC.nr_cliente = C.nr join Moneda M on CC.nr_moneda = M.nr where CC.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $nr_cliente = $row['nr_cliente'];
            $id_cliente = $row['id_cliente'];
            $razon_social = $row['razon_social'];
            $credito_maximo = $row['limite_credito'];
            $nr_moneda = $row['nr_moneda'];
            $total_ventas = $row['total_ventas'];
            $total_nota_credito = $row['total_nota_credito'];
            $total_recibo = $row['total_recibos'];
            $saldo = $row['saldo'];
            $credito_disponible = $row['credito_disponible'];
        endforeach;
    }

    if ($moneda_defecto == $nr_moneda)
    {
        $decimal=0;
    }else{
        $decimal=2;
    }

    //Result from Clientes
    if(isset($_GET['cliente']))
    {
        $cliente = $_GET['cliente'];
        $dato_cliente = $limite_credito->query("select * from clientes C left join cuenta_cliente CC on C.nr = CC.nr_cliente where id_cliente = '$cliente'");//We get the desired result from the table
        foreach ($dato_cliente as $resultado) {        
            $nr_cliente = $resultado['nr'];
            $id_cliente = $resultado['id_cliente'];
            $razon_social = $resultado['razon_social'];
            $total_ventas = $resultado['total_ventas'];
            $total_nota_credito = $resultado['total_nota_credito'];
            $total_recibos = $resultado['total_recibos'];
            $saldo = $resultado['saldo'];
            $credito_disponible = $resultado['credito_disponible'];
        }
    }
?>

<!-- Javascript section -->
<script type="text/javascript">
    function getCliente(){
        var opcion = document.getElementById('id_cliente').value;
        window.location.href = 'limite_credito.php?cliente=' +opcion+'#modal';
    }
</script> 

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
</head>
<body>
<form class="form-horizontal" action="limite_credito.php" method="POST" name = "limite_credito" autocomplete = "off">
<h2>Limite de Credito</h2>
<!--<a href="#modal" class = "add">Nuevo</a>-->
<center>
     <!--<p class="left-style">Nro. de registros por pag.:<select name="limit" id= "limit" onchange="this.form.submit();">
        <?php 
        echo '<option value="5"'.$select5.'>5</option>';
        echo '<option value="10"'.$select10.'>10</option>';
        echo '<option value="50"'.$select50.'>50</option>';
        ?>
    </select>
    Busqueda: 
    <input type="text" id="searchstring" name="searchstring" placeholder="Buscar" value="<?php echo $searchstring ?>">
    <input type="submit" id="search" value="Buscar"></p>


    <input type="text" name="typeahead">

    <script>
    $(document).ready(function(){
    $('input.typeahead').typeahead({
        name: 'typeahead',
        remote:'limite_credito.php?key=%QUERY',
        limit : 10
        });
    });
    </script>-->

<div>
<?php
    echo '<table class="list">';
       echo '<thead>';
            echo '<tr>';
                echo '<th><a href="?sort=id_cliente&sort_by='.$sort_order.'">Codigo Cliente</th>';
                echo '<th><a href="?sort=razon_social&sort_by='.$sort_order.'">Razon Social</th>';
                echo '<th><a href="?sort=id_moneda&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th>Limite de Credito</th>';
                echo '<th>Editar</th>';
                //echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_cliente'] . '</td>';
            echo '<td>'. $total_row['razon_social'] . '</td>';
            echo '<td>'. $total_row['id_moneda'] . '</td>';
            $valor_limite = number_format($total_row['limite_credito'],2,",",".");
            echo '<td>'. $valor_limite . '</td>';
            echo '<td><a href="limite_credito.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            //echo '<td><a onclick="return confirmarBorrado()" href="limite_credito.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption style="text-align: center">Limite de Credito</caption><hr><p>
			<a href="limite_credito.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <p hidden><input type="number" id="nr_cliente" name="nr_cliente" value="<?php echo $nr_cliente;?>"> </p>
                    <b><label class="control-label">Codigo Cliente</label></b>
                    <input type="text" id="id_cliente" name="id_cliente" onchange="return getCliente();" value="<?php echo $id_cliente;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required readonly disabled><p>
                    <b><label class="control-label">Razon Social</label></b>
                    <input type="text" id="razon_social" name="razon_social" value="<?php echo $razon_social;?>" class="boxes" readonly disabled><p>
                    <b><label class="control-label">Moneda</label></b>
                    <p hidden><input type="number" id="nr_moneda" name="nr_moneda" value="<?php echo $nr_moneda;?>"> </p>
                    <select id= "nr_moneda" name="nr_moneda" required class="boxes" readonly disabled>
                        <?php 
                            $moneda_result = $limite_credito->get_moneda();//We get all the results from the table Moneda
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_moneda==$moneda_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Credito Maximo</label></b>
                    <input type="text" id="credito_maximo" name="credito_maximo" step="any" value="<?php echo number_format($credito_maximo,$decimal,",",".");?>" class="boxes" min = 1 required><p>
                    <b><label class="control-label">Total Facturas</label></b>
                    <input type="text" id="total_ventas" name="total_ventas" step="any" value="<?php echo number_format($total_ventas,$decimal,",",".");?>" class="boxes" readonly disabled><p>
                    <b><label class="control-label">Total Notas de Credito</label></b>
                    <input type="text" id="total_nota_credito" name="total_nota_credito" step="any" value="<?php echo number_format($total_nota_credito,$decimal,",",".");?>" class="boxes" readonly disabled><p>
                    <b><label class="control-label">Total Recibos</label></b>
                    <input type="text" id="total_recibo" name="total_recibo" step="any" value="<?php echo number_format($total_recibo,$decimal,",",".");?>" class="boxes" readonly disabled><p>
                    <b><label class="control-label">Saldo</label></b>
                    <input type="text" id="saldo" name="saldo" step="any" value="<?php echo number_format($saldo,$decimal,",",".");?>" class="boxes" readonly disabled><p>
                    <b><label class="control-label">Credito Disponible</label></b>
                    <input type="text" id="credito_disponible" name="credito_disponible" step="any" value="<?php echo number_format($credito_disponible,$decimal,",",".");?>" class="boxes" readonly disabled style="font-weight: bold"><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="limite_credito.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="limite_credito.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "limite_credito.php";
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
            $nr_cliente = $nr_cliente;
            $nr_moneda= $nr_moneda;
            $credito_maximo = $_POST['credito_maximo'];
            if(empty($credito_maximo)) 
            {
                $credito_maximo = 0;
            }
            $credito_disponible = $_POST['credito_maximo'];

            $limite_credito->insert_limite_credito($nr_cliente,$nr_moneda,$credito_maximo,$credito_disponible);
        break;

        case "Modificar":
            $nr = $_POST['nr'];
            $nr_cliente = $nr_cliente;
            //echo 'nr_cliente: '.$nr_cliente;
            $nr_moneda = $nr_moneda;
            $credito_maximo = $_POST['credito_maximo'];
            //echo 'Credito:'.$credito_maximo;
            $total_ventas = $total_ventas;
            $total_nota_credito = $total_nota_credito;
            $total_recibo = $total_recibo;

            if(empty($credito_maximo)) 
            {
                $credito_maximo = 0;
            }
            //echo "Valor:".$nr."-".$nr_cliente."-".$nr_moneda."-".$credito_maximo;
            $result = $limite_credito->query("select CC.nr, CC.nr_cliente, C.id_cliente, C.razon_social, CC.nr_moneda, M.id_moneda id_moneda, CC.limite_credito, CC.total_ventas, CC.total_nota_credito, CC.total_recibos, CC.saldo, CC.credito_disponible from cuenta_cliente CC join clientes C on CC.nr_cliente = C.nr join Moneda M on CC.nr_moneda = M.nr where C.nr ='$nr_cliente'");//We get the desired result from the table
            foreach($result as $row):
                $saldo = $row['saldo'];
            endforeach;
            //echo 'Saldo: '.$saldo;

            $credito_disponible_nuevo = ($credito_maximo - $saldo);

            //echo $credito_disponible_nuevo;
            if ($credito_disponible_nuevo<0)
            {
                echo "<script>alert('El nuevo limite de credito no puede ser inferior al saldo del cliente.')</script>";
            }else{
                $limite_credito->update_limite_credito($nr,$nr_cliente,$nr_moneda,$credito_maximo,$credito_disponible_nuevo);
                echo "<script>window.location.href = 'limite_credito.php'</script>";
            }
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $limite_credito->delete_limite_credito($nr);
    }
?>