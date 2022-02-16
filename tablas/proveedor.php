<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/proveedor.class.php';
    $proveedor = proveedor::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_proveedor=isset($_POST['id_proveedor']) ? $_POST['id_proveedor'] : '';
    $descripcion=isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $nr_condicion=isset($_POST['nr_condicion']) ? $_POST['nr_condicion'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $ciudad=isset($_POST['ciudad']) ? $_POST['ciudad'] : '';
    $nr_pais=isset($_POST['nr_pais']) ? $_POST['nr_pais'] : '';
    $contacto=isset($_POST['contacto']) ? $_POST['contacto'] : '';
    $pagina_web=isset($_POST['pagina_web']) ? $_POST['pagina_web'] : '';
    $email=isset($_POST['email']) ? $_POST['email'] : '';
    $otros_datos=isset($_POST['otros_datos']) ? $_POST['otros_datos'] : '';
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

    $query = "SELECT * FROM proveedor";
    //Search query
    $searchstring = null;    
    $qty_register = $proveedor -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $proveedor->param = "&search=$searchstring";
        $query = $query . " WHERE id_proveedor LIKE '%$searchstring%' OR descripcion LIKE '%$searchstring%'";
        $qty_register = $proveedor -> rowCount($query);
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
        case 'id_proveedor':  
            $order_by = 'id_proveedor';  
        break;  
        case 'descripcion':  
            $order_by = 'descripcion';  
        break;   
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $proveedor->get_proveedor($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $proveedor->get_proveedor("select * from proveedor where nr = $parameter");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_proveedor = $row['id_proveedor'];
            $descripcion = $row['descripcion'];
            $nr_condicion = $row['nr_condicion'];
            $direccion = $row['direccion'];
            $telefono = $row['telefono'];
            $ciudad = $row['ciudad'];
            $nr_pais = $row['nr_pais'];
            $contacto = $row['contacto'];
            $pagina_web = $row['pagina_web'];
            $email = $row['email'];
            $otros_datos = $row['otros_datos'];
            $nr_moneda = $row['nr_moneda'];
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
<form class="form-horizontal" action="proveedor.php" method="POST" name = "proveedor" autocomplete = "off">
<h2>Proveedor</h2>
<a href="#modal" class = "add">Nuevo</a>
<center>
    <!--<p class="left-style">Nro. de registros por pag.:<select name='limit' onchange='javascript: submit()'>
        <option>5</option>
        <option>10</option>
        <option>50</option>
    </select>
    Busqueda: 
    <input type="text" id="searchstring" name="searchstring" class="right-style" placeholder="Busqueda" value="<?php echo $searchstring ?>">
    <input type="submit" id="search" value="Buscar"></p>-->
<div>
<?php
    echo '<table class="list">';
       echo '<thead>';
            echo '<tr>';
                echo '<th><a href="?sort=id_proveedor&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=descripcion&sort_by='.$sort_order.'">Razon Social</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_proveedor'] . '</td>';
            echo '<td>'. $total_row['descripcion'] . '</td>';
            echo '<td><a href="proveedor.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="proveedor.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Proveedor</caption><hr><p>
			<a href="proveedor.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="text" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_proveedor" name="id_proveedor" value="<?php echo $id_proveedor;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Razon Social</label></b>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo $descripcion;?>" class="boxes" required><p>
                    <b><label class="control-label">Forma de Pago</label></b>
                    <select name="nr_condicion" required class="boxes">
                    <option value="" selected>Seleccione:</option>
                        <?php 
                            $condicion_result = $proveedor->get_condicion();//We get all the results from the table Banco
                            foreach ($condicion_result as $row) {
                            $condicion_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_condicion==$condicion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Direccion</label></b>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $direccion;?>" class="boxes"><p>
                    <b><label class="control-label">Telefono</label></b>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $telefono;?>" class="boxes"><p>
                    <b><label class="control-label">Ciudad</label></b>
                    <input type="text" id="ciudad" name="ciudad" value="<?php echo $ciudad;?>" class="boxes"><p>
                    <b><label class="control-label">Pais</label></b>
                    <select name="nr_pais" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $pais_result = $proveedor->get_pais();//We get all the results from the table Banco
                            foreach ($pais_result as $row) {
                            $pais_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_pais==$pais_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Contacto</label></b>
                    <input type="text" id="contacto" name="contacto" value="<?php echo $contacto;?>" class="boxes"><p>
                    <b><label class="control-label">Pagina web</label></b>
                    <input type="text" id="pagina_web" name="pagina_web" value="<?php echo $pagina_web;?>" class="boxes"><p>
                    <b><label class="control-label">E-mail</label></b>
                    <input type="text" id="email" name="email" value="<?php echo $email;?>" class="boxes"><p>
                    <b><label class="control-label">Otros Datos</label></b>
                    <input type="text" id="otros_datos" name="otros_datos" value="<?php echo $otros_datos;?>" class="boxes"><p>
                    <b><label class="control-label">Moneda</label></b>
                    <select name="nr_moneda" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $moneda_result = $proveedor->get_moneda();//We get all the results from the table Banco
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_moneda==$moneda_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                        </select><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="proveedor.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="proveedor.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "proveedor.php";
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
            $id_proveedor = $_POST['id_proveedor'];
            $descripcion = $_POST['descripcion'];
            $nr_condicion = $_POST['nr_condicion'];
            $direccion = $_POST['direccion'];
            $telefono= $_POST['telefono'];
            $ciudad= $_POST['ciudad'];
            $nr_pais= $_POST['nr_pais'];
            $contacto= $_POST['contacto'];
            $pagina_web= $_POST['pagina_web'];
            $email= $_POST['email'];
            $otros_datos= $_POST['otros_datos'];
            $nr_moneda= $_POST['nr_moneda'];
        
            $proveedor->insert_proveedor($id_proveedor,$descripcion,$nr_condicion,$direccion,$telefono,$ciudad,$nr_pais,$contacto,$pagina_web,$email,$otros_datos,$nr_moneda);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_proveedor = $_POST['id_proveedor'];
            $descripcion = $_POST['descripcion'];
            $nr_condicion = $_POST['nr_condicion'];
            $direccion = $_POST['direccion'];
            $telefono= $_POST['telefono'];
            $ciudad= $_POST['ciudad'];
            $nr_pais= $_POST['nr_pais'];
            $contacto= $_POST['contacto'];
            $pagina_web= $_POST['pagina_web'];
            $email= $_POST['email'];
            $otros_datos= $_POST['otros_datos'];
            $nr_moneda= $_POST['nr_moneda'];
            
            $proveedor->update_proveedor($nr,$id_proveedor,$descripcion,$nr_condicion,$direccion,$telefono,$ciudad,$nr_pais,$contacto,$pagina_web,$email,$otros_datos,$nr_moneda);
            echo "<script>window.location.href = 'proveedor.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $proveedor->delete_proveedor($nr);
    }
?>