<?php
	require '../clases/formularios/orden_pago.class.php';
	$orden_pago = orden_pago::singleton();

	//Get the data from Proveedor;
	@$proveedor = $_POST['proveedor'];
	if (isset($_POST['proveedor'])){
		@$query = "select * from proveedor where id_proveedor ='$proveedor'";
	    $proveedor_result = $orden_pago->query($query);//We get the desired result from the table
	    $qty_register = $orden_pago->rowCount($query);
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
		}else{
			echo 'alert("No existe el Proveedor")';
		}
	}

	//Get the data from Caja for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_caja = $orden_pago->get_caja($sucursal); 
		foreach ($dato_caja as $caja_result) {
            $caja_nr = $caja_result['nr'];    
            echo '<option value="'.$caja_result['nr'].'"';
            if (@$nr_caja==$caja_nr) echo 'selected="selected"';
            echo '>'.$caja_result['descripcion'].'</option>';
        }
	}

	//Get the data from Medio de Pago;
	@$medio_pago = $_POST['medio_pago'];
	if (isset($_POST['medio_pago'])){
		@$query = "select * from medio_pago where id_medio_pago ='$medio_pago'";
	    $medio_pago_result = $orden_pago->query($query);//We get the desired result from the table
	    $qty_register = $orden_pago->rowCount($query);
		if($qty_register>0) {
		    foreach ($medio_pago_result as $resultado_medio_pago) {        
		        $nr_medio_pago = $resultado_medio_pago['nr'];
		        $id_medio_pago = $resultado_medio_pago['id_medio_pago'];
		        $descripcion_medio_pago = $resultado_medio_pago['descripcion'];
		    }
			echo json_encode($nr_medio_pago.",".$descripcion_medio_pago);
		}else{
			echo 'alert("No existe el Medio de Pago")';
		}
	}

    //Check the qty on Detalle
    //Get the number to show the list of items
    @$orden = $_POST['orden'];
    if (isset($_POST['orden'])){
        /* Lista Detalle section */
        $query = "select * from detalle_orden_pago DOP where DOP.nr = '$orden'";
        //echo $query;
        $detalle_result = $orden_pago->query($query);//We get all the results from the table
        @$cantidad_pago = $orden_pago -> rowCount($query);
         /* Lista Detalle section */
        $query = "select * from detalle_aplicacion_pago DAP where DAP.nr = '$orden'";
        $detalle_result = $orden_pago->query($query);//We get all the results from the table
        @$cantidad_aplicacion = $orden_pago -> rowCount($query);
        echo json_encode($cantidad_pago.','.$cantidad_aplicacion.','.$orden);
    }

    //Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(DOP.total_linea) as total_pago from detalle_orden_pago DOP where DOP.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $orden_pago->query($query);//We get all the results from the table
        $qty_register = $orden_pago->rowCount($query);
        foreach($detalle_result as $total_row):
            $total_pago = $total_row['total_pago'];
        endforeach;
        if (empty($total_pago))
        {
            $total_pago = 0;
        }
        $query = "select sum(DAP.total_linea) as total_aplicado from detalle_aplicacion_pago DAP where DAP.nr = '$orden_nr'";
        $detalle_result = $orden_pago->query($query);//We get all the results from the table
        $qty_register = $orden_pago->rowCount($query);
        foreach($detalle_result as $total_row):
            $total_aplicado = $total_row['total_aplicado'];
        endforeach;
        if (empty($total_aplicado))
        {
            $total_aplicado = 0;
        }
        echo json_encode($total_aplicado.','.$total_pago.','.$orden_nr);
    }

	//Get the data from Factura Compra;
	@$factura_compra = $_POST['factura_compra'];
    //@$nr_proveedor = $_POST['proveedor'];
	if (isset($_POST['factura_compra'])){
		//@$query = "select * from cabecera_factura_compra where nr_factura ='$factura_compra' and nr_proveedor = $nr_proveedor and saldo_factura > 0";
        @$query = "select * from cabecera_factura_compra where nr_factura ='$factura_compra' and saldo_factura > 0";
	    $factura_compra_result = $orden_pago->query($query);//We get the desired result from the table
	    $qty_register = $orden_pago->rowCount($query);
		if($qty_register>0) {
		    foreach ($factura_compra_result as $resultado_factura_compra) {        
		        $nr_factura_compra = $resultado_factura_compra['nr'];
		        $nr_factura_oficial = $resultado_factura_compra['nr_factura'];
		        $saldo_factura = $resultado_factura_compra['saldo_factura'];
		    }
			echo json_encode($nr_factura_compra.",".$nr_factura_oficial.",".$saldo_factura);
		}else{
			//echo 'alert("No existe el Nro. de Factura para el Proveedor")';
		}
	}

	//Search section
	if(!empty($_POST["buscar_proveedor"])) {
		//The variable buscar_proveedor is already in uppercase
		@$buscar_proveedor = $_POST["buscar_proveedor"];
		//We also uppercase the database columns to match with the variable->buscar_proveedor
		@$query = "select * from proveedor P where upper(descripcion) like '%".$buscar_proveedor."%' order by descripcion";
		//echo $query;
		$proveedor_result = $orden_pago->query($query);
		$qty_register = $orden_pago->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="proveedor-list">';
			foreach($proveedor_result as $result_proveedor) {
				?>
				<li onClick="selectProveedor('<?php echo $result_proveedor["id_proveedor"]; ?>');">
				<?php echo $result_proveedor["descripcion"]; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}

	if(!empty($_POST["buscar_medio_pago"])) {
		//The variable buscar_medio_pago is already in uppercase
		@$buscar_medio_pago = $_POST["buscar_medio_pago"];
		//We also uppercase the database columns to match with the variable->buscar_medio_pago
		@$query = "select * from medio_pago MP where upper(descripcion) like '%".$buscar_medio_pago."%' order by descripcion";
		//echo $query;
		$medio_pago_result = $orden_pago->query($query);
		$qty_register = $orden_pago->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="medio-pago-list">';
			foreach($medio_pago_result as $result_medio_pago) {
				?>
				<li onClick="selectMedioPago('<?php echo $result_medio_pago["id_medio_pago"]; ?>');">
				<?php echo $result_medio_pago["descripcion"]; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}

	if(!empty($_POST["buscar_factura"])) {
		//The variable buscar_factura_compra is already in uppercase
		@$buscar_factura_compra = $_POST["buscar_factura"];
		//We also uppercase the database columns to match with the variable->buscar_factura_compra
		@$query = "select * from cabecera_factura_compra MP where upper(nr_factura) like '%".$buscar_factura_compra."%' order by nr";
		//echo $query;
		$factura_compra_result = $orden_pago->query($query);
		$qty_register = $orden_pago->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="medio-pago-list">';
			foreach($factura_compra_result as $result_factura_compra) {
				?>
				<li onClick="selectFacturaCompra('<?php echo $result_factura_compra["nr_factura"]; ?>');">
				<?php echo $result_factura_compra["nr_factura"]; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}


	//Get the number to show the list of items
    @$listar_pago = $_POST['listar_pago'];
    if (isset($_POST['listar_pago'])){
        /* Lista Detalle section */
        $query = "select DOP.nr, DOP.nr_medio_pago, MP.id_medio_pago, MP.descripcion descripcion_medio_pago, DOP.monto_pago, DOP.total_linea, DOP.obs, COP.nr_moneda
        from detalle_orden_pago DOP join medio_pago MP on DOP.nr_medio_pago = MP.nr join cabecera_orden_pago COP on COP.nr = DOP.nr
        where DOP.nr = '$listar_pago'";
        //echo $query;
        $detalle_result = $orden_pago->query($query);//We get all the results from the table
        @$qty_register = $orden_pago -> rowCount($query);

        if($qty_register > 0){
        echo '<table id="detalle-list">';
            echo '<p><b>Lista de Pagos Agregados</b></p>';
            echo '<thead>';
            echo '<tr>';
                echo '<th>#</th>';
                echo '<th>Medio Pago</th>';
                echo '<th>Monto Pago</th>';
                echo '<th>Obs.</th>';
                echo '<th>Total</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            //Check if more than 0 records were found
            //if($qty_register > 0){
                foreach($detalle_result as $total_row):
                @$i= $i + 1;
                @$action = "EliminarDetalle";
                echo '<tr>';
                	if ($total_row['nr_moneda']==1)
                    {
                        $decimal = 0;
                    }else{
                        $decimal = 2;
                    }
                    echo '<td>'. $i . '</td>';
                    echo '<td>'. $total_row['descripcion_medio_pago'] . '</td>';
                    echo '<td>'. number_format($total_row['monto_pago'],$decimal,",",".") . '</td>';
                    echo '<td>'. $total_row['obs'] . '</td>';
                    echo '<td>'. number_format($total_row['total_linea'],$decimal,",",".") . '</td>';
                    //echo '<td><input type="button" value="Eliminar" id="eliminardetalle" name="editarDetalle" class="edit" "eliminarDetalle('.$action.','.$total_row['nr'].','.$total_row['nr_producto'].')"></td>';
                    echo '<td></a><a href="#" onclick="eliminarDetalle('.$total_row['nr'].','.$total_row['nr_medio_pago'].'"  title="Borrar" class = "delete"></a></td>';
                echo '</tr>';
                @$total_orden+=$total_row['total_linea'];
                //echo $total_orden;
                //echo json_encode($total_orden);
                endforeach;
        }else{
            $texto = "Ningun Pago agregado.";
            //echo "<b>".$texto."</b>";
        }
        echo '</tbody>';
        echo '</table>';
    }

    //Get the number to show the list of items in Aplicacion Pago
    @$listar_aplicacion_pago = $_POST['listar_aplicacion_pago'];
    if (isset($_POST['listar_aplicacion_pago'])){
        /* Lista Detalle section */
        $query = "select DAP.nr, DAP.nr_factura_compra, DAP.nr_nota_credito_compra, DAP.monto_aplicado, DAP.total_linea, COP.nr_moneda
        from detalle_aplicacion_pago DAP join cabecera_factura_compra COP on DAP.nr_factura_compra = COP.nr
        left join cabecera_nota_credito_compra CNC on DAP.nr_nota_credito_compra = CNC.nr
        where DAP.nr = '$listar_aplicacion_pago'";
        //echo $query;
        $detalle_aplicacion_result = $orden_pago->query($query);//We get all the results from the table
        @$qty_register = $orden_pago -> rowCount($query);

        if($qty_register > 0){
        echo '<table id="detalle-list">';
            echo '<p><b>Lista de Pagos Aplicados</b></p>';
            echo '<thead>';
            echo '<tr>';
                echo '<th>#</th>';
                echo '<th>Nro. Factura Compra</th>';
                echo '<th>Nro. Nota de Credito</th>';
                echo '<th>Monto Aplicado</th>';
                echo '<th>Total</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            //Check if more than 0 records were found
            //if($qty_register > 0){
                foreach($detalle_aplicacion_result as $total_aplicacion_row):
                @$i= $i + 1;
                @$action = "EliminarDetalle";
                echo '<tr>';
                	if ($total_aplicacion_row['nr_moneda']==1)
                    {
                        $decimal = 0;
                    }else{
                        $decimal = 2;
                    }
                    echo '<td>'. $i . '</td>';
                    echo '<td>'. $total_aplicacion_row['nr_factura_compra']. '</td>';
                    echo '<td>'. $total_aplicacion_row['nr_nota_credito_compra'] . '</td>';
                    echo '<td>'. number_format($total_aplicacion_row['monto_aplicado'],$decimal,",",".") . '</td>';
                    echo '<td>'. number_format($total_aplicacion_row['total_linea'],$decimal,",",".") . '</td>';
                    //echo '<td><input type="button" value="Eliminar" id="eliminardetalle" name="editarDetalle" class="edit" "eliminarDetalle('.$action.','.$total_aplicacion_row['nr'].','.$total_aplicacion_row['nr_producto'].')"></td>';
                    echo '<td></a><a href="#" onclick="eliminarDetalle('.$total_aplicacion_row['nr'].','.$total_aplicacion_row['nr_factura_compra'].'"  title="Borrar" class = "delete"></a></td>';
                echo '</tr>';
                @$total_orden+=$total_row['total_linea'];
                //echo $total_orden;
                //echo json_encode($total_orden);
                endforeach;
        }else{
            $texto = "Ningun Pago aplicado.";
            //echo "<b>".$texto."</b>";
        }
        echo '</tbody>';
        echo '</table>';
    }

    //Onclick function
	/*echo '<script language="Javascript">
    function eliminarDetalle(nr_eliminar, nr_producto_eliminar)
    {
        var nr_eliminar = nr_eliminar;
        var nr_producto_eliminar = nr_producto_eliminar;
        eliminar=confirm("¿Seguro que desea eliminar del Pedido?");
        if (eliminar)
        {
            return true;
        	var action = "EliminarDetalle";
                $.ajax({
                    type: "POST",
                    url: "../ajax/guardar_orden_pago_ajax.php",
                    data: {"action":action,"nr_eliminar":nr_eliminar,"nr_producto_eliminar":nr_producto_eliminar},
                    success: function(data){
                        //alert("Result data: " + data);
                    }
            });
        }else{
            return false;
            window.location.href = "orden_pago_form.php";
        }
    }
	</script>';*/
?>

