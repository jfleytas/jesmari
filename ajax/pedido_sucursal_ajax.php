<?php
	require '../clases/formularios/pedido_sucursal.class.php';
	$pedido_sucursal = pedido_sucursal::singleton();

	//Get the data from Deposito Stock for the specific Sucursal;
	@$sucursal_origen = $_POST['sucursal_origen'];
	if (isset($_POST['sucursal_origen'])){
		$dato_sucursal = $pedido_sucursal->get_deposito_stock($sucursal_origen); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Deposito Stock for the specific Sucursal;
	@$sucursal_origen2 = $_POST['sucursal_origen2'];
	if (isset($_POST['sucursal_origen2'])){
		$dato_sucursal_origen = $pedido_sucursal->query("select * from sucursal where nr <> '$sucursal_origen2'"); 
		foreach ($dato_sucursal_origen as $sucursal_destino_result) {
            $deposito_nr = $sucursal_destino_result['nr'];    
            echo '<option value="'.$sucursal_destino_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$sucursal_destino_result['descripcion'].'</option>';
        }
	}

	//Get the data from Deposito Destino for the specific Sucursal Destino;
	@$sucursal_destino = $_POST['sucursal_destino'];
	if (isset($_POST['sucursal_destino'])){
		$dato_sucursal = $pedido_sucursal->get_deposito_stock($sucursal_destino); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Producto;
	@$producto = $_POST['producto'];
	@$nr_moneda = $_POST['nr_moneda'];
	if (isset($_POST['producto'])){
		@$query = "select P.nr nr_producto, P.id_producto, P.descripcion descripcion_producto, UM.nr nr_unidad, UM.id_unidad_medida id_unidad 
		from productos P join unidad_medida UM on UM.nr = P.nr_unidad_medida where P.id_producto ='$producto'";
	    $producto_result = $pedido_sucursal->query($query);//We get the desired result from the table
	    $qty_register = $pedido_sucursal->rowCount($query);
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
            $costo_result = $pedido_sucursal->query($query);//We get the desired result from the table
            $qty_register = $pedido_sucursal->rowCount($query);
            if($qty_register>0) {
                //echo $costo_result;
                foreach ($costo_result as $resultado_costo) {        
                    $costo = $resultado_costo['cpp'];
                }
            }else{
                $costo = 0;
            }
			echo json_encode($nr_producto.",".$descripcion_producto.",".$nr_unidad.",".$id_unidad.",".$costo);
		}else{
			//echo 'alert("No existe el Producto")';
		}
	}

    //Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(total_linea) as total_general from detalle_pedido_sucursal DPS where DPS.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $pedido_sucursal->query($query);//We get all the results from the table
        $qty_register = $pedido_sucursal->rowCount($query);
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
    @$pedido = $_POST['pedido'];
    if (isset($_POST['pedido'])){
        /* Lista Detalle section */
        $query = "select * from detalle_pedido_sucursal DPS where DPS.nr = '$pedido'";
        //echo $query;
        $detalle_result = $pedido_sucursal->query($query);//We get all the results from the table
        @$qty_register = $pedido_sucursal -> rowCount($query);
        echo json_encode($qty_register.','.$pedido);
    }


	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' or upper(id_producto) like '%".$buscar_producto."%' and nr_tipo = 0 order by descripcion";
		//echo $query;
		$producto_result = $pedido_sucursal->query($query);
		$qty_register = $pedido_sucursal->rowCount($query);
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
        @$nr_sucursal = $_POST['nr_sucursal_origen'];
        @$nr_deposito = $_POST['nr_deposito_origen'];
        @$nr_producto =$_POST['nr_producto'];
        //Check the Stock
        @$query = "select stock_actual from stock_deposito_sucursal where nr_producto ='$nr_producto' and nr_sucursal ='$nr_sucursal' and nr_deposito ='$nr_deposito'";
        $stock_result = $pedido_sucursal->query($query);//We get the desired result from the table
        $qty_register = $pedido_sucursal->rowCount($query);
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
    @$listar_pedido = $_POST['listar_pedido'];
    if (isset($_POST['listar_pedido'])){
        /* Lista Detalle section */
        $query = "select DPS.nr, DPS.nr_producto, P.id_producto, P.descripcion descripcion_producto, DPS.cantidad, DPS.costo, DPS.total_linea, DPS.nr_unidad, UM.id_unidad_medida id_unidad, CPS.nr_moneda
        from detalle_pedido_sucursal DPS join productos P on DPS.nr_producto = P.nr join cabecera_pedido_sucursal CPS on CPS.nr = DPS.nr
        join unidad_medida UM on DPS.nr_unidad = UM.nr where DPS.nr = '$listar_pedido'";
        //echo $query;
        $detalle_result = $pedido_sucursal->query($query);//We get all the results from the table
        @$qty_register = $pedido_sucursal -> rowCount($query);
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