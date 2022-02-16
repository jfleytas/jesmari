<?php
	require '../clases/formularios/cobranza.class.php';
	$cobranza = cobranza::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GuardarCabecera"){
		@$nr = $_POST['nr'];
        //echo $nr;
        @$nr_cobranza = $_POST['nr_cobranza'];
        //echo $nr_cobranza;
	    @$fecha_cobranza = $_POST['fecha_cobranza'];
        //echo $fecha_cobranza;
        @$nr_cliente = $_POST['nr_cliente'];
        //echo $nr_cliente;
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
        @$nr_cobrador = $_POST['nr_cobrador'];
        //echo $nr_cobrador;
	    @$total_pago=$_POST['total_pago'];
        //echo $total_pago;
        @$obs=$_POST['obs'];
        //echo $obs;
        @$estado=$_POST['estado'];
        //echo $estado;

        //Check if the Cabecera Orden de Pago already exists if yes
        @$qty_register = $cobranza -> rowCount("select * from cabecera_cobranza where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Pedido Sucursal already exists, update it.
            $cobranza->update_cabecera_cobranza($nr,$nr_cobranza,$fecha_cobranza,$nr_cliente,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$nr_cobrador,$obs,$estado);
        }else{
            //Cabecera Orden Pago don't exists, insert it.
            $cobranza->insert_cabecera_cobranza($nr,$nr_cobranza,$fecha_cobranza,$nr_cliente,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$nr_cobrador,$obs,$estado);
        }
        
        echo json_encode($nr.",".$nr_cobranza.",".$fecha_cobranza.",".$nr_cliente.",".$nr_sucursal.",".$nr_caja.",".$nr_moneda.",".$cotizacion_compra.",".$cotizacion_venta.",".$total_pago.",".$nr_user.",".$nr_cobrador.",".$obs.",".$estado);
	}

	if ($action=="GuardarDetallePago"){
        @$nr = $_POST['nr'];
	    @$nr_medio_pago = $_POST['nr_medio_pago'];
        @$monto_pago = $_POST['monto_pago'];
        @$total_linea = $_POST['total_linea'];
        @$obs_detalle = $_POST['obs_detalle'];
	    
	    //Check if the Orden de Compra was stored
	    //@$qty_register = $cobranza -> rowCount("select * from cabecera_cobranza where nr = '$nr'");
	    //if ($qty_register>0){
            $detalleOP = array(
                'lines'=>array(
                array('nr'=>$nr,'nr_medio_pago'=>$nr_medio_pago,'monto_pago'=>$monto_pago,'total_linea'=>$total_linea,'obs_detalle'=>$obs_detalle)
                )
            );

            $cart['lines'][] = array('nr'=>$nr,'nr_medio_pago'=>$nr_medio_pago,'monto_pago'=>$monto_pago,'total_linea'=>$total_linea,'obs_detalle'=>$obs_detalle);

            foreach($detalleOP['lines'] as $detalleOPLine)
            { 
                $cobranza->insert_detalle_cobranza($detalleOPLine['nr'],$detalleOPLine['nr_medio_pago'], $detalleOPLine['monto_pago'], $detalleOPLine['total_linea'], $detalleOPLine['obs_detalle']);
            }

			//$cobranza->insert_detalle_cobranza($nr,$nr_factura_venta,$nf_nota_credito_venta,$medio_pago,$monto_aplicado,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            //echo json_encode($nr.",".$nr_factura_venta.",".$id_producto.",".$descripcion_producto.",".$nf_nota_credito_venta.",".$nr_unidad.",".$id_unidad.",".$monto_aplicado.",".$precio_final.",".$impuesto.",".$medio_pago.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_medio_pago = "";
            $id_medio_pago = "";
            $descripcion_medio_pago = "";
            @$monto_pago = 0;
            $total_linea = 0;
            $obs = "";
			echo json_encode($nr.",".$nr_medio_pago.",".$id_medio_pago.",".$descripcion_medio_pago.",".$monto_pago.",".$total_linea.",".$obs);
	    //}
	}

    if ($action=="GuardarDetalleAplicacionPago"){
        @$nr = $_POST['nr'];
        @$nr_factura_venta = $_POST['nr_factura_venta'];
        @$nr_nota_credito_venta = $_POST['nr_nota_credito_venta'];
        @$monto_aplicado = $_POST['monto_aplicado'];
        @$total_linea = $_POST['total_linea'];
        
        //Check if the Orden de Compra was stored
        //@$qty_register = $cobranza -> rowCount("select * from cabecera_cobranza where nr = '$nr'");
        //if ($qty_register>0){
            $detalleAplicacionOP = array(
                'lines'=>array(
                array('nr'=>$nr,'nr_factura_venta'=>$nr_factura_venta,'nr_nota_credito_venta'=>$nr_nota_credito_venta,
                    'monto_aplicado'=>$monto_aplicado,'total_linea'=>$total_linea)
            )
            );

            $cart['lines'][] = array('nr'=>$nr,'nr_factura_venta'=>$nr_factura_venta,'nr_nota_credito_venta'=>$nr_nota_credito_venta,
                'monto_aplicado'=>$monto_aplicado,'total_linea'=>$total_linea);

            foreach($detalleAplicacionOP['lines'] as $detalleAplicacionOPLine)
            { 
                $cobranza->insert_detalle_aplicacion_cobranza($detalleAplicacionOPLine['nr'], $detalleAplicacionOPLine['nr_factura_venta'], $detalleAplicacionOPLine['nr_nota_credito_venta'], 
                    $detalleAplicacionOPLine['monto_aplicado'], $detalleAplicacionOPLine['total_linea']);
            }

            //$cobranza->insert_detalle_cobranza($nr,$nr_factura_venta,$nf_nota_credito_venta,$medio_pago,$monto_aplicado,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            //echo json_encode($nr.",".$nr_factura_venta.",".$id_producto.",".$descripcion_producto.",".$nf_nota_credito_venta.",".$nr_unidad.",".$id_unidad.",".$monto_aplicado.",".$precio_final.",".$impuesto.",".$medio_pago.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_factura_venta = "";
            $nr_nota_credito_venta = "";
            @$monto_aplicado = 0;
            $total_linea = 0;
            $saldo_factura = 0;
            echo json_encode($nr.",".$nr_factura_venta.",".$nr_nota_credito_venta.",".$monto_aplicado.",".$total_linea.",".$saldo_factura);
        //}
    }

    if ($action=="EliminarDetallePago"){
        @$nr = $_POST['nr'];
        @$nr_medio_pago = $_POST['nr_medio_pago'];

        //Delete the detail
        $cobranza->delete_detalle_cobranza($nr,$nr_medio_pago);
        echo json_encode($action);
    }

    if ($action=="EliminarAplicacionPago"){
        @$nr = $_POST['nr'];
        @$nr_factura_venta = $_POST['nr_factura_venta_eliminar'];

        //Delete the detail
        $cobranza->delete_detalle_aplicacion_cobranza($nr,$nr_factura_venta);
        echo json_encode($action);
    }

    if ($action=="ConfirmarOrden"){
       //Insert Cabecera Orden Compra
       foreach($cabeceraOP['lines'] as $cabeceraOPLine)
        { 
            if(is_null($cabeceraOPLine['nr'])) 
            {   
                echo "alert('Esta vacio')";
            }
            /*$cobranza->insert_cabecera_cobranza($cabeceraOPLine['nr'], $cabeceraOPLine['nr_cliente'], $cabeceraOPLine['fecha_cobranza'], 
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
            $cobranza->insert_detalle_cobranza($detalleOPLine['nr'], $detalleOPLine['nr_factura_venta'], $detalleOPLine['nr_nota_credito_venta'], 
                $detalleOPLine['medio_pago'],$detalleOPLine['monto_aplicado'],$detalleOPLine['total_linea']);
        }
    }

    if ($action=="EliminarOrden"){
        @$nr = $_POST['nr'];
        //Check if the Cabecera Orden de Compra already exists if yes
        $cobranza->delete_cobranza($nr);
        //echo ();
    }
?>