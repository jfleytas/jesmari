<?php
	require '../clases/formularios/transferencia_stock.class.php';
	$transferencia_stock = transferencia_stock::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="GenerarCabecera"){
		@$nr = $_POST['nr'];
	    @$fecha_transferencia = $_POST['fecha_transferencia'];
	    @$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito_origen = $_POST['nr_deposito_origen'];
        @$nr_deposito_destino = $_POST['nr_deposito_destino'];
	    @$total_transferencia=$_POST['total_transferencia'];
        @$nr_user = $_POST['nr_user'];
        @$obs=$_POST['obs'];
        //Check if the Cabecera Transferencia Stock already exists if yes
        @$qty_register = $transferencia_stock -> rowCount("select * from cabecera_transferencia_stock where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Transferencia Stock already exists, update it.
            $transferencia_stock->update_cabecera_transferencia_stock($nr,$fecha_transferencia,$nr_sucursal,$nr_deposito_origen,$nr_deposito_destino,$total_transferencia,$nr_user,$obs);
            echo json_encode($nr.",".$fecha_transferencia.",".$nr_sucursal.",".$nr_deposito_origen.",".$nr_deposito_destino.",".$total_transferencia.",".$nr_user.",".$obs);
        }else{
            //Cabecera Transferencia Stock don't exists, insert it.
            $transferencia_stock->insert_cabecera_transferencia_stock($nr,$fecha_transferencia,$nr_sucursal,$nr_deposito_origen,$nr_deposito_destino,$total_transferencia,$nr_user,$obs);
            echo json_encode($nr.",".$fecha_transferencia.",".$nr_sucursal.",".$nr_deposito_origen.",".$nr_deposito_destino.",".$total_transferencia.",".$nr_user.",".$obs);
        }
	}

	if ($action=="GenerarDetalle"){
        @$nr = $_POST['nr'];
		@$nr_producto = $_POST['nr_producto'];
	    @$cantidad = $_POST['cantidad'];
	    @$costo = $_POST['costo'];
	    @$total_linea = $_POST['total_linea'];
	    @$nr_unidad = $_POST['nr_unidad'];

	    //Check if the Orden de Compra was stored
	    @$qty_register = $transferencia_stock -> rowCount("select * from cabecera_transferencia_stock where nr = '$nr'");
	    if ($qty_register>0){
			$transferencia_stock->insert_detalle_transferencia_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad);
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
        @$fecha_transferencia = $_POST['fecha_transferencia'];
        @$nr_sucursal = $_POST['nr_sucursal'];
        @$nr_deposito_origen = $_POST['nr_deposito_origen'];
        @$nr_deposito_destino = $_POST['nr_deposito_destino'];
        @$nr_user = $_POST['nr_user'];
        @$total_transferencia=$_POST['total_transferencia'];

        $transferencia_stock->delete_detalle_transferencia_stock($nr,$fecha_transferencia,$nr_sucursal,$nr_deposito_origen,$nr_deposito_destino,$total_transferencia,$nr_user);
        echo ($nr.",".$fecha_transferencia.",".$nr_sucursal.",".$nr_deposito_origen.",".$nr_deposito_destino.",".$total_transferencia.",".$nr_user);
    }

    if ($action=="EliminarTransferencia"){
        @$nr = $_POST['nr'];
        //Delete the entry
        $transferencia_stock->delete_transferencia_stock($nr);
        //echo ();
    }
?>