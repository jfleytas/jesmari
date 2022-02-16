<?php
	require '../clases/formularios/ingreso_stock.class.php';
	$ingreso_stock = ingreso_stock::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
	    @$fecha_ingreso = $_POST['fecha_ingreso'];
	    @$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito = $_POST['nr_deposito'];
        @$nr_moneda = $_POST['nr_moneda'];
        @$total_ingreso=$_POST['total_ingreso'];
        @$nr_user = $_POST['nr_user'];
        @$cotizacion_compra = $_POST['cotizacion_compra'];
        @$cotizacion_venta = $_POST['cotizacion_venta'];
        @$obs=$_POST['obs'];
        //Check if the Cabecera Orden de Compra already exists if yes
        @$qty_register = $ingreso_stock -> rowCount("select * from cabecera_ingreso_stock where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Ingreso Stock already exists, update it.
            $ingreso_stock->update_cabecera_ingreso_stock($nr,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
        }else{
            //Cabecera Ingreso Stock don't exists, insert it.
            $ingreso_stock->insert_cabecera_ingreso_stock($nr,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs);
        }
        echo json_encode($nr.",".$fecha_ingreso.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$total_ingreso.",".$nr_user.",".$cotizacion_compra.",".$cotizacion_venta.",".$obs);
	}

	if ($action=="GenerarDetalle"){
        @$nr = $_POST['nr'];
		@$nr_producto = $_POST['nr_producto'];
	    @$cantidad = $_POST['cantidad'];
	    @$costo = $_POST['costo'];
        @$nr_unidad = $_POST['nr_unidad'];
	    @$total_linea = $_POST['total_linea'];
        
	    //Check if the Orden de Compra was stored
	    @$qty_register = $ingreso_stock -> rowCount("select * from cabecera_ingreso_stock where nr = '$nr'");
	    if ($qty_register>0){
			$ingreso_stock->insert_detalle_ingreso_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad);
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
        @$fecha_ingreso = $_POST['fecha_ingreso'];
        @$nr_sucursal = $_POST['nr_sucursal'];
        @$nr_deposito = $_POST['nr_deposito'];
        @$nr_moneda = $_POST['nr_moneda'];
        @$nr_user = $_POST['nr_user'];
        @$total_ingreso=$_POST['total_ingreso'];

        $ingreso_stock->delete_detalle_ingreso_stock($nr,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_ingreso,$nr_user);
        echo ($nr.",".$fecha_ingreso.",".$nr_sucursal.",".$nr_deposito.",".$nr_moneda.",".$total_ingreso.",".$nr_user);
    }

    if ($action=="EliminarIngreso"){
        @$nr = $_POST['nr'];
        //Check if the Cabecera Orden de Compra already exists if yes
        $ingreso_stock->delete_ingreso_stock($nr);
        //echo ();
    }
?>