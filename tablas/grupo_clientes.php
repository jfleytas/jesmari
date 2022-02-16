<?php
    require '../tablas/menu.php';
    require '../clases/tablas/grupo_clientes.class.php';
    $grupo_clientes = grupo_clientes::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_grupo=isset($_POST['id_grupo']) ? $_POST['id_grupo'] : '';
    $descripcion=isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $nr_lista_precios=isset($_POST['nr_lista_precios']) ? $_POST['nr_lista_precios'] : '';
    $moneda_lista_precios=isset($_POST['moneda_lista_precios']) ? $_POST['moneda_lista_precios'] : '';
    $moneda=isset($_POST['moneda']) ? $_POST['moneda'] : '';

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

    $query = "select * from grupo_clientes";
    //Search query
    $searchstring = null;    
    $qty_register = $grupo_clientes -> rowCount($query);
    if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $grupo_clientes->param = "&search=$searchstring";
        $query = $query . " WHERE id_grupo LIKE '%$searchstring%' OR descripcion LIKE '%$searchstring%'";
        $qty_register = $grupo_clientes -> rowCount($query);
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
        case 'id_grupo':  
            $order_by = 'id_grupo';  
        break;  
        case 'descripcion':  
            $order_by = 'descripcion';  
        break;
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $grupo_clientes->get_grupo_clientes($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        //$result = $grupo_clientes->get_grupo_clientes("select * from grupo_clientes where nr = '$parameter'"); //We get the desired result from the table
        $result = $grupo_clientes->query("select GC.nr, GC.id_grupo, GC.descripcion, GC.nr_lista_precios, LP.descripcion lp_descripcion, LP.nr_moneda, M.descripcion descripcion_moneda 
        from grupo_clientes GC join lista_de_precios LP on GC.nr_lista_precios = LP.nr join moneda M on LP.nr_moneda = M.nr where GC.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_grupo = $row['id_grupo'];
            $descripcion = $row['descripcion'];
            $nr_lista_precios = $row['nr_lista_precios'];
            $moneda_lista_precios = $row['descripcion_moneda'];
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<script src="../js/jquery.js"></script>
</head>
<body>
<form id = "grupo_clientes" class="form-horizontal" action="grupo_clientes.php" method="POST" name = "grupo_clientes" autocomplete = "off">
<!-- AJAX section -->
<script>
    function getMoneda()
        {
            var lista_precios=document.getElementById("nr_lista_precios").value;
            //alert(lista_precios);
            $.ajax({
                type: "POST",
                url: "../ajax/grupo_clientes_ajax.php",
                data: {"lista_precios_nr":lista_precios},
                dataType : 'json',
                success: function(resultado)
                    {
                        var data = resultado.split(",");
                        $('#moneda_lista_precios').val(data[1]);
                    }
            });
        } 
</script>
<h2>Grupo Clientes</h2>
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
                echo '<th><a href="?sort=id_grupo&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=descripcion&sort_by='.$sort_order.'">Descripcion</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
                foreach($total_result as $total_row):
                    echo '<tr>';
                    echo '<td>'. $total_row['id_grupo'] . '</td>';
                    echo '<td>'. $total_row['descripcion'] . '</td>';
                    echo '<td><a href="grupo_clientes.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
                    echo '<td><a onclick="return confirmarBorrado()" href="grupo_clientes.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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
            <caption style="text-align: center">Grupo Clientes</caption><hr><p>
			<a href="grupo_clientes.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_grupo" name="id_grupo" value="<?php echo $id_grupo;?>" required class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Descripcion</label></b>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo $descripcion;?>" class="boxes" required><p>
                    <b><label class="control-label">Lista de Precios</label></b>
                    <select id="nr_lista_precios" name="nr_lista_precios" onchange="getMoneda()" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $lista_precios_result = $grupo_clientes->get_lista_precios();//We get all the results from the table Lista de Precios
                            foreach ($lista_precios_result as $row) {
                            $lista_precios_nr = $row['nr']; 
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_lista_precios==$lista_precios_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Moneda</label></b>
                    <input type="text" id="moneda_lista_precios" name="moneda_lista_precios" value="<?php echo $moneda_lista_precios;?>" class="boxes" readonly><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="grupo_clientes.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="grupo_clientes.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }?>
                    </div>
                    </form>
                </div>
            </div> 
		</div>
	</div>
<div>
</div>

    <script language="Javascript">
        function confirmarBorrado()
        {
            eliminar=confirm("¿Deseas eliminar este registro?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "grupo_clientes.php";
        }
    </script>
</body>
</html>

<?php
    if (isset($_POST['action'])){
    switch($_POST['action'])
    {
        case "Crear": 
            $id_grupo = $_POST['id_grupo'];
            $descripcion = $_POST['descripcion'];
            $nr_lista_precios = $_POST['nr_lista_precios'];

            $grupo_clientes->insert_grupo_clientes($id_grupo,$descripcion,$nr_lista_precios);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_grupo = $_POST['id_grupo'];
            $descripcion = $_POST['descripcion'];
            $nr_lista_precios = $_POST['nr_lista_precios'];
            
            $grupo_clientes->update_grupo_clientes($nr,$id_grupo,$descripcion,$nr_lista_precios);
            echo "<script>window.location.href = 'grupo_clientes.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $grupo_clientes->delete_grupo_clientes($nr);
    }
?>