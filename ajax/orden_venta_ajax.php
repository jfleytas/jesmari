<?php
	require '../clases/formularios/orden_venta.class.php';
	$orden_venta = orden_venta::singleton();

	//Get the data from Cliente;
	@$cliente = $_POST['cliente'];
	if (isset($_POST['cliente'])){
	    $cliente_result = $orden_venta->query("select C.nr nr_cliente, C.id_cliente, C.nr_grupo, C.nr_vendedor, C.razon_social, C.direccion, C.telefono, C.nr_condicion, CC.saldo, CC.credito_disponible from clientes C left join cuenta_cliente CC on C.nr = CC.nr_cliente where id_cliente = '$cliente'");//We get the desired result from the table
	    foreach ($cliente_result as $resultado_cliente) {        
	        $nr_cliente = $resultado_cliente['nr_cliente'];
	        $id_cliente = $resultado_cliente['id_cliente'];
	        $nr_grupo = $resultado_cliente['nr_grupo'];
	        $nr_vendedor = $resultado_cliente['nr_vendedor'];
	        $descripcion_cliente = $resultado_cliente['razon_social'];
	        $direccion = $resultado_cliente['direccion'];
	        $telefono = $resultado_cliente['telefono'];
	        $nr_condicion= $resultado_cliente['nr_condicion'];
	        $limite_credito= $resultado_cliente['credito_disponible'];
	    }

	    if ($nr_grupo)
    	{
    		@$query = "select GC.nr, GC.id_grupo, GC.nr_lista_precios, LP.id_lista, LP.descripcion descripcion_lista, M.nr nr_moneda, M.descripcion descripcion_moneda
            from grupo_clientes GC join lista_de_precios LP on GC.nr_lista_precios = LP.nr join moneda M on LP.nr_moneda = M.nr  
            where GC.nr = '$nr_grupo'";
	        $grupo_result = $orden_venta->query($query);//We get the desired result from the table
	        $qty_register = $orden_venta -> rowCount($query);
	        if($qty_register > 0){
	            foreach ($grupo_result as $resultado) {        
	                $nr_lista_precios = $resultado['nr_lista_precios'];
	                $id_lista_precios = $resultado['id_lista'];
	                $nr_moneda = $resultado['nr_moneda'];
	                $descripcion_moneda = $resultado['descripcion_moneda'];
	            }
	        }else{   
	        }
	    }

	    if ($nr_vendedor)
    	{
    		@$query = "select * from personal where nr = '$nr_vendedor'";
	        $vendedor_result = $orden_venta->query($query);//We get the desired result from the table
	        $qty_register = $orden_venta -> rowCount($query);
	        if($qty_register > 0){
	            foreach ($vendedor_result as $resultado) {        
	                $nr_vendedor = $resultado['nr'];
	                $descripcion_vendedor = $resultado['nombre_apellido'];
	            }
	        }else{
	        }
	    }

		if ($nr_condicion)
    	{
    		@$query = "select D.nr nr_descuento, D.id_descuento, COV.nr_descuento, COV.nr nr_condicion, COV.id_condicion, COV.cant_dias, D.valor 
    		from descuentos D right join condicion_compra_venta COV on COV.nr_descuento = D.nr where COV.nr = '$nr_condicion'";
	        $condicion_result = $orden_venta->query($query);//We get the desired result from the table
	        $qty_register = $orden_venta -> rowCount($query);
	        if($qty_register > 0){
	            foreach ($condicion_result as $resultado) {        
	                $nr_descuento = $resultado['nr_descuento'];
	                $descuento = $resultado['valor'];
	                $cant_dias = $resultado['cant_dias'];
	            }
	            if ($descuento=="")
	            {
	            	$descuento = 0;
	            }
	        }else{   
	        }
	    }

		echo json_encode($nr_cliente.";".$descripcion_cliente.";".$nr_condicion.";".$nr_grupo.";".$nr_lista_precios.";".$id_lista_precios.";".$nr_moneda.";".$descripcion_moneda.";".$nr_vendedor.";".$descripcion_vendedor.";".$descuento.";".$limite_credito.";".$cant_dias);
	}

	//Get the data from Vendedor;
	@$vendedor = $_POST['vendedor'];
	if (isset($_POST['vendedor'])){
	    @$query = "select * from personal where id_personal = '$vendedor'";
	    $vendedor_result = $orden_venta->query($query);//We get the desired result from the table
	    $qty_register = $orden_venta -> rowCount($query);
	    if($qty_register > 0){
	        foreach ($vendedor_result as $resultado) {        
	            $nr_vendedor = $resultado['nr'];
	            $descripcion_vendedor = $resultado['nombre_apellido'];
	        }
	    }else{
	    }
		echo json_encode($nr_vendedor.",".$descripcion_vendedor);
	}

	//Get the data from Deposito Stock for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_sucursal = $orden_venta->get_deposito_stock($sucursal); 
		foreach ($dato_sucursal as $deposito_result) {
            $deposito_nr = $deposito_result['nr'];    
            echo '<option value="'.$deposito_result['nr'].'"';
            if (@$nr_deposito==$deposito_nr) echo 'selected="selected"';
            echo '>'.$deposito_result['descripcion'].'</option>';
        }
	}

	//Get the data from Producto and Linked lista de precios;
	@$producto = $_POST['producto'];
	@$nr_lista_precios = $_POST['nr_lista_precios'];
	@$nr_moneda = $_POST['nr_moneda'];
	if (isset($_POST['producto'])){
		@$query= "select P.nr nr_producto, P.id_producto, P.descripcion, P.nr_unidad_medida, UM.id_unidad_medida, I.id_impuesto, I.valor from productos P join tipo_impuesto I on P.nr_impuesto = I.nr 
		join unidad_medida UM on P.nr_unidad_medida = UM.nr where P.id_producto ='$producto'";
		//echo $query;
		$producto_result = $orden_venta->query($query);
		$qty_register = $orden_venta -> rowCount($query);
		//echo $producto_result;
		if($qty_register > 0){
			foreach ($producto_result as $resultado_producto) {        
			    $nr_producto = $resultado_producto['nr_producto'];
			    $id_producto = $resultado_producto['id_producto'];
			    $descripcion_producto = $resultado_producto['descripcion'];
			    $nr_unidad = $resultado_producto['nr_unidad_medida'];  
			    $id_unidad = $resultado_producto['id_unidad_medida'];
	            $impuesto = $resultado_producto['valor'];
	        }
	    }else{
	        $impuesto = 0;
	    }

	    if (!empty($nr_producto))
	    {
			$precio_lista = 0;
			$cantidad = 0;
	        if (!empty($nr_lista_precios))
			  {
			  $query = "select DLP.precio from detalle_lista_precios DLP join lista_de_precios LP on DLP.nr_lista_precios = LP.nr join productos P on DLP.nr_producto = P.nr
			  where LP.nr = '$nr_lista_precios' and DLP.nr_producto ='$nr_producto'"; 
			  $precio_result = $orden_venta->query($query);//We get the desired result from the table
			  $qty_register = $orden_venta -> rowCount($query);
			  if($qty_register > 0)
			    {
					foreach ($precio_result as $resultado_precio) 
					{  
					//$precio_producto = number_format($resultado_precio['precio'],2,",",".");
					$precio_lista = $resultado_precio['precio'];
					}  	
				}
			  }
			
			//Check the available stock
			@$nr_sucursal = $_POST['nr_sucursal'];
        	@$nr_deposito = $_POST['nr_deposito'];
	        //Check the Stock
			if (!empty($nr_sucursal))
			{
	        	@$query = "select stock_actual from stock_deposito_sucursal where nr_producto ='$nr_producto' and nr_sucursal ='$nr_sucursal' and nr_deposito ='$nr_deposito'";
	        	$stock_result = $orden_venta->query($query);//We get the desired result from the table
	        	$qty_register = $orden_venta->rowCount($query);
	        	if($qty_register>0) {
	            	//echo $stock_result;
	            	foreach ($stock_result as $resultado_stock) {        
	                	$cantidad = $resultado_stock['stock_actual'];
	            	}
				}
		 	}
		}
		echo json_encode($nr_producto.";".$descripcion_producto.";".$impuesto.";".$nr_unidad.";".$id_unidad.";".$precio_lista.";".$cantidad);
	}

	//Check the qty on Detalle
	//Get the number to show the list of items
    @$orden = $_POST['orden'];
    if (isset($_POST['orden'])){
        /* Lista Detalle section */
        $query = "select * from detalle_orden_venta DOV where DOV.nr = '$orden'";
        //echo $query;
        $detalle_result = $orden_venta->query($query);//We get all the results from the table
        @$cantidad_orden = $orden_venta->rowCount($query);
        echo json_encode($cantidad_orden.','.$orden);
    }

	//Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(total_linea) as total_general, sum(total_iva_linea) as total_iva from detalle_orden_venta DOV where DOV.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $orden_venta->query($query);//We get all the results from the table
        $qty_register = $orden_venta->rowCount($query);
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

	//Check the Stock
    @$action = $_POST['action'];
    /*if ($action=="VerificarStock"){
        @$nr_sucursal = $_POST['nr_sucursal'];
        @$nr_deposito = $_POST['nr_deposito'];
        @$nr_producto =$_POST['nr_producto'];
        //Check the Stock
        @$query = "select stock_actual from stock_deposito_sucursal where nr_producto ='$nr_producto' and nr_sucursal ='$nr_sucursal' and nr_deposito ='$nr_deposito'";
        $stock_result = $orden_venta->query($query);//We get the desired result from the table
        $qty_register = $orden_venta->rowCount($query);
        if($qty_register>0) {
            //echo $stock_result;
            foreach ($stock_result as $resultado_stock) {        
                $cantidad = $resultado_stock['stock_actual'];
            }
        }else{
            $cantidad = 0;
        }
        echo json_encode($cantidad.",".$nr_sucursal.",".$nr_deposito);
    }*/

	//Search section
	if(!empty($_POST["buscar_cliente"])) {
		//The variable buscar_cliente is already in uppercase
		@$buscar_cliente = $_POST["buscar_cliente"];
		//We also uppercase the database columns to match with the variable->buscar_cliente
		@$query = "select * from clientes C  where upper(razon_social) like '%".$buscar_cliente."%' or upper(id_cliente) like '%".$buscar_cliente."%' order by razon_social";
		//echo $query;
		$cliente_result = $orden_venta->query($query);
		$qty_register = $orden_venta->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="cliente-list" class="lista">';
			foreach($cliente_result as $result_cliente) {
				$resultado = $result_cliente["id_cliente"].'||'.$result_cliente["razon_social"];
				?>
				<li onClick="selectCliente('<?php echo $result_cliente["id_cliente"]; ?>');">
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
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' or upper(id_producto) like '%".$buscar_producto."%' order by descripcion";
		//echo $query;
		$producto_result = $orden_venta->query($query);
		$qty_register = $orden_venta->rowCount($query);
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

	if(!empty($_POST["buscar_vendedor"])) {
		//We uppercase the searchbox
		@$buscar_vendedor = $_POST["buscar_vendedor"];
		//We also uppercase the database columns to match with the variable->buscar_vendedor
		@$query = "select * from personal V where upper(nombre_apellido) like '%".$buscar_vendedor."%' or upper(id_personal) like '%".$buscar_vendedor."%' order by nombre_apellido";
		//echo $query;
		$vendedor_result = $orden_venta->query($query);
		$qty_register = $orden_venta->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="vendedor-list" class="lista">';
			foreach($vendedor_result as $result_vendedor) {
				$resultado = $result_vendedor["id_personal"].'||'.$result_vendedor["nombre_apellido"];
				?>
				<li onClick="selectVendedor('<?php echo $result_vendedor["id_personal"]; ?>');">
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
        $query = "select DOV.nr, DOV.nr_producto, P.id_producto, P.descripcion descripcion_producto, DOV.cantidad, DOV.descuento, DOV.precio_lista, DOV.precio_final, DOV.total_linea, DOV.impuesto, DOV.nr_unidad, UM.id_unidad_medida id_unidad, COV.nr_moneda
        from detalle_orden_venta DOV join productos P on DOV.nr_producto = P.nr join unidad_medida UM on DOV.nr_unidad = UM.nr join cabecera_orden_venta COV on COV.nr = DOV.nr 
        where DOV.nr = '$listar_orden'";
        //echo $query;
        $detalle_result = $orden_venta->query($query);//We get all the results from the table
        @$qty_register = $orden_venta -> rowCount($query);
        //Check if more than 0 records were found
        if($qty_register > 0){
            echo '<table id="detalle-list" >';
                echo '<p><b><label class="control-label">Detalle Productos Agregados</label></b></p>';
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
                    echo '<td style="width:200px">'. $i . '</td>';
                    echo '<td width="2%">'. $total_row['id_producto'] . '</td>';
                    echo '<td width="2%">'. $total_row['descripcion_producto'] . '</td>';
                    echo '<td width="2%">'. number_format($total_row['cantidad'],$decimal,",",".") . '</td>';
                    echo '<td width="2%">'. $total_row['id_unidad'] . '</td>';
                    echo '<td width="2%">'. number_format($total_row['precio_lista'],$decimal,",",".") . '</td>';
                    echo '<td width="2%">'. number_format($total_row['impuesto'],$decimal,",",".")  . '</td>';
                    echo '<td width="2%">'. number_format($total_row['descuento'],$decimal,",",".")  . '</td>';
                    echo '<td width="2%">'. number_format($total_row['precio_final'],$decimal,",",".") . '</td>';
                    echo '<td width="2%">'. number_format($total_row['total_linea'],$decimal,",",".") . '</td>';
                    echo '<td width="2%" class = "buttons-column"><a onclick="EditarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].')" href="#modal"  title="Editar" class = "edit"></a></td>';
                    echo '<td width="2%"><a onclick="EliminarDetalle('.$total_row['nr'].','.$total_row['nr_producto'].')"  title="Borrar" class = "delete"></a></td>';
                echo '</tr>';
                endforeach;
            }else{
            }
            echo '</tbody>';
        echo '</table>';
    }

	//Verify if the Orden de venta has an Invoice;
	@$orden_estado = $_POST['orden_estado'];
	if (isset($_POST['orden_estado'])){
	    $query = "select (sum(cantidad)-sum(facturado))as cantidad_pend from detalle_orden_venta where nr = '$orden_estado'"; 
		$factura_result = $orden_venta->query($query);//We get the desired result from the table
		foreach($factura_result as $resultado):
			$cant_pendiente = $resultado['cantidad_pend'];
        endforeach;
		if($cant_pendiente == 0){
			$factura = 1;
		}else{
			$factura = 0;
		}
		echo json_encode($orden_estado.",".$factura);
	}
	
	
	//Getting value of "searchstring" variable from "busqueda.js".
	//Search query.
	$query = "select OV.nr, OV.fecha_orden, C.razon_social descripcion_cliente, M.descripcion moneda_descripcion, OV.total_orden 
    from cabecera_orden_venta OV join clientes C on OV.nr_cliente = C.nr join moneda M on OV.nr_moneda = M.nr";
	//echo $query;	
	if (isset($_POST['searchstring'])) {
		//Search box value assigning to $cliente variable.
		$cliente = $_POST['searchstring'];
		//Search query.
		if(!empty($cliente))
          {
		  $query = $query . " where upper(C.razon_social) like '%" . $cliente . "%'"; 
		  }
		$query = $query . " order by fecha_orden desc limit 150"; 
		//echo $query;
        //Query execution
        $total_result = $orden_venta->query($query);//We get all the results from the table		
        $qty_register = $orden_venta->rowCount($query);
		//Creating unordered list to display result.
		echo '<ul>';
		$result = [];
		foreach($total_result as $total_row):
		   $result = $total_row;
		endforeach;
        echo json_encode($result);
		//Fetching result from database.
       /*echo '<table class="list">';
	   echo '<thead>';
            echo '<tr>';
              	echo '<th>Facturar</th>';               
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
                echo '<th>Imprimir</th>';
            echo '</tr>';
        echo '</thead>';
    	echo '<tbody>';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $orden = $total_row['nr'];
                echo '<td>'. $orden . '</td>';
                $fecha_orden = date_format(date_create($total_row['fecha_orden']),"d/m/Y");
                echo '<td>'. $fecha_orden . '</td>';
                echo '<td>'. $total_row['descripcion_cliente'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_orden = number_format($total_row['total_orden'],2,",",".");
                echo '<td class="monto">'. $total_orden . '</td>';
                //echo '<td class = "buttons-column"><a target="_blank" href="factura_venta_form.php?orden='.$orden.'" title="Facturar" class = "Facturar" onclick="VerificarFactura('.$orden.')">Facturar</a></td>';
                echo '<td class = "buttons-column"><a href="#" title="Facturar" class = "Facturar" onclick="VerificarFactura('.$orden.',0)">Facturar</a></td>';
                echo '<td class = "buttons-column"><a href="#" title="Editar" class = "Edit" onclick="VerificarFactura('.$orden.',1)"></a></td>';
                //echo '<td class = "buttons-column"><a onclick="return confirmarBorrado('.$orden.')" href="'.$page_name.'?delete='.$orden.'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_orden_venta.php?orden_venta='.$orden.'">Imprimir</a></td>';
                echo '</tr>';
                endforeach;
            }else{
                echo '<div class = "no_record">No se encontraron registros</div>';
            }
        echo '</tbody>';
	echo '</table>';*/
	echo '</ul>';
	}	
?>