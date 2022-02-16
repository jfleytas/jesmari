<?php
	require '../clases/formularios/orden_compra.class.php';
	$orden_compra = orden_compra::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
		@$nr_proveedor = $_POST['nr_proveedor'];
	    @$fecha_orden = $_POST['fecha_orden'];
	    @$nr_condicion = $_POST['nr_condicion'];
	    @$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito = $_POST['nr_deposito'];
	    @$nr_moneda = $_POST['nr_moneda'];
	    @$fecha_entrega = $_POST['fecha_entrega'];
	    @$nr_user = $_POST['nr_user'];
	    @$cotizacion_compra = $_POST['cotizacion_compra'];
	    @$cotizacion_venta = $_POST['cotizacion_venta'];
	    @$total_exentas=$_POST['total_exentas'];
	    @$total_gravadas=$_POST['total_gravadas'];
        @$total_iva=$_POST['total_iva'];
	    @$total_orden=$_POST['total_orden'];
        @$obs=$_POST['obs'];

        $cabeceraOC = array(
            'lines'=>array(
            array('nr'=>$nr,'nr_proveedor' =>$nr_proveedor,'fecha_orden' =>$fecha_orden,'nr_condicion' =>$nr_condicion,'nr_sucursal' =>$nr_sucursal,'nr_deposito' =>$nr_deposito,'nr_moneda' =>$nr_moneda,'fecha_entrega' =>$fecha_entrega,'total_exentas' =>$total_exentas,'total_gravadas' =>$total_gravadas,'total_iva' =>$total_iva,'total_orden' =>$total_orden,'nr_user' =>$nr_user,'cotizacion_compra' =>$cotizacion_compra,'cotizacion_venta' =>$cotizacion_venta,'obs' =>$obs)
            )
        );

        foreach($cabeceraOC['lines'] as $cabeceraOCLine)
        { 
            $orden_compra->insert_cabecera_orden_compra($cabeceraOCLine['nr'], $cabeceraOCLine['nr_proveedor'], $cabeceraOCLine['fecha_orden'], 
                $cabeceraOCLine['nr_condicion'], $cabeceraOCLine['nr_sucursal'], $cabeceraOCLine['nr_deposito'], $cabeceraOCLine['nr_moneda'], 
                $cabeceraOCLine['fecha_entrega'], $cabeceraOCLine['total_exentas'], $cabeceraOCLine['total_gravadas'], $cabeceraOCLine['total_iva'], 
                $cabeceraOCLine['total_orden'], $cabeceraOCLine['nr_user'], $cabeceraOCLine['cotizacion_compra'], $cabeceraOCLine['cotizacion_venta'], 
                $cabeceraOCLine['obs']);
        }
        
        echo json_encode($nr.",".$nr_proveedor.",".$fecha_orden.",".$nr_condicion.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$fecha_entrega.",".$total_exentas.",".$total_gravadas.",".$total_iva.",".$total_orden.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
        //Check if the Cabecera Orden de Compra already exists if yes
        /*if (sizeof($_SESSION['cabeceraOC'])==16)
        {
            @$qty_register = $orden_compra -> rowCount("select * from cabecera_orden_compra where nr = '$nr'");
            if ($qty_register>0){
                //Cabecera Orden de Compra already exists, update it.
        		$orden_compra->update_cabecera_orden_compra($nr,$nr_proveedor,$fecha_orden,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_entrega,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
        		echo json_encode($nr.",".$nr_proveedor.",".$fecha_orden.",".$nr_condicion.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$fecha_entrega.",".$total_exentas.",".$total_gravadas.",".$total_iva.",".$total_orden.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
            }else{
                //Cabecera Orden de Compra don't exists, insert it.
                $orden_compra->insert_cabecera_orden_compra($nr,$nr_proveedor,$fecha_orden,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_entrega,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
                echo json_encode($nr.",".$nr_proveedor.",".$fecha_orden.",".$nr_condicion.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$fecha_entrega.",".$total_exentas.",".$total_gravadas.",".$total_iva.",".$total_orden.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
            }
        }*/
	}

	if ($action=="GenerarDetalle"){
        @$nr = $_POST['nr'];
		@$nr_producto = $_POST['nr_producto'];
	    @$cantidad = $_POST['cantidad'];
	    @$descuento = $_POST['descuento'];
        @$precio_lista = $_POST['precio_lista'];
        @$precio_final = $_POST['precio_final'];
	    @$total_gravadas_linea = $_POST['total_gravadas_linea'];
	    @$total_exentas_linea = $_POST['total_exentas_linea'];
        @$total_iva_linea = $_POST['total_iva_linea'];
	    @$total_linea = $_POST['total_linea'];
	    @$impuesto = $_POST['impuesto'];
	    @$nr_unidad = $_POST['nr_unidad'];

	    //Check if the Orden de Compra was stored
	    //@$qty_register = $orden_compra -> rowCount("select * from cabecera_orden_compra where nr = '$nr'");
	    //if ($qty_register>0){
            $detalleOC = array(
                'lines'=>array(
                array('nr'=>$nr,'nr_producto'=>$nr_producto,'cantidad'=>$cantidad,'descuento'=>$descuento,'precio_final'=>$precio_final,
                    'precio_lista'=>$precio_lista,'total_gravadas_linea'=>$total_gravadas_linea,'total_exentas_linea'=>$total_exentas_linea,
                    'total_iva_linea'=>$total_iva_linea,'total_linea'=>$total_linea,'impuesto'=>$impuesto,'nr_unidad'=>$nr_unidad)
            )
            );

            $cart['lines'][] = array('nr'=>$nr,'nr_producto'=>$nr_producto,'cantidad'=>$cantidad,'descuento'=>$descuento,'precio_lista'=>$precio_lista,
                'precio_final'=>$precio_final, 'total_gravadas_linea'=>$total_gravadas_linea,'total_exentas_linea'=>$total_exentas_linea,
                'total_iva_linea'=>$total_iva_linea,'total_linea'=>$total_linea,'impuesto'=>$impuesto,'nr_unidad'=>$nr_unidad);

            foreach($detalleOC['lines'] as $detalleOCLine)
            { 
                $orden_compra->insert_detalle_orden_compra($detalleOCLine['nr'], $detalleOCLine['nr_producto'], $detalleOCLine['cantidad'], 
                    $detalleOCLine['descuento'], $detalleOCLine['precio_lista'], $detalleOCLine['precio_final'], $detalleOCLine['total_gravadas_linea'], 
                    $detalleOCLine['total_exentas_linea'], $detalleOCLine['total_iva_linea'], $detalleOCLine['total_linea'], $detalleOCLine['impuesto'], 
                    $detalleOCLine['nr_unidad']);
            }

			//$orden_compra->insert_detalle_orden_compra($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            //echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$precio_lista.",".$precio_final.",".$impuesto.",".$descuento.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_producto = "";
            @$id_producto = "";
            @$descripcion_producto = "";
            $cantidad = "";
            $nr_unidad = "";
            @$id_unidad = "";
            @$precio_lista = 0;
            @$precio_final = 0;
            $impuesto = 0;
            $total_gravadas_linea = 0;
            $total_exentas_linea = 0;
            $total_iva_linea = 0;
            $descuento = 0;
            $total_linea = 0;
			echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$precio_lista.",".$precio_final.",".$impuesto.",".$descuento.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
	    //}
	}

    if ($action=="EliminarDetalle"){
        @$nr = $_POST['nr'];
        @$nr_producto = $_POST['nr_producto'];

        //Delete the detail
        $orden_compra->delete_detalle_orden_compra($nr,$nr_producto);
        echo json_encode($action);
    }

    if ($action=="ConfirmarOrden"){
       //Insert Cabecera Orden Compra
       foreach($cabeceraOC['lines'] as $cabeceraOCLine)
        { 
            if(is_null($cabeceraOCLine['nr'])) 
            {   
                echo "alert('Esta vacio')";
            }
            /*$orden_compra->insert_cabecera_orden_compra($cabeceraOCLine['nr'], $cabeceraOCLine['nr_proveedor'], $cabeceraOCLine['fecha_orden'], 
                $cabeceraOCLine['nr_condicion'], $cabeceraOCLine['nr_sucursal'], $cabeceraOCLine['nr_deposito'], $cabeceraOCLine['nr_moneda'], 
                $cabeceraOCLine['fecha_entrega'], $cabeceraOCLine['total_exentas'], $cabeceraOCLine['total_gravadas'], $cabeceraOCLine['total_iva'], 
                $cabeceraOCLine['total_orden'], $cabeceraOCLine['nr_user'], $cabeceraOCLine['cotizacion_compra'], $cabeceraOCLine['cotizacion_venta'], 
                $cabeceraOCLine['obs']);*/
        }

        //Insert Detalle Orden Compra
        foreach($detalleOC['lines'] as $detalleOCLine)
        { 
            if(is_null($detalleOCLine['nr'])) 
            {   
                echo "alert('Esta vacio')";
            }
            $orden_compra->insert_detalle_orden_compra($detalleOCLine['nr'], $detalleOCLine['nr_producto'], $detalleOCLine['cantidad'], 
                $detalleOCLine['descuento'], $detalleOCLine['precio_final'], $detalleOCLine['precio_lista'], $detalleOCLine['total_gravadas_linea'], 
                $detalleOCLine['total_exentas_linea'], $detalleOCLine['total_iva_linea'], $detalleOCLine['total_linea'], $detalleOCLine['impuesto'], 
                $detalleOCLine['nr_unidad']);
        }
    }

    if ($action=="EliminarOrden"){
        @$nr = $_POST['nr'];
        //Check if the Cabecera Orden de Compra already exists if yes
        $orden_compra->delete_orden_compra($nr);
        //echo ();
    }
?>