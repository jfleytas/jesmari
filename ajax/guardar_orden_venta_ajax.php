<?php
	require '../clases/formularios/orden_venta.class.php';
	$orden_venta = orden_venta::singleton();

	//Get the all the data Cabecera and Detalle
	@$action = $_POST['action'];
	if ($action=="Crear"){
		@$nr = $_POST['nr'];
		@$nr_cliente = $_POST['nr_cliente'];
	    @$fecha_orden = $_POST['fecha_orden'];
	    @$nr_condicion = $_POST['nr_condicion'];
	    @$nr_sucursal = $_POST['nr_sucursal'];
	    @$nr_deposito = $_POST['nr_deposito'];
	    @$nr_moneda = $_POST['nr_moneda'];
        @$nr_lista_precios = $_POST['nr_lista_precios'];
	    @$nr_vendedor = $_POST['nr_vendedor'];
	    @$nr_user = $_POST['nr_user'];
	    @$cotizacion_compra = $_POST['cotizacion_compra'];
	    @$cotizacion_venta = $_POST['cotizacion_venta'];
	    @$total_exentas=0;
	    @$total_gravadas=0;
        @$total_iva=0;
	    @$total_orden=0;
        @$obs = $_POST['obs'];
        //Check if the Cabecera Orden de Venta already exists if yes
        @$qty_register = $orden_venta -> rowCount("select * from cabecera_orden_venta where nr = '$nr'");
        if ($qty_register>0){
            //Cabecera Orden de Venta already exists, update it.
            $orden_venta->update_cabecera_orden_venta($nr,$nr_cliente,$fecha_orden,$nr_vendedor,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$nr_lista_precios,$cotizacion_compra,$cotizacion_venta,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$obs);
            echo json_encode($nr.",".$action.",".$nr_cliente.",".$fecha_orden.",".$nr_condicion);
        }else{
            //Cabecera Orden de Venta don't exists, insert it.
            $orden_venta->insert_cabecera_orden_venta($nr,$nr_cliente,$fecha_orden,$nr_vendedor,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$nr_lista_precios,$cotizacion_compra,$cotizacion_venta,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$obs);
            echo json_encode($nr.",".$action.",".$nr_cliente.",".$fecha_orden.",".$nr_condicion);
        }
	}

	if ($action=="AgregarDetalle"){
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

	    //Check if the Orden de venta was stored
    	@$qty_register = $orden_venta -> rowCount("select * from cabecera_orden_venta where nr = '$nr'");
    	if ($qty_register>0){
    	    $orden_venta->insert_detalle_orden_venta($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            $nr_producto = "";
            @$id_producto = "";
            @$descripcion_producto = "";
            $cantidad = "";
            $nr_unidad = "";
            @$id_unidad = "";
            @$precio_lista = 0;
            @$precio_final = 0;
            $impuesto = 0;
            $descuento = 0;
            $total_linea = 0;
            @$stock_actual = "";
    		echo json_encode($nr.",".$action.",".$nr_producto.",".$id_producto.",".$descripcion_producto.",".$cantidad.",".$nr_unidad.",".$id_unidad.",".$precio_lista.",".$precio_final.",".$impuesto.",".$descuento.",".$total_linea.",".$stock_actual);
    	}
	}

    if ($action=="EliminarDetalle"){
        @$nr = $_POST['nr'];
        @$nr_producto = $_POST['nr_producto'];

        //Delete the detail
        $orden_venta->delete_detalle_orden_venta($nr,$nr_producto);
        echo json_encode($action);
    }

    if ($action=="EditarDetalle"){
        @$nr = $_POST['nr'];
        @$nr_producto = $_POST['nr_producto'];

        //Edit Form
        echo '<div id="modal" class="modalstyle" id="modalAsistencia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        echo '<div class="modalbox movedown">';
            echo '<a href="banco.php" title="Cerrar" class="close">X</a>';
            echo '<div class="container">';
                echo '<div class="span10 offset1">';
                    echo '<p hidden><input type="number" id="nr" name="nr" value="'. $nr .'"> </p>';
                    echo '<b><label class="control-label">Codigo</label></b>';
                    echo '<input type="text" id="id_producto" name="id_producto" value="'. $id_producto .'" class="boxes"><p>';
                    echo '<b><label class="control-label">Descripcion</label></b>';
                    echo '<input type="text" id="descripcion" name="descripcion" value="'. $descripcion .'" class="boxes"><p>';
                    echo '<b><label class="control-label">Cantidad</label></b>';
                    echo '<input type="text" id="cantidad" name="cantidad" value="'. $cantidad .'" class="boxes" required><p>';
                    echo '<b><label class="control-label">Precio</label></b>';
                    echo '<input type="text" id="precio_lista" name="precio_lista" value="'. $precio_lista .'" class="boxes" required><p>';
                    echo '<b><label class="control-label">Total</label></b>';
                    echo '<input type="text" id="total_linea" name="total_linea" value="'. $total_linea .'" class="boxes"><p>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
        echo '</div>';

        //Edit the detail
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
        
        $orden_venta->update_detalle_orden_venta($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas,$total_exentas,$total_linea,$impuesto,$nr_unidad);
        echo json_encode($action);

    }

    if ($action=="EliminarOrden"){
        @$nr = $_POST['nr'];
        //Delete the entry
        $orden_venta->delete_orden_venta($nr);
        //echo ();
    }
?>