<?php
	require '../clases/formularios/orden_compra.class.php';
	$orden_compra = orden_compra::singleton();

	//Get the data from Proveedor;
	@$proveedor = $_POST['proveedor'];
	if (isset($_POST['proveedor'])){
		@$query = "select * from proveedor where id_proveedor ='$proveedor'";
	    $proveedor_result = $orden_compra->query($query);//We get the desired result from the table
	    $qty_register = $orden_compra->rowCount($query);
		if($qty_register>0) {
		    foreach ($proveedor_result as $resultado_proveedor) {        
		        $nr_proveedor = $resultado_proveedor['nr'];
		        $id_proveedor = $resultado_proveedor['id_proveedor'];
		        $descripcion_proveedor = $resultado_proveedor['descripcion'];
		        $direccion = $resultado_proveedor['direccion'];
		        $telefono = $resultado_proveedor['telefono'];
		        $nr_condicion= $resultado_proveedor['nr_condicion'];
		    }
			echo json_encode($nr_proveedor.",".$descripcion_proveedor.",".$nr_condicion);
		/*}else{
			echo 'alert("No existe el Proveedor")';*/
		}
	}

	//Get the data from Deposito Stock for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_sucursal = $orden_compra->get_deposito_stock($sucursal); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Producto and Precio Producto Proveedor;
	@$producto = $_POST['producto'];
	@$nr_proveedor = $_POST['nr_proveedor'];
	@$nr_moneda = $_POST['nr_moneda'];
	if (isset($_POST['producto'])){
		@$query = "select P.nr nr_producto, P.id_producto, P.descripcion descripcion_producto, I.nr nr_impuesto, I.valor valor, UM.nr nr_unidad, UM.id_unidad_medida id_unidad 
		from productos P join  tipo_impuesto I on I.nr = P.nr_impuesto join unidad_medida UM on UM.nr = P.nr_unidad_medida where P.id_producto ='$producto'";
	    $producto_result = $orden_compra->query($query);//We get the desired result from the table
	    $qty_register = $orden_compra->rowCount($query);
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

			if ($nr_producto)
    		{
	            @$query = "select PPP.precio precio_lista from precio_producto_proveedor PPP join productos P on PPP.nr_producto = P.nr 
				join proveedor PR on PPP.nr_proveedor = PR.nr join moneda M on PPP.nr_moneda = M.nr  where PR.nr = '$nr_proveedor' and P.nr ='$nr_producto' and PPP.nr_moneda = '$nr_moneda'"; 
				$precio_result = $orden_compra->query($query);//We get the desired result from the table
				$qty_register = $orden_compra -> rowCount($query);
				if($qty_register > 0){
			    	foreach ($precio_result as $resultado_precio) {  
			    		$precio_lista = $resultado_precio['precio_lista'];
			    	}
				}else{
			    	$precio_lista = 0;
				}
	    	}
	    	echo json_encode($nr_producto.";".$descripcion_producto.";".$impuesto.";".$nr_unidad.";".$id_unidad.";".$precio_lista);
		}else{
			//echo 'alert("No existe el Producto")';
		}
	}

	//Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(total_linea) as total_general, sum(total_iva_linea) as total_iva from detalle_orden_compra DOC where DOC.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $orden_compra->query($query);//We get all the results from the table
        $qty_register = $orden_compra->rowCount($query);
	    foreach($detalle_result as $total_row):
	        $total_general = $total_row['total_general'];
	    	$total_iva = $total_row['total_iva'];
	    endforeach;
        if (empty($total_general))
        {
        	$total_general = 0;
        	$total_iva = 0;
        }
        echo json_encode($total_general.','.$total_iva.','.$orden_nr);
    }

	//Check the qty on Detalle
	//Get the number to show the list of items
    @$orden_compra_nr = $_POST['orden_compra_nr'];
    if (isset($_POST['orden_compra_nr'])){
        /* Lista Detalle section */
        $query = "select * from detalle_orden_compra DOC where DOC.nr = '$orden_compra_nr'";
        //echo $query;
        $detalle_result = $orden_compra->query($query);//We get all the results from the table
        @$qty_register = $orden_compra -> rowCount($query);
        echo json_encode($qty_register.','.$orden_compra_nr);
    }

	//Search section
	if(!empty($_POST["buscar_proveedor"])) {
		//The variable buscar_proveedor is already in uppercase
		@$buscar_proveedor = $_POST["buscar_proveedor"];
		//We also uppercase the database columns to match with the variable->buscar_proveedor
		@$query = "select * from proveedor P where upper(descripcion) like '%".$buscar_proveedor."%' or upper (id_proveedor) like '%".$buscar_proveedor."%' order by descripcion";
		//echo $query;
		$proveedor_result = $orden_compra->query($query);
		$qty_register = $orden_compra->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="proveedor-list">';
			foreach($proveedor_result as $result_proveedor) {
				$resultado = $result_proveedor["id_proveedor"].'||'.$result_proveedor["descripcion"];
				?>
				<li onClick="selectProveedor('<?php echo $result_proveedor["id_proveedor"]; ?>');">
				<?php echo $resultado; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}


	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' or upper (id_producto) like '%".$buscar_producto."%' order by descripcion";
		//echo $query;
		$producto_result = $orden_compra->query($query);
		$qty_register = $orden_compra->rowCount($query);
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

	//Get the number to show the list of items
    @$listar_orden = $_POST['listar_orden'];
    if (isset($_POST['listar_orden'])){
        /* Lista Detalle section */
        $query = "select DOC.nr, DOC.nr_producto, P.id_producto, P.descripcion descripcion_producto, DOC.cantidad, DOC.descuento, DOC.precio_lista, DOC.precio_final, DOC.total_linea, DOC.impuesto, DOC.nr_unidad, UM.id_unidad_medida id_unidad, COC.nr_moneda
        from detalle_orden_compra DOC join productos P on DOC.nr_producto = P.nr join cabecera_orden_compra COC on COC.nr = DOC.nr
        join unidad_medida UM on DOC.nr_unidad = UM.nr where DOC.nr = '$listar_orden'";
        //echo $query;
        $detalle_result = $orden_compra->query($query);//We get all the results from the table
        @$qty_register = $orden_compra -> rowCount($query);

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
                echo '<th>Precio Lista</th>';
                echo '<th>I.</th>';
                echo '<th>Desc. %</th>';
                echo '<th>Precio Final</th>';
                echo '<th>Total</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
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
                    echo '<td>'. number_format($total_row['precio_lista'],$decimal,",",".") . '</td>';
                    echo '<td>'. number_format($total_row['impuesto'],$decimal,",",".")  . '</td>';
                    echo '<td>'. number_format($total_row['descuento'],$decimal,",",".")  . '</td>';
                    echo '<td>'. number_format($total_row['precio_final'],$decimal,",",".") . '</td>';
                    echo '<td>'. number_format($total_row['total_linea'],$decimal,",",".") . '</td>';
                    echo '<td><a onclick="EditarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].')" href="#modal"  title="Editar" class = "edit"></a></td>';
                    echo '<td><a onclick="EliminarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].')"  title="Borrar" class = "delete"></a></td>';
                echo '</tr>';
                endforeach;
        }else{
            //$texto = "Ningun producto agregado.";
            //echo "<b>".$texto."</b>";
        }
            echo '</tbody>';
        echo '</table>';

    }


	//Verify if the Orden de Compra has an Invoice;
	@$orden_estado = $_POST['orden_estado'];
	if (isset($_POST['orden_estado'])){
	    $query = "select * from cabecera_factura_compra where orden_compra = '$orden_estado'"; 
		$factura_result = $orden_compra->query($query);//We get the desired result from the table
		$qty_register = $orden_compra -> rowCount($query);
		if($qty_register > 0){
			$factura = 1;
		}else{
			$factura = 0;
		}
		echo json_encode($orden_estado.",".$factura);
	}

	//Verify if the Orden de Compra has an Ingreso Proveedor;
	@$orden_ingreso_estado = $_POST['orden_ingreso_estado'];
	if (isset($_POST['orden_ingreso_estado'])){
	    $query = "select * from cabecera_ingreso_proveedor where orden_compra = '$orden_ingreso_estado'"; 
		$ingreso_result = $orden_compra->query($query);//We get the desired result from the table
		$qty_register = $orden_compra -> rowCount($query);
		if($qty_register > 0){
			$ingresado = 1;
		}else{
			$ingresado = 0;
		}
		echo json_encode($orden_ingreso_estado.",".$ingresado);
	}
?>

