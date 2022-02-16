<?php
	require '../clases/formularios/orden_pago.class.php';
	$orden_pago = orden_pago::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
        //echo $nr;
	    @$fecha_pago = $_POST['fecha_pago'];
        //echo $fecha_pago;
        @$nr_proveedor = $_POST['nr_proveedor'];
        //echo $nr_proveedor;
	    @$nr_sucursal = $_POST['nr_sucursal'];
        //echo $nr_sucursal;
	    @$nr_caja = $_POST['nr_caja'];
        //echo $nr_caja;
	    @$nr_moneda = $_POST['nr_moneda'];
        //echo $nr_moneda;
	    @$cotizacion_compra = $_POST['cotizacion_compra'];
        //echo $cotizacion_compra;
        @$cotizacion_venta = $_POST['cotizacion_venta'];
        //echo $cotizacion_venta;
	    @$nr_user = $_POST['nr_user'];
        //echo $nr_user;
	    @$total_pago=$_POST['total_pago'];
        //echo $total_pago;
        @$obs=$_POST['obs'];
        //echo $obs;
        @$estado=$_POST['estado'];
        //echo $estado;

        /*$cabeceraOP = array(
            'lines'=>array(
            array('nr'=>$nr,'fecha_pago' =>$fecha_pago,'nr_proveedor'=>$nr_proveedor,'nr_sucursal' =>$nr_sucursal,'nr_caja' =>$nr_caja,'nr_moneda' =>$nr_moneda,
                'cotizacion_compra' =>$cotizacion_compra,'cotizacion_venta' =>$cotizacion_venta,'total_pago' =>$total_pago,'nr_user'=>$nr_user,'obs' =>$obs,'estado' =>$estado)
            )
        );

        foreach($cabeceraOP['lines'] as $cabeceraOPLine)
        { 
            $orden_pago->insert_cabecera_orden_pago($cabeceraOPLine['nr'], $cabeceraOPLine['fecha_pago'], $cabeceraOPLine['nr_proveedor'],  
                $cabeceraOPLine['nr_sucursal'], $cabeceraOPLine['nr_caja'], $cabeceraOPLine['nr_moneda'], $cabeceraOPLine['cotizacion_compra'], 
                $cabeceraOPLine['cotizacion_venta'],$cabeceraOPLine['total_pago'], $cabeceraOPLine['nr_user'],$cabeceraOPLine['obs'], 
                $cabeceraOPLine['estado']);
        }*/

        //Check if the Cabecera Orden de Pago already exists if yes
        @$qty_register = $orden_pago -> rowCount("select * from cabecera_orden_pago where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Pedido Sucursal already exists, update it.
            $orden_pago->update_cabecera_orden_pago($nr,$fecha_pago,$nr_proveedor,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$obs,$estado);
        }else{
            //Cabecera Orden Pago don't exists, insert it.
            $orden_pago->insert_cabecera_orden_pago($nr,$fecha_pago,$nr_proveedor,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$obs,$estado);
        }
        
        echo json_encode($nr.",".$fecha_pago.",".$nr_proveedor.",".$nr_sucursal.",".$nr_caja.",".$nr_moneda.",".$cotizacion_compra.",".$cotizacion_venta.",".$total_pago.",".$nr_user.",".$obs.",".$estado);
	}

	if ($action=="GenerarDetallePago"){
        @$nr = $_POST['nr'];
	    @$nr_medio_pago = $_POST['nr_medio_pago'];
        @$monto_pago = $_POST['monto_pago'];
        @$total_linea = $_POST['total_linea'];
        @$obs_detalle = $_POST['obs_detalle'];
	    
	    //Check if the Orden de Compra was stored
	    //@$qty_register = $orden_pago -> rowCount("select * from cabecera_orden_pago where nr = '$nr'");
	    //if ($qty_register>0){
            $detalleOP = array(
                'lines'=>array(
                array('nr'=>$nr,'nr_medio_pago'=>$nr_medio_pago,'monto_pago'=>$monto_pago,'total_linea'=>$total_linea,'obs_detalle'=>$obs_detalle)
            )
            );

            $cart['lines'][] = array('nr'=>$nr,'nr_medio_pago'=>$nr_medio_pago,'monto_pago'=>$monto_pago,'total_linea'=>$total_linea,'obs_detalle'=>$obs_detalle);

            foreach($detalleOP['lines'] as $detalleOPLine)
            { 
                $orden_pago->insert_detalle_orden_pago($detalleOPLine['nr'],$detalleOPLine['nr_medio_pago'], $detalleOPLine['monto_pago'], $detalleOPLine['total_linea'], $detalleOPLine['obs_detalle']);
            }

			//$orden_pago->insert_detalle_orden_pago($nr,$nr_factura_compra,$nf_nota_credito_compra,$medio_pago,$monto_aplicado,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            //echo json_encode($nr.",".$nr_factura_compra.",".$id_producto.",".$descripcion_producto.",".$nf_nota_credito_compra.",".$nr_unidad.",".$id_unidad.",".$monto_aplicado.",".$precio_final.",".$impuesto.",".$medio_pago.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_medio_pago = "";
            $descripcion_medio_pago = "";
            @$monto_pago = 0;
            $total_linea = 0;
            $obs = "";
			echo json_encode($nr.",".$nr_medio_pago.",".$descripcion_medio_pago.",".$monto_pago.",".$total_linea.",".$obs);
	    //}
	}

    if ($action=="GenerarDetalleAplicacionPago"){
        @$nr = $_POST['nr'];
        @$nr_factura_compra = $_POST['nr_factura_compra'];
        @$nr_nota_credito_compra = $_POST['nr_nota_credito_compra'];
        @$monto_aplicado = $_POST['monto_aplicado'];
        @$total_linea = $_POST['total_linea'];
        
        //Check if the Orden de Compra was stored
        //@$qty_register = $orden_pago -> rowCount("select * from cabecera_orden_pago where nr = '$nr'");
        //if ($qty_register>0){
            $detalleAplicacionOP = array(
                'lines'=>array(
                array('nr'=>$nr,'nr_factura_compra'=>$nr_factura_compra,'nr_nota_credito_compra'=>$nr_nota_credito_compra,
                    'monto_aplicado'=>$monto_aplicado,'total_linea'=>$total_linea)
            )
            );

            $cart['lines'][] = array('nr'=>$nr,'nr_factura_compra'=>$nr_factura_compra,'nr_nota_credito_compra'=>$nr_nota_credito_compra,
                'monto_aplicado'=>$monto_aplicado,'total_linea'=>$total_linea);

            foreach($detalleAplicacionOP['lines'] as $detalleAplicacionOPLine)
            { 
                $orden_pago->insert_detalle_aplicacion_pago($detalleAplicacionOPLine['nr'], $detalleAplicacionOPLine['nr_factura_compra'], $detalleAplicacionOPLine['nr_nota_credito_compra'], 
                    $detalleAplicacionOPLine['monto_aplicado'], $detalleAplicacionOPLine['total_linea']);
            }

            //$orden_pago->insert_detalle_orden_pago($nr,$nr_factura_compra,$nf_nota_credito_compra,$medio_pago,$monto_aplicado,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            //echo json_encode($nr.",".$nr_factura_compra.",".$id_producto.",".$descripcion_producto.",".$nf_nota_credito_compra.",".$nr_unidad.",".$id_unidad.",".$monto_aplicado.",".$precio_final.",".$impuesto.",".$medio_pago.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_factura_compra = "";
            $nr_nota_credito_compra = "";
            @$monto_aplicado = 0;
            $total_linea = 0;
            $saldo_factura = 0;
            echo json_encode($nr.",".$nr_factura_compra.",".$nr_nota_credito_compra.",".$monto_aplicado.",".$total_linea.",".$saldo_factura);
        //}
    }

    if ($action=="EliminarDetalle"){
        @$nr = $_POST['nr_eliminar'];
        @$nr_factura_compra = $_POST['nr_factura_compra_eliminar'];
        @$nr_medio_pago = $_POST['nr_medio_pago_eliminar'];
        @$qty_register = $orden_pago -> rowCount("select * from detalle_orden_pago where nr = '$nr' and nr_factura_compra = '$nr_factura_compra' and nr_medio_pago = '$nr_medio_pago'");
        if ($qty_register>0){
            foreach ($detalle_result as $resultado_detalle) {        
                $nr = $resultado_detalle['nr'];
                $nr_factura_compra = $resultado_detalle['nr_factura_compra'];
                $nr_nota_credito_compra = $resultado_detalle['nr_nota_credito_compra'];
                $medio_pago = $resultado_detalle['medio_pago'];
                $monto_aplicado= $resultado_detalle['monto_aplicado'];
                $total_linea = $resultado_detalle['total_linea'];
            }

            $orden_pago->delete_detalle_orden_pago($nr,$nr_factura_compra,$nr_nota_credito_compra,$medio_pago,$monto_aplicado,$total_linea);
        }
    }

    if ($action=="ConfirmarOrden"){
       //Insert Cabecera Orden Compra
       foreach($cabeceraOP['lines'] as $cabeceraOPLine)
        { 
            if(is_null($cabeceraOPLine['nr'])) 
            {   
                echo "alert('Esta vacio')";
            }
            /*$orden_pago->insert_cabecera_orden_pago($cabeceraOPLine['nr'], $cabeceraOPLine['nr_proveedor'], $cabeceraOPLine['fecha_pago'], 
                $cabeceraOPLine['nr_condicion'], $cabeceraOPLine['nr_sucursal'], $cabeceraOPLine['nr_caja'], $cabeceraOPLine['nr_moneda'], 
                $cabeceraOPLine['fecha_entrega'], $cabeceraOPLine['total_exentas'], $cabeceraOPLine['total_gravadas'], $cabeceraOPLine['total_iva'], 
                $cabeceraOPLine['total_pago'], $cabeceraOPLine['nr_user'], $cabeceraOPLine['cotizacion_compra'], $cabeceraOPLine['cotizacion_venta'], 
                $cabeceraOPLine['obs']);*/
        }

        //Insert Detalle Aplicacion Pago
        foreach($detalleOP['lines'] as $detalleOPLine)
        { 
            if(is_null($detalleOPLine['nr'])) 
            {   
                echo "alert('Esta vacio')";
            }
            $orden_pago->insert_detalle_orden_pago($detalleOPLine['nr'], $detalleOPLine['nr_factura_compra'], $detalleOPLine['nr_nota_credito_compra'], 
                $detalleOPLine['medio_pago'],$detalleOPLine['monto_aplicado'],$detalleOPLine['total_linea']);
        }
    }

    if ($action=="EliminarOrden"){
        @$nr = $_POST['nr'];
        //Check if the Cabecera Orden de Compra already exists if yes
        $orden_pago->delete_orden_pago($nr);
        //echo ();
    }
?>