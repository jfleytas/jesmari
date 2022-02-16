<?php
	require '../clases/formularios/egreso_stock.class.php';
	$egreso_stock = egreso_stock::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
        //echo $nr;
	    @$fecha_egreso = $_POST['fecha_egreso'];
	    @$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito = $_POST['nr_deposito'];
        @$nr_moneda = $_POST['nr_moneda'];
	    @$total_egreso=$_POST['total_egreso'];
        @$nr_user = $_POST['nr_user'];
        @$cotizacion_compra = $_POST['cotizacion_compra'];
        @$cotizacion_venta = $_POST['cotizacion_venta'];
        @$obs=$_POST['obs'];
        //Check if the Cabecera Egreso Stock already exists if yes
        @$qty_register = $egreso_stock -> rowCount("select * from cabecera_egreso_stock where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Egreso Stock already exists, update it.
            $egreso_stock->update_cabecera_egreso_stock($nr,$fecha_egreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_egreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
		    echo json_encode($nr.",".$fecha_egreso.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$total_egreso.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
        }else{
            //Cabecera Egreso Stock don't exists, insert it.
            $egreso_stock->insert_cabecera_egreso_stock($nr,$fecha_egreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_egreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
            echo json_encode($nr.",".$fecha_egreso.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$total_egreso.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
        }
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
	    @$qty_register = $egreso_stock -> rowCount("select * from cabecera_egreso_stock where nr = '$nr'");
	    if ($qty_register>0){
			$egreso_stock->insert_detalle_egreso_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad);
            //echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$costo_lista.",".$costo_final.",".$impuesto.",".$descuento.",".$total_linea.",".$total_gravadas_linea.",".$total_exentas_linea.",".$total_iva_linea);
            $nr_producto = "";
            @$id_producto = "";
            @$descripcion_producto = "";
            $cantidad = "";
            $nr_unidad = "";
            @$id_unidad = "";
            @$costo = 0;
            $total_linea = 0;
			echo json_encode($nr.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$costo.",".$total_linea);
	    }
	}

    if ($action=="EliminarDetalle"){
        @$nr = $_POST['nr'];
        @$fecha_egreso = $_POST['fecha_egreso'];
        @$nr_sucursal = $_POST['nr_sucursal'];
        @$nr_deposito = $_POST['nr_deposito'];
        @$nr_moneda = $_POST['nr_moneda'];
        @$nr_user = $_POST['nr_user'];
        @$total_egreso=$_POST['total_egreso'];

        $egreso_stock->delete_detalle_egreso_stock($nr,$fecha_egreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_egreso,$nr_user);
        echo ($nr.",".$fecha_egreso.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$total_egreso.",".$nr_user);
    }

    if ($action=="EliminarEgreso"){
        @$nr = $_POST['nr'];
        //Check if the Cabecera Orden de Compra already exists if yes
        $egreso_stock->delete_egreso_stock($nr);
        //echo ();
    }
?>