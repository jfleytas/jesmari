<?php
	require '../clases/formularios/pedido_sucursal.class.php';
	$pedido_sucursal = pedido_sucursal::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
	    @$fecha_pedido = $_POST['fecha_pedido'];
	    @$nr_sucursal_origen = $_POST['nr_sucursal_origen'];
	    @$nr_deposito_origen = $_POST['nr_deposito_origen'];
	    @$nr_sucursal_destino = $_POST['nr_sucursal_destino'];
        @$nr_deposito_destino = $_POST['nr_deposito_destino'];
	    @$nr_moneda = $_POST['nr_moneda'];
	    @$total_pedido=$_POST['total_pedido'];
        @$nr_user = $_POST['nr_user'];
        @$cotizacion_compra = $_POST['cotizacion_compra'];
        @$cotizacion_venta = $_POST['cotizacion_venta'];
        @$obs=$_POST['obs'];
        //Check if the Cabecera Orden de Compra already exists if yes
        @$qty_register = $pedido_sucursal -> rowCount("select * from cabecera_pedido_sucursal where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Pedido Sucursal already exists, update it.
		    $pedido_sucursal->update_cabecera_pedido_sucursal($nr,$fecha_pedido,$nr_sucursal_origen,$nr_deposito_origen,$nr_sucursal_destino,$nr_deposito_destino,$nr_moneda,$total_pedido,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
        }else{
            //Cabecera Pedido Sucursal don't exists, insert it.
            $pedido_sucursal->insert_cabecera_pedido_sucursal($nr,$fecha_pedido,$nr_sucursal_origen,$nr_deposito_origen,$nr_sucursal_destino,$nr_deposito_destino,$nr_moneda,$total_pedido,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
        }
        echo json_encode($nr.",".$fecha_pedido.",".$nr_sucursal_origen.",".$nr_deposito_origen.",".$nr_sucursal_destino.",".$nr_deposito_destino.",".$nr_moneda.",".$total_pedido.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
	}

	if ($action=="GenerarDetalle"){
        @$nr = $_POST['nr'];
		@$nr_producto = $_POST['nr_producto'];
	    @$cantidad = $_POST['cantidad'];
	    @$costo = $_POST['costo'];
	    @$total_linea = $_POST['total_linea'];
	    @$impuesto = $_POST['impuesto'];
	    @$nr_unidad = $_POST['nr_unidad'];

	    //Check if the Orden de Compra was stored
	    @$qty_register = $pedido_sucursal -> rowCount("select * from cabecera_pedido_sucursal where nr = '$nr'");
	    if ($qty_register>0){
			$pedido_sucursal->insert_detalle_pedido_sucursal($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad);
            //echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$costo_lista.",".$costo_final.",".$impuesto.",".$descuento.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_producto = "";
            @$id_producto = "";
            @$descripcion_producto = "";
            $cantidad = "";
            $nr_unidad = "";
            @$id_unidad = "";
            @$costo = 0;
            $impuesto = 0;
            $total_linea = 0;
			echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$costo.",".$total_linea);
	    }
	}

    if ($action=="EliminarDetalle"){
        @$nr = $_POST['nr'];
        @$fecha_pedido = $_POST['fecha_pedido'];
        @$nr_sucursal_origen = $_POST['nr_sucursal_origen'];
        @$nr_deposito_origen = $_POST['nr_deposito_origen'];
        @$nr_sucursal_destino = $_POST['nr_sucursal_destino'];
        @$nr_deposito_destino = $_POST['nr_deposito_destino'];
        @$nr_moneda = $_POST['nr_moneda'];
        @$nr_user = $_POST['nr_user'];
        @$cotizacion_compra = $_POST['cotizacion_compra'];
        @$cotizacion_venta = $_POST['cotizacion_venta'];
        @$total_pedido=$_POST['total_pedido'];

        $pedido_sucursal->delete_detalle_pedido_sucursal($nr,$fecha_pedido,$nr_sucursal_origen,$nr_deposito_origen,$nr_sucursal_destino,$nr_deposito_destino,$nr_moneda,$total_pedido,$nr_user,$cotizacion_compra,$cotizacion_venta);
        echo ($nr.",".$fecha_pedido.",".$nr_sucursal_origen.",".$nr_deposito_origen.",".$nr_sucursal_destino.",".$nr_deposito_destino.",".$nr_moneda.",".$total_pedido.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta);
    }

    if ($action=="EliminarPedido"){
        @$nr = $_POST['nr'];
        //Check if the Pedido Sucursal already exists if yes
        $pedido_sucursal->delete_pedido_sucursal($nr);
        //echo ();
    }
?>