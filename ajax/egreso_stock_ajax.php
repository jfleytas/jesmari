<?php
	require '../clases/formularios/egreso_stock.class.php';
	$egreso_stock = egreso_stock::singleton();

	//Get the data from Deposito Stock Origen for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_sucursal = $egreso_stock->query("select * from depositos_stock where nr_sucursal = '$sucursal' order by nr"); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Producto;
	@$producto = $_POST['producto'];
	if (isset($_POST['producto'])){
		@$query = "select P.nr nr_producto, P.id_producto, P.descripcion descripcion_producto, I.valor valor, UM.nr nr_unidad, UM.id_unidad_medida id_unidad 
		from productos P join  tipo_impuesto I on I.nr = P.nr_impuesto join unidad_medida UM on UM.nr = P.nr_unidad_medida where P.id_producto ='$producto'";
	    $producto_result = $egreso_stock->query($query);//We get the desired result from the table
	    $qty_register = $egreso_stock->rowCount($query);
		if($qty_register>0) {
			//echo $producto_result;
			foreach ($producto_result as $resultado_producto) {        
			    $nr_producto = $resultado_producto['nr_producto'];
			    $id_producto = $resultado_producto['id_producto'];
			    $descripcion_producto = $resultado_producto['descripcion_producto'];
		    	$nr_unidad = $resultado_producto['nr_unidad'];       
                $id_unidad = $resultado_producto['id_unidad'];
			}
            @$query = "select cpp from costos_productos where nr_producto ='$nr_producto'";
            $costo_result = $egreso_stock->query($query);//We get the desired result from the table
            $qty_register = $egreso_stock->rowCount($query);
            if($qty_register>0) {
                //echo $costo_result;
                foreach ($costo_result as $resultado_costo) {        
                    $costo = $resultado_costo['cpp'];
                }
            }else{
                $costo = 0;
            }
			echo json_encode($nr_producto.";".$descripcion_producto.";".$nr_unidad.";".$id_unidad.";".$costo);
		}else{
			//echo 'alert("No existe el Producto")';
		}
	}

    //Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(total_linea) as total_general from detalle_egreso_stock DES where DES.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $egreso_stock->query($query);//We get all the results from the table
        $qty_register = $egreso_stock->rowCount($query);
        foreach($detalle_result as $total_row):
            $total_general = $total_row['total_general'];
            //$total_iva = $total_row['total_iva'];
        endforeach;
        if (empty($total_general))
        {
            $total_general = 0;
            //$total_iva = 0;
        }
        echo json_encode($total_general.','.$orden_nr);
    }

    //Check the qty on Detalle
    //Get the number to show the list of items
    @$egreso = $_POST['egreso'];
    if (isset($_POST['egreso'])){
        /* Lista Detalle section */
        $query = "select * from detalle_egreso_stock DES where DES.nr = '$egreso'";
        //echo $query;
        $detalle_result = $egreso_stock->query($query);//We get all the results from the table
        @$qty_register = $egreso_stock -> rowCount($query);
        echo json_encode($qty_register.','.$egreso);
    }


	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' or upper(id_producto) like '%".$buscar_producto."%' and nr_tipo = 0 order by descripcion";
		//echo $query;
		$producto_result = $egreso_stock->query($query);
		$qty_register = $egreso_stock->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="producto-list">';
			foreach($producto_result as $result_producto) {
                $resultado = $result_producto["id_producto"].'||'.$result_producto["descripcion"];
				?>
				<li onClick="selectProducto('<?php echo $result_producto["id_producto"]; ?>');">
				<?php echo $resultado; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}

    //Check the Stock
    @$action = $_POST['action'];
    if ($action=="VerificarStock"){
        @$nr_sucursal = $_POST['nr_sucursal'];
        @$nr_deposito = $_POST['nr_deposito'];
        @$nr_producto =$_POST['nr_producto'];
        //Check the Stock
        @$query = "select stock_actual from stock_deposito_sucursal where nr_producto ='$nr_producto' and nr_sucursal ='$nr_sucursal' and nr_deposito ='$nr_deposito'";
        $stock_result = $egreso_stock->query($query);//We get the desired result from the table
        $qty_register = $egreso_stock->rowCount($query);
        if($qty_register>0) {
            //echo $stock_result;
            foreach ($stock_result as $resultado_stock) {        
                $cantidad = $resultado_stock['stock_actual'];
            }
        }else{
            $cantidad = 0;
        }
        echo json_encode($cantidad.",".$nr_sucursal.",".$nr_deposito);
    }


	//Get the number to show the list of items
    @$listar_egreso = $_POST['listar_egreso'];
    if (isset($_POST['listar_egreso'])){
        /* Lista Detalle section */
        $query = "select DES.nr, DES.nr_producto, P.id_producto, P.descripcion descripcion_producto, DES.cantidad, DES.costo, DES.total_linea, DES.nr_unidad, UM.id_unidad_medida id_unidad, CES.nr_moneda
        from detalle_egreso_stock DES join productos P on DES.nr_producto = P.nr join cabecera_egreso_stock CES on CES.nr = DES.nr
        join unidad_medida UM on DES.nr_unidad = UM.nr where DES.nr = '$listar_egreso'";
        //echo $query;
        $detalle_result = $egreso_stock->query($query);//We get all the results from the table
        @$qty_register = $egreso_stock -> rowCount($query);
        if($qty_register > 0){
        echo '<table id="detalle-list">';
            echo '<p><b>Productos Agregados</b></p>';
            echo '<thead>';
            echo '<tr>';
                echo '<th>#</th>';
                echo '<th>Codigo</th>';
                echo '<th>Descripcion</th>';
                echo '<th>Cant.</th>';
                echo '<th>Unid.Med.</th>';
                echo '<th>Costo</th>';
                echo '<th>Total</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            //Check if more than 0 records were found
            //if($qty_register > 0){
                foreach($detalle_result as $total_row):
                @$i= $i + 1;
                echo '<tr>';
                    if ($total_row['nr_moneda']==1)
                    {
                        $decimal = 0;
                    }else{
                        $decimal = 2;
                    }
                    echo '<td>'. $i . '</td>';
                    echo '<td>'. $total_row['id_producto'] . '</td>';
                    echo '<td>'. $total_row['descripcion_producto'] . '</td>';
                    echo '<td>'. number_format($total_row['cantidad'],$decimal,",",".") . '</td>';
                    echo '<td>'. $total_row['id_unidad'] . '</td>';
                    echo '<td>'. number_format($total_row['costo'],$decimal,",",".") . '</td>';
                    echo '<td>'. number_format($total_row['total_linea'],$decimal,",",".") . '</td>';
                    echo '<td><input type="button" value="Eliminar" id="eliminardetalle" name="eliminardetalle" class="eliminardetalle" "eliminarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].')"></td>';
                    echo '<td class = "buttons-column"><a href="#" onclick="editarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].'"  title="Borrar" class = "delete">Eliminar</a></td>';
                echo '</tr>';
                endforeach;
        }else{
            //$texto = "Ningun producto agregado.";
            //echo "<b>".$texto."</b>";
        }
            echo '</tbody>';
        echo '</table>';
    }
?>