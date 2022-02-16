<?php
    require '../tablas/menu.php';
    
    require '../clases/tablas/personal.class.php';
    $personal = personal::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_personal=isset($_POST['id_personal']) ? $_POST['id_personal'] : '';
    $nombre_apellido=isset($_POST['nombre_apellido']) ? $_POST['nombre_apellido'] : '';
    $documento_nr=isset($_POST['documento_nr']) ? $_POST['documento_nr'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $email=isset($_POST['email']) ? $_POST['email'] : '';
    $nr_tipo_personal=isset($_POST['nr_tipo_personal']) ? $_POST['nr_tipo_personal'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $nr_user=isset($_POST['nr_user']) ? $_POST['nr_user'] : '';
    $otros_datos=isset($_POST['otros_datos']) ? $_POST['otros_datos'] : '';

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

    $query = "select P.nr, P.id_personal, P.nombre_apellido, P.nr_tipo_personal, TP.descripcion from personal P join tipo_personal TP on P.nr_tipo_personal = TP.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $personal -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $personal->param = "&search=$searchstring";
        $query = $query . " WHERE id_personal LIKE '%$searchstring%' OR nombre_apellido LIKE '%$searchstring%'";
        $qty_register = $personal -> rowCount($query);
    }
    //Order query
    $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'nombre_apellido';  
              
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
        case 'id_personal':  
            $order_by = 'id_personal';  
        break;  
        case 'nombre_apellido':  
            $order_by = 'nombre_apellido';  
        break;   
        case 'tipo_personal':  
            $order_by = 'descripcion';  
        break; 
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $personal->get_personal($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $personal->get_personal("select * from personal where nr = $parameter");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_personal = $row['id_personal'];
            $nombre_apellido = $row['nombre_apellido'];
            $documento_nr = $row['documento_nr'];
            $direccion = $row['direccion'];
            $telefono = $row['telefono'];
            $email = $row['email'];
            $nr_tipo_personal = $row['nr_tipo_personal'];
            $nr_sucursal = $row['nr_sucursal'];
            $nr_user = $row['nr_user'];
            $otros_datos = $row['otros_datos'];
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
<form class="form-horizontal" action="personal.php" method="POST" name = "personal" autocomplete = "off">
<h2>Personal</h2>
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
                echo '<th><a href="?sort=id_personal&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=nombre_apellido&sort_by='.$sort_order.'">Razon Social</th>';
                echo '<th><a href="?sort=tipo_personal&sort_by='.$sort_order.'">Tipo Personal</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_personal'] . '</td>';
            echo '<td>'. $total_row['nombre_apellido'] . '</td>';
            echo '<td>'. $total_row['descripcion'] . '</td>';
            echo '<td><a href="personal.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="personal.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption align="center">Personal</caption><hr><p>
			<a href="personal.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="text" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_personal" name="id_personal" value="<?php echo $id_personal;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Nombre y Apellido</label></b>
                    <input type="text" id="nombre_apellido" name="nombre_apellido" value="<?php echo $nombre_apellido;?>" class="boxes" required><p>
                    <b><label class="control-label">Documento Nr.</label></b>
                    <input type="text" id="documento_nr" name="documento_nr" value="<?php echo $documento_nr;?>" class="boxes"><p>
                    <b><label class="control-label">Direccion</label></b>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $direccion;?>" class="boxes"><p>
                    <b><label class="control-label">Telefono</label></b>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $telefono;?>" class="boxes"><p>
                    <b><label class="control-label">E-mail</label></b>
                    <input type="text" id="email" name="email" value="<?php echo $email;?>" class="boxes"><p>
                    <b><label class="control-label">Tipo Personal</label></b>
                    <select name="nr_tipo_personal" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $tipo_personal_result = $personal->get_tipo_personal();//We get all the results from the table Banco
                            foreach ($tipo_personal_result as $row) {
                            $tipo_personal_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_tipo_personal==$tipo_personal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Sucursal</label></b>
                    <select name="nr_sucursal" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $sucursal_result = $personal->get_sucursal();//We get all the results from the table Banco
                            foreach ($sucursal_result as $row) {
                            $sucursal_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Usuario</label></b>
                    <select name="nr_user" class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $user_result = $personal->get_user();//We get all the results from the table Banco
                            foreach ($user_result as $row) {
                            $user_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_user==$user_nr) echo 'selected="selected"';
                            echo '>'.$row['id_user'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Otros Datos</label></b>
                    <input type="text" id="otros_datos" name="otros_datos" value="<?php echo $otros_datos;?>" class="boxes"><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="personal.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="personal.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "personal.php";
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
            $id_personal = $_POST['id_personal'];
            $nombre_apellido = $_POST['nombre_apellido'];
            $documento_nr = $_POST['documento_nr'];
            $direccion = $_POST['direccion'];
            $telefono= $_POST['telefono'];
            $email= $_POST['email'];
            $nr_tipo_personal= $_POST['nr_tipo_personal'];
            $nr_sucursal= $_POST['nr_sucursal'];
            $nr_user= $_POST['nr_user'];
            if(empty($nr_user)) 
            {
                $nr_user = NULL;
            }
            $otros_datos= $_POST['otros_datos'];
        
            $personal->insert_personal($id_personal,$nombre_apellido,$documento_nr,$direccion,$telefono,$email,$nr_tipo_personal,$nr_sucursal,$nr_user,$otros_datos);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_personal = $_POST['id_personal'];
            $nombre_apellido = $_POST['nombre_apellido'];
            $documento_nr = $_POST['documento_nr'];
            $direccion = $_POST['direccion'];
            $telefono= $_POST['telefono'];
            $email= $_POST['email'];
            $nr_tipo_personal= $_POST['nr_tipo_personal'];
            $nr_sucursal= $_POST['nr_sucursal'];
            $nr_user= $_POST['nr_user'];
            if(empty($nr_user)) 
            {
                $nr_user = NULL;
            }
            $otros_datos= $_POST['otros_datos'];
            
            $personal->update_personal($nr,$id_personal,$nombre_apellido,$documento_nr,$direccion,$telefono,$email,$nr_tipo_personal,$nr_sucursal,$nr_user,$otros_datos);
            echo "<script>window.location.href = 'personal.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $personal->delete_personal($nr);
    }
?>