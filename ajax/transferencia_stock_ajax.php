<?php
	require '../clases/formularios/transferencia_stock.class.php';
	$transferencia_stock = transferencia_stock::singleton();

	//Get the data from Deposito Stock Origen for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_sucursal = $transferencia_stock->query("select * from depositos_stock where nr_sucursal = '$sucursal' order by descripcion"); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data for Deposito Destino without the selection of Deposito Origen;
	@$deposito_origen = $_POST['deposito_origen'];
	@$sucursal_actual = $_POST['sucursal_actual'];
	if (isset($_POST['deposito_origen'])){
		$dato_deposito_origen = $transferencia_stock->query("select * from depositos_stock where nr_sucursal = '$sucursal_actual' and nr <> '$deposito_origen'"); ; 
		foreach ($dato_deposito_origen as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Producto;
	@$producto = $_POST['producto'];
	if (isset($_POST['producto'])){
		@$query = "select P.nr nr_producto, P.id_producto, P.descripcion descripcion_producto, I.nr nr_impuesto, I.valor valor, UM.nr nr_unidad, UM.id_unidad_medida id_unidad 
		from productos P join  tipo_impuesto I on I.nr = P.nr_impuesto join unidad_medida UM on UM.nr = P.nr_unidad_medida where P.id_producto ='$producto'";
	    $producto_result = $transferencia_stock->query($query);//We get the desired result from the table
	    $qty_register = $transferencia_stock->rowCount($query);
		if($qty_register>0) {
			//echo $producto_result;
			foreach ($producto_result as $resultado_producto) {        
			    $nr_producto = $resultado_producto['nr_producto'];
			    $id_producto = $resultado_producto['id_producto'];
			    $descripcion_producto = $resultado_producto['descripcion_producto'];
			    $impuesto = $resultado_producto['valor'];
		    	$nr_unidad = $resultado_producto['nr_unidad'];       
                $id_unidad = $resultado_producto['id_unidad'];
			}
			@$query = "select cpp from costos_productos where nr_producto ='$nr_producto'";
		    $costo_result = $transferencia_stock->query($query);//We get the desired result from the table
		    $qty_register = $transferencia_stock->rowCount($query);
			if($qty_register>0) {
				//echo $costo_result;
				foreach ($costo_result as $resultado_costo) {        
				    $costo = $resultado_costo['cpp'];
				}
			}else{
				$costo = 0;
			}
			
			echo json_encode($nr_producto.",".$descripcion_producto.",".$impuesto.",".$nr_unidad.",".$id_unidad.",".$costo);
		}else{
			//echo 'alert("No existe el Producto")';
		}
	}

	//Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(total_linea) as total_general from detalle_transferencia_stock DTS where DTS.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $transferencia_stock->query($query);//We get all the results from the table
        $qty_register = $transferencia_stock->rowCount($query);
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
    @$transferencia = $_POST['transferencia'];
    if (isset($_POST['transferencia'])){
        /* Lista Detalle section */
        $query = "select * from detalle_transferencia_stock DTS where DTS.nr = '$transferencia'";
        //echo $query;
        $detalle_result = $transferencia_stock->query($query);//We get all the results from the table
        @$qty_register = $transferencia_stock -> rowCount($query);
        echo json_encode($qty_register.','.$transferencia);
    }


	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' or upper(id_producto) like '%".$buscar_producto."%' and nr_tipo = 0 order by descripcion";
		//echo $query;
		$producto_result = $transferencia_stock->query($query);
		$qty_register = $transferencia_stock->rowCount($query);
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

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="VerificarStock"){
		@$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito = $_POST['nr_deposito_origen'];
        @$nr_producto=$_POST['nr_producto'];
        //Check the Stock
        @$query = "select stock_actual from stock_deposito_sucursal where nr_producto ='$nr_producto' and nr_sucursal ='$nr_sucursal' and nr_deposito ='$nr_deposito'";
	    $stock_result = $transferencia_stock->query($query);//We get the desired result from the table
	    $qty_register = $transferencia_stock->rowCount($query);
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
    @$listar_transferencia = $_POST['listar_transferencia'];
    if (isset($_POST['listar_transferencia'])){
        /* Lista Detalle section */
        $query = "select DTS.nr, DTS.nr_producto, P.id_producto, P.descripcion descripcion_producto, DTS.cantidad, DTS.costo, DTS.total_linea, DTS.nr_unidad, UM.id_unidad_medida id_unidad
        from detalle_transferencia_stock DTS join productos P on DTS.nr_producto = P.nr
        join unidad_medida UM on DTS.nr_unidad = UM.nr where DTS.nr = '$listar_transferencia'";
        //echo $query;
        $detalle_result = $transferencia_stock->query($query);//We get all the results from the table
        @$qty_register = $transferencia_stock -> rowCount($query);
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
                	$decimals = 0;
                    echo '<td>'. $i . '</td>';
                    echo '<td>'. $total_row['id_producto'] . '</td>';
                    echo '<td>'. $total_row['descripcion_producto'] . '</td>';
                    echo '<td>'. number_format($total_row['cantidad'],$decimals,",",".") . '</td>';
                    echo '<td>'. $total_row['id_unidad'] . '</td>';
                    echo '<td>'. number_format($total_row['costo'],$decimals,",",".") . '</td>';
                    echo '<td>'. number_format($total_row['total_linea'],$decimals,",",".") . '</td>';
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