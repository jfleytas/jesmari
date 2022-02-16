<?php
    require '../tablas/menu.php';

    require '../clases/tablas/productos.class.php';
    $productos = productos::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $producto_desde=isset($_POST['producto_desde']) ? $_POST['producto_desde'] : '';
    $producto_hasta=isset($_POST['producto_hasta']) ? $_POST['producto_hasta'] : '';

    $result_config = $productos->query("select * from configuracion");

    foreach($result_config as $config) {
        $nombre_empresa = $config['nombre_empresa'];
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

    $query = "select P.nr nr, P.id_producto id_producto, P.codigo_barra, P.descripcion descripcion, P.nr_marca nr_marca, M.descripcion descripcion_marca, P.nr_unidad_medida nr_unidad_medida, P.nr_impuesto nr_impuesto, P.nr_clasificacion nr_clasificacion, P.empaque empaque, P.otros_datos otros_datos, P.nr_tipo nr_tipo, P.fecha_alta, P.temporada, P.inicio_temporada, P.fin_temporada, TP.descripcion descripcion_tipo  from productos P left join marca M on P.nr_marca = M.nr join unidad_medida U on P.nr_unidad_medida = U.nr join tipo_impuesto TI on P.nr_impuesto = TI.nr join Clasificacion C on P.nr_clasificacion = C.nr join tipo_producto TP on P.nr_tipo = TP.nr";   
    $qty_register = $productos -> rowCount($query);

    //Order query
    $query = $query . " order by id_producto"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    //echo $query;   
    $total_result = $productos->get_productos($query);//We get all the results from the table
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action="productos.php" method="POST" name = "productos">
<h2>Lista de Productos / Servicios</h2>
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

</center>
</div> 
	<div id="modal" class="modalstyle">
		<div class="modalbox movedown">
            <caption style="text-align: center">Productos / Servicios</caption><hr><p>
			<a href="productos.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto;?>" class="boxes" autofocus accesskey="b" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required><p>
                    
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
                    <select name="nr_unidad_medida" required class="boxes">
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
                   
                    <p>
                    <b><label class="control-label">Clasificacion</label></b>
                    <?php 
                        @$parametro=$_POST['parametro'];
                        if((empty($nr_tipo)) && (empty($nr))) //If parameter is not set and also is a new record
                        {
                            @$parametro= 0;
                        }else if (!empty($nr)){ //If is not a new record, is a edit, then take the value from the nr_tipo
                            @$parametro=$_GET['nr_tipo'];
                        }

                        $clasificacion_result = $productos->get_clasificacion($parametro);
                        //echo '<script>alert("Valor: "'.$parametro.')</script>';
                    ?>
                    <select name="nr_clasificacion" class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php foreach ($clasificacion_result as $row) {
                            $clasificacion_nr = $row['nr'];
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_clasificacion==$clasificacion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>'; 
                        }?>
                    </select><p>
                   
                    <div class="form-actions">
                        <p><p><input type="submit" class="btn-success" id="ejecutar" name="ejecutar" value ="Ejecutar">
                    </div>
                    </form>
                </div>
            </div> 
		</div>
	</div>

</form>
</body>
</html>


<?php
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $productos->delete_productos($nr);
    }
?>