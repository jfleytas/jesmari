<?php
	require '../clases/formularios/cobranza.class.php';
	$cobranza = cobranza::singleton();

	//Get the data from cliente;
	@$cliente = $_POST['cliente'];
	if (isset($_POST['cliente'])){
		@$query = "select * from clientes where id_cliente ='$cliente'";
	    $cliente_result = $cobranza->query($query);//We get the desired result from the table
	    $qty_register = $cobranza->rowCount($query);
		if($qty_register>0) {
		    foreach ($cliente_result as $resultado_cliente) {        
		        $nr_cliente = $resultado_cliente['nr'];
		        $id_cliente = $resultado_cliente['id_cliente'];
		        $descripcion_cliente = $resultado_cliente['razon_social'];
		        $direccion = $resultado_cliente['direccion'];
		        $telefono = $resultado_cliente['telefono'];
		        $nr_condicion= $resultado_cliente['nr_condicion'];
		    }
			echo json_encode($nr_cliente.",".$descripcion_cliente.",".$nr_condicion);
		}
	}

	//Get the data from Caja for the specific Sucursal;
	@$sucursal = $_POST['sucursal'];
	if (isset($_POST['sucursal'])){
		$dato_caja = $cobranza->get_caja($sucursal); 
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
	    $medio_pago_result = $cobranza->query($query);//We get the desired result from the table
	    $qty_register = $cobranza->rowCount($query);
		if($qty_register>0) {
		    foreach ($medio_pago_result as $resultado_medio_pago) {        
		        $nr_medio_pago = $resultado_medio_pago['nr'];
		        $id_medio_pago = $resultado_medio_pago['id_medio_pago'];
		        $descripcion_medio_pago = $resultado_medio_pago['descripcion'];
		    }
			echo json_encode($nr_medio_pago.",".$descripcion_medio_pago);
		}
	}

    //Check the qty on Detalle
    //Get the number to show the list of items
    @$orden = $_POST['orden'];
    if (isset($_POST['orden'])){
        /* Lista Detalle section */
        $query = "select * from detalle_cobranza DC where DC.nr = '$orden'";
        //echo $query;
        $detalle_result = $cobranza->query($query);//We get all the results from the table
        @$cantidad_pago = $cobranza->rowCount($query);
         /* Lista Detalle section */
        $query = "select * from detalle_aplicacion_cobranza DAC where DAC.nr = '$orden'";
        $detalle_result = $cobranza->query($query);//We get all the results from the table
        @$cantidad_aplicacion = $cobranza -> rowCount($query);
        echo json_encode($cantidad_pago.','.$cantidad_aplicacion.','.$orden);
    }

    //Get the Total_General
    @$orden_nr = $_POST['orden_nr'];
    if (isset($_POST['orden_nr'])){
        // Lista Detalle section 
        $query = "select sum(DC.total_linea) as total_pago from detalle_cobranza DC where DC.nr = '$orden_nr'";
        //echo $query;
        $detalle_result = $cobranza->query($query);//We get all the results from the table
        $qty_register = $cobranza->rowCount($query);
        foreach($detalle_result as $total_row):
            $total_pago = $total_row['total_pago'];
        endforeach;
        if (empty($total_pago))
        {
            $total_pago = 0;
        }
        $query = "select sum(DAC.total_linea) as total_aplicado from detalle_aplicacion_cobranza DAC where DAC.nr = '$orden_nr'";
        $detalle_result = $cobranza->query($query);//We get all the results from the table
        $qty_register = $cobranza->rowCount($query);
        foreach($detalle_result as $total_row):
            $total_aplicado = $total_row['total_aplicado'];
        endforeach;
        if (empty($total_aplicado))
        {
            $total_aplicado = 0;
        }
        echo json_encode($total_aplicado.','.$total_pago.','.$orden_nr);
    }

	//Get the data from Factura Venta;
	@$factura_venta = $_POST['factura_venta'];
    //@$nr_cliente = $_POST['cliente'];
	if (isset($_POST['factura_venta'])){
		//@$query = "select * from cabecera_factura_venta where nr_factura ='$factura_venta' and nr_cliente = $nr_cliente and saldo_factura > 0";
        @$query = "select * from cabecera_factura_venta where nr_factura ='$factura_venta' and saldo_factura > 0";
	    $factura_venta_result = $cobranza->query($query);//We get the desired result from the table
	    $qty_register = $cobranza->rowCount($query);
		if($qty_register>0) {
		    foreach ($factura_venta_result as $resultado_factura_venta) {        
		        $nr_factura_venta = $resultado_factura_venta['nr'];
		        $nr_factura_oficial = $resultado_factura_venta['nr_factura'];
		        $saldo_factura = $resultado_factura_venta['saldo_factura'];
		    }
			echo json_encode($nr_factura_venta.",".$nr_factura_oficial.",".$saldo_factura);
		}else{
			//echo 'alert("No existe el Nro. de Factura para el cliente")';
		}
	}

	//Search section
	if(!empty($_POST["buscar_cliente"])) {
		//The variable buscar_cliente is already in uppercase
		@$buscar_cliente = $_POST["buscar_cliente"];
		//We also uppercase the database columns to match with the variable->buscar_cliente
		@$query = "select * from clientes P where upper(razon_social) like '%".$buscar_cliente."%' order by razon_social";
		//echo $query;
		$cliente_result = $cobranza->query($query);
		$qty_register = $cobranza->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="cliente-list">';
			foreach($cliente_result as $result_cliente) {
				?>
				<li onClick="selectCliente('<?php echo $result_cliente["id_cliente"]; ?>');">
				<?php echo $result_cliente["razon_social"]; ?>
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
		$medio_pago_result = $cobranza->query($query);
		$qty_register = $cobranza->rowCount($query);
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
		@$buscar_factura_venta = $_POST["buscar_factura"];
        @$buscar_cliente = $_POST["cliente"];
		//We also uppercase the database columns to match with the variable->buscar_factura_venta
		@$query = "select * from cabecera_factura_venta MP where upper(nr_factura) like '%".$buscar_factura_venta."%' and nr_cliente = '".$buscar_cliente."' order by nr";
		//echo $query;
		$factura_venta_result = $cobranza->query($query);
		$qty_register = $cobranza->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="medio-pago-list">';
			foreach($factura_venta_result as $result_factura_venta) {
				?>
				<li onClick="selectFacturaVenta('<?php echo $result_factura_venta["nr_factura"]; ?>');">
				<?php echo $result_factura_venta["nr_factura"]; ?>
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
        $query = "select DC.nr, DC.nr_medio_pago, MP.id_medio_pago, MP.descripcion descripcion_medio_pago, DC.monto_pago, DC.total_linea, DC.obs, CC.nr_moneda
        from detalle_cobranza DC join medio_pago MP on DC.nr_medio_pago = MP.nr join cabecera_cobranza CC on CC.nr = DC.nr
        where DC.nr = '$listar_pago'";
        //echo $query;
        $detalle_result = $cobranza->query($query);//We get all the results from the table
        @$qty_register = $cobranza -> rowCount($query);

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
                @$action = "EliminarDetallePago";
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
                    echo '<td width="2%"><a onclick="EliminarDetallePago('.$total_row['nr'].','.$total_row['nr_medio_pago'].')"  title="Borrar" class = "delete"></a></td>';
                echo '</tr>';
                @$total_orden+=$total_row['total_linea'];
                //echo $total_orden;
                //echo json_encode($total_orden);
                endforeach;
        }else{
            $texto = "Ninguna Cobranza agregada.";
            //echo "<b>".$texto."</b>";
        }
        echo '</tbody>';
        echo '</table>';
    }

    //Get the number to show the list of items in Aplicacion Pago
    @$listar_aplicacion_pago = $_POST['listar_aplicacion_pago'];
    if (isset($_POST['listar_aplicacion_pago'])){
        /* Lista Detalle section */
        $query = "select DAC.nr, DAC.nr_factura_venta, DAC.nr_nota_credito_venta, DAC.monto_aplicado, DAC.total_linea, CC.nr_moneda
        from detalle_aplicacion_cobranza DAC join cabecera_factura_venta CC on DAC.nr_factura_venta = CC.nr
        left join cabecera_nota_credito_venta CNC on DAC.nr_nota_credito_venta = CNC.nr
        where DAC.nr = '$listar_aplicacion_pago'";
        //echo $query;
        $detalle_aplicacion_result = $cobranza->query($query);//We get all the results from the table
        @$qty_register = $cobranza -> rowCount($query);

        if($qty_register > 0){
        echo '<table id="detalle-list">';
            echo '<p><b>Lista de Pagos Aplicados</b></p>';
            echo '<thead>';
            echo '<tr>';
                echo '<th>#</th>';
                echo '<th>Nro. Factura Venta</th>';
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
                @$action = "EliminarAplicacionPago";
                echo '<tr>';
                	if ($total_aplicacion_row['nr_moneda']==1)
                    {
                        $decimal = 0;
                    }else{
                        $decimal = 2;
                    }
                    echo '<td>'. $i . '</td>';
                    echo '<td>'. $total_aplicacion_row['nr_factura_venta']. '</td>';
                    echo '<td>'. $total_aplicacion_row['nr_nota_credito_venta'] . '</td>';
                    echo '<td>'. number_format($total_aplicacion_row['monto_aplicado'],$decimal,",",".") . '</td>';
                    echo '<td>'. number_format($total_aplicacion_row['total_linea'],$decimal,",",".") . '</td>';
                    //echo '<td><input type="button" value="Eliminar" id="eliminardetalle" name="editarDetalle" class="edit" "eliminarDetalle('.$action.','.$total_aplicacion_row['nr'].','.$total_aplicacion_row['nr_producto'].')"></td>';
                    echo '<td width="2%"><a onclick="EliminarAplicacionPago('.$total_aplicacion_row['nr'].','.$total_aplicacion_row['nr_factura_venta'].')"  title="Borrar" class = "delete"></a></td>';
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
                    url: "../ajax/guardar_cobranza_ajax.php",
                    data: {"action":action,"nr_eliminar":nr_eliminar,"nr_producto_eliminar":nr_producto_eliminar},
                    success: function(data){
                        //alert("Result data: " + data);
                    }
            });
        }else{
            return false;
            window.location.href = "cobranza_form.php";
        }
    }
	</script>';*/
?>

