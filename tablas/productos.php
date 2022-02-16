<?php
    require '../tablas/menu.php';

    require '../clases/tablas/productos.class.php';
    $productos = productos::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $codigo_barra=isset($_POST['codigo_barra']) ? $_POST['codigo_barra'] : '';
    $descripcion=isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $nr_marca=isset($_POST['nr_marca']) ? $_POST['nr_marca'] : '';
    $nr_unidad_medida=isset($_POST['nr_unidad_medida']) ? $_POST['nr_unidad_medida'] : '';
    $nr_impuesto=isset($_POST['nr_impuesto']) ? $_POST['nr_impuesto'] : '';
    $nr_clasificacion=isset($_POST['nr_clasificacion']) ? $_POST['nr_clasificacion'] : '';
    $empaque=isset($_POST['empaque']) ? $_POST['empaque'] : '';
    $otros_datos=isset($_POST['otros_datos']) ? $_POST['otros_datos'] : '';
    $nr_tipo=isset($_POST['nr_tipo']) ? $_POST['nr_tipo'] : '';
    $nr_tipo_value=isset($_POST['nr_tipo_value']) ? $_POST['nr_tipo_value'] : '';
    $fecha_alta=isset($_POST['fecha_alta']) ? $_POST['fecha_alta'] : '';
    $temporada=isset($_POST['temporada']) ? $_POST['temporada'] : '';
    $inicio_temporada=isset($_POST['inicio_temporada']) ? $_POST['inicio_temporada'] : '';
    $fin_temporada=isset($_POST['fin_temporada']) ? $_POST['fin_temporada'] : '';

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

    $query = "select P.nr nr, P.id_producto id_producto, P.codigo_barra, P.descripcion descripcion, P.nr_marca nr_marca, M.descripcion descripcion_marca, P.nr_unidad_medida nr_unidad_medida, P.nr_impuesto nr_impuesto, P.nr_clasificacion nr_clasificacion, P.empaque empaque, P.otros_datos otros_datos, P.nr_tipo nr_tipo, P.fecha_alta, P.temporada, P.inicio_temporada, P.fin_temporada, TP.descripcion descripcion_tipo  from productos P left join marca M on P.nr_marca = M.nr join unidad_medida U on P.nr_unidad_medida = U.nr join tipo_impuesto TI on P.nr_impuesto = TI.nr left join Clasificacion C on P.nr_clasificacion = C.nr join tipo_producto TP on P.nr_tipo = TP.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $productos -> rowCount($query);


    //
    /*@$key=$_GET['key'];
    $array = array();
    $query1= $query . " WHERE id_producto LIKE '%{$key}%' OR descripcion LIKE '%{$key}%' OR descripcion_marca LIKE '%{$key}%'";
    echo $query1;
    $test = $productos->get_productos($query1);


    foreach($test as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_producto'] . '</td>';
            echo '<td>'. $total_row['descripcion'] . '</td>';
            echo '<td>'. $total_row['descripcion_tipo'] . '</td>';
            echo '</tr>';
    endforeach;


    while($row=pg_fetch_assoc($test))
    {
      $array[] = $row['id_producto'];
    }
    echo json_encode($array);*/





    /*if(isset($_REQUEST['search']) && $_REQUEST['search'] != "")
    {
        $search = htmlspecialchars($_REQUEST["search"]);
        $productos->param = "&search=$searchstring";
        $query = $query . " WHERE id_producto LIKE '%$searchstring%' OR descripcion LIKE '%$searchstring%' OR descripcion_marca LIKE '%$searchstring%'";
        $qty_register = $productos -> rowCount($query);
    }*/

    if (!empty($_REQUEST['searchstring']))
    {
        //$searchstring = mysql_real_escape_string($_REQUEST['searchstring']); 
        $qty_register = $productos -> rowCount($query);
        $query = $query . " WHERE id_producto LIKE '%$searchstring%' OR descripcion LIKE '%$searchstring%' OR descripcion_marca LIKE '%$searchstring%'";
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
        case 'id_producto':  
            $order_by = 'id_producto';  
        break;  
        case 'descripcion':  
            $order_by = 'descripcion';  
        break;   
        case 'descripcion_tipo':  
            $order_by = 'descripcion_tipo';  
        break;
    }  

    $query = $query . " order by $sort $sort_order"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   

    $total_result = $productos->get_productos($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $productos->get_productos("select P.nr nr, P.id_producto id_producto, P.codigo_barra, P.descripcion descripcion, P.nr_marca nr_marca, P.nr_unidad_medida nr_unidad_medida, P.nr_impuesto nr_impuesto, P.nr_clasificacion nr_clasificacion, P.empaque empaque, P.otros_datos otros_datos, P.nr_tipo nr_tipo, TP.descripcion descripcion_tipo, P.fecha_alta, P.temporada, P.inicio_temporada, P.fin_temporada from productos P left join marca M on P.nr_marca = M.nr join unidad_medida U on P.nr_unidad_medida = U.nr join tipo_impuesto TI on P.nr_impuesto = TI.nr left join Clasificacion C on P.nr_clasificacion = C.nr join tipo_producto TP on P.nr_tipo = TP.nr where P.nr ='$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_producto = $row['id_producto'];
            $codigo_barra = $row['codigo_barra'];
            $descripcion = $row['descripcion'];
            $nr_marca = $row['nr_marca'];
            $nr_unidad_medida = $row['nr_unidad_medida'];
            $nr_impuesto = $row['nr_impuesto'];
            $nr_clasificacion = $row['nr_clasificacion'];
            $empaque = $row['empaque'];
            $otros_datos = $row['otros_datos'];
            $nr_tipo = $row['nr_tipo'];
            $fecha_alta = $row['fecha_alta'];
            $temporada = $row['temporada'];
            $inicio_temporada = $row['inicio_temporada'];
            $fin_temporada = $row['fin_temporada'];
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
<script src="../js/jquery.js"></script>
<script>
    //For dependent Selects
    $(document).ready(function()
    {
        $(".nr_tipo").change(function()
        {
            //var nr_tipo=$(this).val();
            var nr_tipo=document.getElementById("nr_tipo").value;
            var dataString = 'nr_tipo='+ nr_tipo;
            $.ajax
            ({
                type: "POST",
                url: "../ajax/producto_ajax.php",
                data: dataString,
                cache: false,
                success: function(html)
                {
                    $(".nr_clasificacion").html(html);
                } 
            });
        });
    });
</script>
</head>
<body>
<form class="form-horizontal" action="productos.php" method="POST" name = "productos" autocomplete = "off">
<h2>Productos / Servicios</h2>
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
    <input type="text" id="searchstring" name="searchstring" placeholder="Buscar" value="<?php echo $searchstring ?>">
    <input type="submit" id="search" value="Buscar"></p>


    <input type="text" name="typeahead">

    <script>
    $(document).ready(function(){
    $('input.typeahead').typeahead({
        name: 'typeahead',
        remote:'productos.php?key=%QUERY',
        limit : 10
        });
    });
    </script>-->

<div>
<?php
    echo '<table class="list">';
       echo '<thead>';
            echo '<tr>';
                echo '<th><a href="?sort=id_producto&sort_by='.$sort_order.'">Codigo</th>';
                echo '<th><a href="?sort=descripcion&sort_by='.$sort_order.'">Descripcion</th>';
                echo '<th><a href="?sort=descripcion_tipo&sort_by='.$sort_order.'">Tipo</th>';
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        //Check if more than 0 records were found
        if($qty_register > 0){
            foreach($total_result as $total_row):
            echo '<tr>';
            echo '<td>'. $total_row['id_producto'] . '</td>';
            echo '<td>'. $total_row['descripcion'] . '</td>';
            echo '<td>'. $total_row['descripcion_tipo'] . '</td>';
            echo '<td><a href="productos.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="productos.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
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

<!--<a id="div-btn1" style="cursor:pointer;">Ver el archivo que contiene hola</a>
<div id="div-results"></div>-->


	<div id="modal" class="modalstyle">
		<div class="modalbox movedown">
            <caption style="text-align: center">Productos / Servicios</caption><hr><p>
			<a href="productos.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    <b><label class="control-label">Codigo de Barra</label></b>
                    <input type="text" id="codigo_barra" name="codigo_barra" value="<?php echo $codigo_barra;?>" class="boxes" maxlength = 15><p>
                    <b><label class="control-label">Descripcion</label></b>
                    <textarea id="descripcion" name="descripcion" class="boxes" rows="5" required><?php echo $descripcion;?></textarea><p>
                    <b><label class="control-label">Tipo</label></b>
                    <select id= "nr_tipo" name="nr_tipo" class="nr_tipo" required>
                         <option value="" selected>Seleccione:</option>
                        <?php 
                            $tipo_producto_result = $productos->get_tipo_producto();//We get all the results from the table Marca
                            foreach ($tipo_producto_result as $row) {
                            $tipo_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_tipo==$tipo_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Marca</label></b>
                    <select name="nr_marca" class="boxes">
                        <option value="" selected></option>
                        <?php 
                            $marca_result = $productos->get_marca();//We get all the results from the table Marca
                            foreach ($marca_result as $row) {
                            $marca_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_marca==$marca_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Unid. Medida</label></b>
                    <select id="nr_unidad_medida" name="nr_unidad_medida" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $unidad_medida_result = $productos->get_unidad_medida();//We get all the results from the table Unidad_Medida
                            foreach ($unidad_medida_result as $row) {
                            $unidad_medida_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_unidad_medida==$unidad_medida_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Impuesto</label></b>
                    <select name="nr_impuesto" required class="boxes">
                            <option value="" selected>Seleccione:</option>
                        <?php 
                            $impuesto_result = $productos->get_impuesto();//We get all the results from the table Unidad_Medida
                            foreach ($impuesto_result as $row) {
                            $impuesto_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_impuesto==$impuesto_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Clasificacion</label></b>
                    <!--Here we display the values according to the Tipo Producto selected-->
                    <select name="nr_clasificacion" class="nr_clasificacion" >
                    <option value="" selected></option>
                    </select><p>
                    <b><label class="control-label">Empaque</label></b>
                    <input type="text" id="empaque" name="empaque" value="<?php echo $empaque;?>" class="boxes" required><p>
                    <b><label class="control-label">Otros Datos</label></b>
                    <input type="text" id="otros_datos" name="otros_datos" value="<?php echo $otros_datos;?>" class="boxes"><p>
                    <b><label class="control-label">Temporada?</label></b>
                    <input type="checkbox" id="temporada" name="temporada" value="<?php echo $temporada;?>" onchange= "mostrarFechas(this.selectedIndex)"><p>
                    <script language="Javascript">
                        function mostrarFechas(value) 
                        {
                            element = document.getElementById("hideContent");
                            temporada = document.getElementById("temporada");
                            valor_temporada = temporada.value;
                            //document.write(valor_temporada);
                            if ((temporada.checked) || (temporada == "1"))
                            {
                                <?php $temporada = "t";?>
                                element.style.display='block';
                            }else {
                                <?php $temporada = "f";?>
                                element.style.display='none';
                            }
                         }
                        <?php if (($temporada == "1") or ($temporada == "t")) {?>
                            alert ('True');
                            document.getElementById("temporada").checked = true;
                            mostrarFechas($temporada);
                        <?php } ?>   
                    </script>
                    <div id="hideContent" style="display: none;">
                        <?php
                            $meses = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
                        ?>
                        <b><label class="control-label">Inicio Temporada</label></b>
                        <select name="inicio_temporada" class="boxes">
                            <option value="" selected>Seleccione:</option>
                        <?php 
                            for ($i=1; $i<=12; $i++)
                            { 
                                $temporada_inicio = $i;
                                echo '<option value="'.$i.'"';
                                if ($inicio_temporada==$temporada_inicio) echo 'selected="selected"';
                                echo '>'.$meses[$i].'</option>';  
                            }   
                        ?>
                        </select><p>
                        <b><label class="control-label">Fin Temporada</label></b>
                        <select name="fin_temporada" class="boxes">
                            <option value="" selected>Seleccione:</option>
                        <?php 
                            for ($i=1; $i<=12; $i++)
                            { 
                                $temporada_fin = $i;
                                echo '<option value="'.$i.'"';
                                if ($fin_temporada==$temporada_fin) echo 'selected="selected"';
                                echo '>'.$meses[$i].'</option>';  
                            }   
                        ?>
                        </select><p>
                    </div>
                    <b><label class="control-label">Fecha de Alta</label></b>
                    <input type="date" id="fecha_alta" name="fecha_alta" value="<?php echo $fecha_alta;?>" class="boxes" readonly disabled><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="productos.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="productos.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
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
                window.location.href = "productos.php";
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
            $id_producto = $_POST['id_producto'];
            $codigo_barra = $_POST['codigo_barra'];
            if(empty($codigo_barra)) 
            {
                $codigo_barra = NULL;
            }
            $descripcion = $_POST['descripcion'];
            $nr_marca = $_POST['nr_marca'];
            if(empty($nr_marca)) 
            {
                $nr_marca = NULL;
            }
            $nr_unidad_medida = $_POST['nr_unidad_medida'];
            $nr_impuesto = $_POST['nr_impuesto'];
            $nr_clasificacion = $_POST['nr_clasificacion'];
            if(empty($nr_clasificacion)) 
            {
                $nr_clasificacion = NULL;
            }
            $empaque = $_POST['empaque'];
            $otros_datos = $_POST['otros_datos'];
            if(empty($otros_datos)) 
            {
                $otros_datos = NULL;
            }
            $nr_tipo = $_POST['nr_tipo'];
            @$fecha_alta = $_POST['fecha_alta'];
            if(empty($fecha_alta)) 
            {
                $fecha_alta = NULL;
            }
            @$temporada = $_POST['temporada'];
            if (isset($_POST['temporada']) && ($_POST['temporada'] == "TRUE")) 
            {
                $temporada = '1';
                //$temporada = 't';
            } else {
                $temporada = '0';
                //$temporada = 'f';
            }
            $inicio_temporada = $_POST['inicio_temporada'];
            if(empty($inicio_temporada)) 
            {
                $inicio_temporada = '0';
            }
            $fin_temporada = $_POST['fin_temporada'];
            if(empty($fin_temporada)) 
            {
                $fin_temporada = '0';
            }
            
            $productos->insert_productos($id_producto,$codigo_barra,$descripcion,$nr_marca,$nr_unidad_medida,$nr_impuesto,$nr_clasificacion,$empaque,$otros_datos,$nr_tipo,$fecha_alta,$temporada,$inicio_temporada,$fin_temporada);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_producto = $_POST['id_producto'];
            $codigo_barra = $_POST['codigo_barra'];
            if(empty($codigo_barra)) 
            {
                $codigo_barra = NULL;
            }
            $descripcion = $_POST['descripcion'];
            $nr_marca = $_POST['nr_marca'];
            if(empty($nr_marca)) 
            {
                $nr_marca = NULL;
            }
            $nr_unidad_medida = $_POST['nr_unidad_medida'];
            $nr_impuesto = $_POST['nr_impuesto'];
            $nr_clasificacion = $_POST['nr_clasificacion'];
            if(empty($nr_clasificacion)) 
            {
                $nr_clasificacion = NULL;
            }
            $empaque = $_POST['empaque'];
            $otros_datos = $_POST['otros_datos'];
            if(empty($otros_datos)) 
            {
                $otros_datos = NULL;
            }
            $nr_tipo = $_POST['nr_tipo'];
            @$fecha_alta = $_POST['fecha_alta'];
            if(empty($fecha_alta)) 
            {
                $fecha_alta = NULL;
            }
            @$temporada = $_POST['temporada'];
            //if (isset($_POST['temporada']) && ($_POST['temporada'] == "TRUE")) 
            if ($temporada != "") 
            {
                $temporada = 't';
                //echo "<script>alert ('TRUE')</script>";   
            } else {
                $temporada = 'f';
            }
            $inicio_temporada = $_POST['inicio_temporada'];
            if(empty($inicio_temporada)) 
            {
                $inicio_temporada = '0';
            }
            $fin_temporada = $_POST['fin_temporada'];
            if(empty($fin_temporada)) 
            {
                $fin_temporada = '0';
            }   
            
            $productos->update_productos($nr,$id_producto,$codigo_barra,$descripcion,$nr_marca,$nr_unidad_medida,$nr_impuesto,$nr_clasificacion,$empaque,$otros_datos,$nr_tipo,$fecha_alta,$temporada,$inicio_temporada,$fin_temporada);
            echo "<script>window.location.href = 'productos.php'</script>";
        break;
    }
    }

    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $productos->delete_productos($nr);
    }
?>