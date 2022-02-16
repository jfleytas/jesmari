<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
    require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/factura_venta.class.php';
    $factura_venta = factura_venta::singleton();

    $page_name="factura_venta_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $orden_venta=isset($_POST['orden_venta']) ? $_POST['orden_venta'] : '';
    $nr_factura=isset($_POST['nr_factura']) ? $_POST['nr_factura'] : '';
    $fecha_factura=isset($_POST['fecha_factura']) ? $_POST['fecha_factura'] : '';
    $fecha_vto=isset($_POST['fecha_vto']) ? $_POST['fecha_vto'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_cliente=isset($_POST['nr_cliente']) ? $_POST['nr_cliente'] : '';
    $id_cliente=isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $descripcion_cliente=isset($_POST['descripcion_cliente']) ? $_POST['descripcion_cliente'] : '';
    $nr_vendedor=isset($_POST['nr_vendedor']) ? $_POST['nr_vendedor'] : '';
    $descripcion_vendedor=isset($_POST['descripcion_vendedor']) ? $_POST['descripcion_vendedor'] : '';
    $nr_condicion=isset($_POST['nr_condicion']) ? $_POST['nr_condicion'] : '';
    $descripcion_condicion=isset($_POST['descripcion_condicion']) ? $_POST['descripcion_condicion'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $nr_deposito=isset($_POST['nr_deposito']) ? $_POST['nr_deposito'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $cantidad=isset($_POST['cantidad']) ? $_POST['cantidad'] : '';
    $nr_unidad=isset($_POST['nr_unidad']) ? $_POST['nr_unidad'] : '';
    $id_unidad=isset($_POST['id_unidad']) ? $_POST['id_unidad'] : '';
    $precio_lista=isset($_POST['precio_lista']) ? $_POST['precio_lista'] : '';
    $precio_final=isset($_POST['precio_final']) ? $_POST['precio_final'] : '';
    $costo=isset($_POST['costo']) ? $_POST['costo'] : '';
    $impuesto=isset($_POST['impuesto']) ? $_POST['impuesto'] : '';
    $descuento=isset($_POST['descuento']) ? $_POST['descuento'] : '';
    $total_exentas=isset($_POST['total_exentas']) ? $_POST['total_exentas'] : '';
    $total_gravadas=isset($_POST['total_gravadas']) ? $_POST['total_gravadas'] : '';
    $total_iva=isset($_POST['total_iva']) ? $_POST['total_iva'] : '';
    $total_exentas_linea=isset($_POST['total_exentas_linea']) ? $_POST['total_exentas_linea'] : '';
    $total_gravadas_linea=isset($_POST['total_gravadas_linea']) ? $_POST['total_gravadas_linea'] : '';
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : '';
    $total_iva_linea=isset($_POST['total_iva_linea']) ? $_POST['total_iva_linea'] : '';
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : '';
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : '';
    $total_factura=isset($_POST['total_factura']) ? $_POST['total_factura'] : '';
    $saldo_factura=isset($_POST['saldo_factura']) ? $_POST['saldo_factura'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : 1;
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : 1;
    $dias=isset($_POST['dias']) ? $_POST['dias'] : '';
    $estado=isset($_POST['estado']) ? $_POST['estado'] : 0;
    $tipo_factura=isset($_POST['tipo_factura']) ? $_POST['tipo_factura'] : 'A';
    $total_costo=isset($_POST['total_costo']) ? $_POST['total_costo'] : 0;

    //Defining Order # checking the next value from the sequence
    $factura_result = $factura_venta->query("select nextval('cabecera_factura_venta_nr_seq')");
    //$factura_result = $factura_venta->query("select last_value from cabecera_factura_venta_nr_seq");
    foreach ($factura_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the configuration for the form
    $configuracion_result = $factura_venta->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        @$formato_factura = $resultado_config['formato_factura'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $factura_venta->query("select * from users where id_user ='$usuario'");//We get the desired result from the table
    foreach ($user_result as $resultado_user) {        
        $nr_user = $resultado_user['nr'];
        $id_user = $resultado_user['id_user'];
        $nombre_apellido = $resultado_user['nombre_apellido'];
    }

    //Get the Orden de Venta
    if(isset($_GET['orden']))
    {
        $orden_venta = $_GET['orden'];
        /* Lista Cabecera section */
        $cabecera_result = $factura_venta->query("select * from cabecera_orden_venta where nr = '$orden_venta'");//We get all the results from the associated OC
        /* Lista Detalle section */
        $detalle_result = $factura_venta->query("select * from detalle_orden_venta where facturado < cantidad and nr = '$orden_venta'");//We get all the results from the associated OC
        /* Quantity of lines in Detalle section */
        $detalle_result_list = $factura_venta->query("select OC.nr, OC.nr_producto, P.id_producto, P.descripcion descripcion_producto, OC.cantidad, OC.precio_lista, OC.precio_final, OC.descuento, OC.impuesto, OC.nr_unidad, UM.descripcion descripcion_unidad, OC.total_gravadas_linea, OC.total_exentas_linea,OC.total_iva_linea, OC.total_linea from detalle_orden_venta OC join productos P on OC.nr_producto = P.nr join unidad_medida UM on OC.nr_unidad = UM.nr where OC.nr = '$orden_venta'");//We get all the results from the associated OC
        @$qty_register = $factura_venta -> rowCount("select * from detalle_orden_venta where nr = '$orden_venta'");

        //Get the values from Cabecera
        foreach($cabecera_result as $total_row):
            $nr_cliente= $total_row['nr_cliente'];
            $nr_vendedor= $total_row['nr_vendedor'];
            $nr_condicion= $total_row['nr_condicion'];
            $nr_sucursal= $total_row['nr_sucursal'];
            $nr_deposito= $total_row['nr_deposito'];
            $nr_moneda= $total_row['nr_moneda'];
            $cotizacion_compra= $total_row['cotizacion_compra'];
            $cotizacion_venta= $total_row['cotizacion_venta'];
            $total_exentas= $total_row['total_exentas'];
            //echo 'total exentas'.$total_exentas;
            $total_gravadas= $total_row['total_gravadas'];
            //echo 'total gravadas'.$total_gravadas;
            $total_iva= $total_row['total_iva'];
            $total_factura= $total_row['total_orden'];
            $orden_venta=$total_row['nr'];
        endforeach;

        //Get the values from Detalle
        foreach($detalle_result as $total_row):
            $nr_producto= $total_row['nr_producto'];
            //$cantidad= number_format(floatval($total_row['cantidad']),2,",",".") - number_format(floatval($total_row['facturado']),2,",",".");
			$cantidad= number_format(floatval($total_row['cantidad']) - floatval($total_row['facturado']),2,",",".");
            $descuento= $total_row['descuento'];
            $precio_lista=$total_row['precio_lista'];
            $precio_final=$total_row['precio_final'];
            $nr_unidad= $total_row['nr_unidad'];
            $impuesto= $total_row['impuesto'];
            $total_gravadas_linea= $total_row['total_gravadas_linea'];
            $total_exentas_linea= $total_row['total_exentas_linea'];
            $total_iva_linea= $total_row['total_iva_linea'];
            $total_linea= $total_row['total_linea'];
        endforeach;

        //Get the last Invoice Number
        $fv_result = $factura_venta->query("select * from comprobantes where nr_sucursal = '$nr_sucursal' and aplicado_a='factura_venta'");//We get the desired result from the table
        foreach ($fv_result as $resultado_fv) {        
            $formato_factura = $resultado_fv['formato_comprobante'];
            @$ultimo_nro_impreso = $resultado_fv['ultimo_nro_impreso'];
        }

        #Create the next Invoice number
        @$nr_factura_venta = $ultimo_nro_impreso + 1;
        $nr_factura = $formato_factura.'-'.str_pad($nr_factura_venta, 7, '0', STR_PAD_LEFT);

        //Get the cliente code and descripcion
        $cliente_result = $factura_venta->query("select * from clientes where nr = '$nr_cliente'");//We get all the results from the table
        foreach($cliente_result as $total_row):
            $id_cliente= $total_row['id_cliente'];
            $descripcion_cliente= $total_row['razon_social'];
        endforeach;

         //Get the Vendedor code and descripcion
        $vendedor_result = $factura_venta->query("select * from personal where nr = '$nr_vendedor'");//We get all the results from the table
        foreach($vendedor_result as $total_row):
            $id_vendedor= $total_row['id_personal'];
            $descripcion_vendedor= $total_row['nombre_apellido'];
            
        endforeach;

        //Get the Producto code and descripcion
        $producto_result = $factura_venta->query("select * from productos where nr = '$nr_producto'");//We get all the results from the table
        foreach($producto_result as $total_row):
            $id_producto= $total_row['id_producto'];
            $descripcion_producto= $total_row['descripcion'];
        endforeach;

        //Get Condicion Compra Venta
        $condicion_result = $factura_venta->query("select * from condicion_compra_venta where nr = '$nr_condicion'");//We get all the results from the table
        foreach($condicion_result as $total_row):
            $id_condicion= $total_row['id_condicion'];
            $dias= $total_row['cant_dias'];
        endforeach;

    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="../css/estilo_formulario.css" />
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
        <script src="../js/jquery.js"></script>
	</head>
	<body>
    <form id = "factura_venta_form" name = "factura_venta_form"class="form-horizontal" action="factura_venta_form.php" method="POST" name = "factura_venta_form" autocomplete = "off">
    <!-- AJAX section -->
    <script>
        //For dependent Selects
        $(document).ready(function()
        {
            $(".tipo_factura").change(function()
            {
                var tipo_factura = $(this).val();
                var nr_comprobante = document.getElementById("nr").value;
                var nr_comprobante_oficial = <?php echo json_encode($nr_factura)?>;
                $.ajax
                ({
                    type: "POST",
                    url: "../ajax/factura_venta_ajax.php",
                    data: {"tipo_factura":tipo_factura},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result data: " + resultado);
                        var data = resultado.split(",");
                        var tipo_factura_valor = data[0];
                        //echo 'Tipo:'.$tipo_factura_valor;
                        if (tipo_factura_valor == 'A')
                        {
                            $('#nr_factura').val(nr_comprobante_oficial);
                        }else{
                            $('#nr_factura').val(nr_comprobante);
                        }
                    } 
                });
            });
        });

        function EliminarDetalle(val1){
            var action = "EliminarDetalle";
            var nr_producto = val2;
            //alert("Data1: " + nr_producto);
            $.ajax({
                type: "POST",
                url: "../ajax/guardar_orden_venta_ajax.php",
                data: {"action":action,"nr_producto":nr_producto},
                dataType : 'json',
                success: function(resultado)
                {
                    //alert("Producto Eliminado de la orden");
                    //alert("Result: " + resultado);
                    GetTotalOrden();
                    ListaProductosOrden();
                    getCantidadDetalle();
                }
            });
        }

        //To show the list of items in Detalle
        function GetTotalOrden(){
            var orden_nr = document.getElementById("nr").value;
            $.ajax({
                type: "POST",
                url: "../ajax/orden_venta_ajax.php",
                data: {"orden_nr":orden_nr},
                dataType : 'json',
                success: function(resultado){
                    //alert("Result: " + resultado);
                    var data = resultado.split(",");
                    $('#detalle_total_general').val(data[0]);
                    $('#detalle_iva_general').val(data[1]);
                }
            });
        }

        //To show the list of items in Detalle
        function ListaProductosOrden(){
            var listar_orden = document.getElementById("nr").value;
            $.ajax({
                type: "POST",
                url: "../ajax/orden_venta_ajax.php",
                data: {"listar_orden":listar_orden},
                success: function(data){
                    //alert("Result data: " + data);
                    $("#detalle-list").show();
                    $("#detalle-list").html(data);
                    $("listar_orden").css("background","#FFF");
                }
            });
        }

        function onClick(e) {
            ListaProductosOrden();
            GetTotalOrden();
            getCantidadDetalle();
        }

        if (window.addEventListener) {
            document.addEventListener('click', onClick);
        }

        //To show the list of items in Detalle
        function getCantidadDetalle(){
            var orden = document.getElementById("nr").value;
            $.ajax({
                type: "POST",
                url: "../ajax/orden_venta_ajax.php",
                data: {"orden":orden},
                dataType : 'json',
                success: function(data){
                    //alert("Result data: " + data);
                    var data = data.split(",");
                    $('#cantidad_detalle').val(data[0]);
                }
            });
        }

        //Check if the Form has at least one detail before Confirm
        $('#factura_venta_form').submit(function() {
            getCantidadDetalle();
            var cantidad_detalle = document.getElementById("cantidad_detalle").value;
            //alert("Detalle: " + cantidad_detalle);
            if(cantidad_detalle < 1)
            {
                alert("La Factura debe tener por lo menos un producto.");
                return false;
            }else {
                return true;
            }
        });

    </script>

        <header>
		  <h1>Factura Venta</h1>	
        </header>
        <article>
        <!--Totales section -->
        <p hidden><input type="text" id="tipo_factura_valor" name="tipo_factura_valor" value="<?php echo $tipo_factura_valor;?>"></p>
        <p hidden><input type="text" id="total_exentas" name="total_exentas" value="<?php echo $total_exentas;?>"></p>
        <p hidden><input type="text" id="total_gravadas" name="total_gravadas" value="<?php echo $total_gravadas;?>"></p>
		<table class="cabecera">
			<tr>          
                <!--Column1-->
                <th><span>Factura #</span></th>
                <p hidden><input type="text" id="orden_venta" name="orden_venta" value="<?php echo $orden_venta;?>"></p>
                <td><span><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></span></td>
                <!--Column2-->
				<th><span>Usuario</span></th>
                <p hidden><input type="text" id="nr_user" name="nr_user" value="<?php echo $nr_user;?>"></p>
				<td><span><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly disabled></span></td>
			</tr>
			<tr>
                <!--Column1-->
                <th><span>Tipo Factura</span></th>
                <td><span>
                    <select id="tipo_factura" name="tipo_factura" required class="tipo_factura">
                        <option value="A" selected="selected">A</option>;
                        <option value="B">B</option>;
                    </select><p>
                </span></td>
                <!--Column2-->
                <th><span>Sucursal</span></th>
              	<td><span>
              		<select id="nr_sucursal" name="nr_sucursal" required class="boxes">
                        <option value="">Seleccione:</option>;
                        <?php 
                            $sucursal_result = $factura_venta->query("select * from sucursal order by descripcion");//We get all the results from the table
                            foreach ($sucursal_result as $row) {
                            $sucursal_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
			</tr>
			<tr>
                <!--Column1-->
                <th><span>Fecha</span></th>
                <td><span><input type="date" id="fecha_factura" name="fecha_factura" value="<?php echo date("Y-m-d");?>" class="boxes" required></span></td>
                <!--Column2-->
                <th><span>Deposito de stock</span></th>
              	<td><span>
              		<select name="nr_deposito" required class="boxes">
                        <?php 
                            $dato_sucursal = $factura_venta->query("select * from depositos_stock where nr_sucursal = '$nr_sucursal'");//We get all the results from the table
                            foreach ($dato_sucursal as $deposito_result) {
                            $deposito_nr = $deposito_result['nr'];    
                            echo '<option value="'.$deposito_result['nr'].'"';
                            if ($nr_deposito==$deposito_nr) echo 'selected="selected"';
                            echo '>'.$deposito_result['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
            </tr>
			<tr>
                <!--Column1-->
                <th><span>Nro. Oficial</span></th>
                <td><span><input type="text" id="nr_factura" name="nr_factura" value="<?php echo $nr_factura;?>" class="boxes" required></span></td>
                <!--Column2-->
				<th><span>Moneda</span></th>
                <td><span>
                	<select id="nr_moneda" name="nr_moneda" required class="boxes" onChange= "mostrarCotizacion(this.value)">
                        <?php 
                            $moneda_result = $factura_venta->query("select * from moneda order by descripcion");//We get all the results from the table
                            foreach ($moneda_result as $row) {
                            $moneda_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if (($nr_moneda==$moneda_nr) || ($moneda_defecto==$moneda_nr)) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                            //echo $nr_moneda;
                        }?>
                    </select><p>
                    <!--Only show this section if a Moneda <> Gs is selected-->
                    <script language="Javascript">
                        function mostrarCotizacion() 
                        {
                            element = document.getElementById("hideContent");
                            valor_moneda_defecto = <?php echo json_encode($moneda_defecto); ?>;
                            nr_moneda = document.getElementById("nr_moneda");
                            valor_moneda = nr_moneda.value;
                            //document.write(valor_moneda);
                            if (valor_moneda == valor_moneda_defecto)
                            //if (valor_moneda == '1')
                            {
                                element.style.display='none';
                            }else {
                                element.style.display='block';
                            }
                        }        
                    </script> 
                    <div id="hideContent" style="display: none;">
                        <?php
                        
                        ?>
                        <table>
                            <tr>
                                <!--Column1-->
                                <th><span>Cotizacion Compra</span></th>
                                <td><input type="number" id="cotizacion_compra" name="cotizacion_compra" value="<?php echo $cotizacion_compra;?>" class="boxes"></td>
                            </tr>
                            <tr>
                                <!--Column1-->
                                <th><span>Cotizacion Venta</span></th>
                                <td><input type="number" id="cotizacion_venta" name="cotizacion_venta" value="<?php echo $cotizacion_venta;?>" class="boxes"></td>
                            </tr>
                        </table>
                    </div>
                </span></td>
			</tr>
			<tr>
                <!--Column1-->
                <th><span>Cod. cliente</span></th>
                <p hidden><input type="text" id="nr_cliente" name="nr_cliente" value="<?php echo $nr_cliente;?>"></p>
                <td><span><input type="text" id="id_cliente" name="id_cliente" value="<?php echo $id_cliente;?>" onchange="getcliente();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required></span>
                <span><input type="text" id="descripcion_cliente" name="descripcion_cliente" value="<?php echo $descripcion_cliente;?>" readonly disabled></span></td>
                <!--Column2-->
                <th><span>Vendedor</span></th>
                <p><input type="hidden" id="nr_vendedor" name="nr_vendedor" value="<?php echo $nr_vendedor;?>" class="boxes" required></p>
                <td><span><input type="text" id="descripcion_vendedor" name="descripcion_vendedor" value="<?php echo $descripcion_vendedor;?>" class="boxes" required readonly disabled></span></td>
			</tr>
            <tr>
                <!--Column1-->
                <th><span>Cond. de Venta</span></th>
                <td><span>
                    <select id="nr_condicion" name="nr_condicion" required class="boxes">
                        <?php 
                            $condicion_result = $factura_venta->query("select * from condicion_compra_venta order by descripcion");//We get all the results from the table
                            foreach ($condicion_result as $row) {
                            $condicion_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_condicion==$condicion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
                <!--Column2-->
                <th><span>Fecha de Vto.</span></th>
                <?php 
                    if ($dias > 0)
                    {
                        $sumar_dias = "+ ". $dias ." days";
                        //echo $sumar_dias;
                        $fecha_vto = date('Y-m-d', strtotime($sumar_dias));
                    }else{
                        $fecha_vto = date('Y-m-d');
                    }
                    
                ?>
                <td><span><input type="date" id="fecha_vto" name="fecha_vto" value="<?php echo $fecha_vto;?>" class="boxes" required></span></td>
            </tr>
		</table>
           
            <?php
            echo '<table class="list">';
                echo '<thead>';
                echo '<tr>';
                    echo '<th>#</th>';
                    echo '<th>Codigo</th>';
                    echo '<th>Descripcion</th>';
                    echo '<th>Cant.</th>';
                    echo '<th>Unid.Med.</th>';
                    echo '<th>Precio Lista</th>';
                    echo '<th>I.</th>';
                    echo '<th>Desc. %</th>';
                    echo '<th>Precio Final</th>';
                    echo '<th>Total</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                //Check if more than 0 records were found
                /* Quantity of lines in Detalle section */
                $detalle_result_list = $factura_venta->query("select OV.nr, OV.nr_producto, P.id_producto, P.descripcion descripcion_producto, OV.cantidad, OV.facturado, OV.precio_lista, OV.precio_final, OV.descuento, 
                  OV.impuesto, OV.nr_unidad, UM.descripcion descripcion_unidad, OV.total_gravadas_linea, OV.total_exentas_linea, OV.total_iva_linea,OV.total_linea 
                  from detalle_orden_venta OV join productos P on OV.nr_producto = P.nr join unidad_medida UM on OV.nr_unidad = UM.nr where OV.nr = '$orden_venta'");//We get all the results from the associated OC
                @$qty_register = $factura_venta -> rowCount("select * from detalle_orden_venta where nr = '$orden_venta'");

                if($qty_register > 0){
                    foreach($detalle_result_list as $detalle):
                        @$i= $i + 1;
                        if ($nr_moneda==1)
                        {
                            $decimal = 0;
                        }else{
                            $decimal = 2;
                        }
                        $nr_producto = $detalle['nr_producto'];
                        $cantidad = number_format(floatval(($detalle['cantidad']-$detalle['facturado'])),$decimal,",",".");
                        $total_linea = number_format(floatval($detalle['precio_lista']),$decimal,",","") * $cantidad;
                        $detalleFacturaV = array('nr_producto'=>$nr_producto,'cantidad'=>$cantidad,'descuento'=>$descuento,'precio_lista'=>$precio_lista,
                            'precio_final'=>$precio_final,'total_gravadas_linea'=>$total_gravadas_linea,'total_exentas_linea'=>$total_exentas_linea,
                            'total_iva_linea'=>$total_iva_linea,'total_linea'=>$total_linea,'impuesto'=>$impuesto,
                            'nr_unidad'=>$nr_unidad);
                        /*foreach ( $detalleFacturaV as $key => $value ) {
                            echo '<tr>';
                            echo '<td>'. $i . '</td>';
                            echo '<td>'. $value . '</td>';
                            echo '<td width="2%"><a onclick="EliminarDetalle('.$detalle['nr_producto'].')"  title="Borrar" class = "delete"></a></td>';
                            echo '</tr>';
                        }*/

                        echo '<tr>';
                        echo '<td>'. $i . '</td>';
                        echo '<td>'. $detalle['id_producto'] . '</td>';
                        echo '<td>'. $detalle['descripcion_producto'] . '</td>';
                        echo '<td>'. $cantidad. '</td>';
                        echo '<td>'. $detalle['descripcion_unidad'] . '</td>';
                        echo '<td>'. number_format(floatval($detalle['precio_lista']),$decimal,",",".") . '</td>';
                        echo '<td>'. $detalle['impuesto'] . '</td>';
                        echo '<td>'. $detalle['descuento'] . '</td>';
                        echo '<td>'. number_format(floatval($detalle['precio_final']),$decimal,",",".") . '</td>';
                        echo '<td>'. $total_linea. '</td>';
                        echo '<td width="2%"><a onclick="EliminarDetalle('.$detalle['nr_producto'].')"  title="Borrar" class = "delete"></a></td>';
                        echo '</tr>';
                    endforeach;
                }else{
                        echo '<div class = "no_record">No se ingresaron articulos</div>';
                    }
                 echo '</tbody>';
            echo '</table>';
            ?>
			<table class="balance">
				<tr>
					<th><span balance>I.V.A.</span></th>
					<td><span detallerow-number><?php $id_moneda?></span><span><input type="number" id="total_iva" name="total_iva" value="<?php echo $total_iva;?>" readonly></span></td>
				</tr>
				<tr>
					<th><span balance>Total General</span></th>
					<td><span detallerow-number><?php $id_moneda?></span><span><input type="number" id="total_factura" name="total_factura" value="<?php echo $total_factura;?>" readonly></span></td>
				</tr>
			</table>
		</article>
		<aside>
			<div contenteditable>
			</div>
		</aside>
        <?echo "User: ".$nr_user;?>
        <div class="form-actions">
            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Confirmar">
            <a class="btn" href="factura_venta.php" onclick="window.close()";>Cancelar</a>
        </div>
    </form>
	</body>
</html>

<?php
    if (isset($_POST['action']))
    {
        //Get the Credito disponible
        $credito_result = $factura_venta->query("select C.nr, C.id_cliente, C.razon_social, CC.limite_credito, CC.saldo, CC.credito_disponible from clientes C left join cuenta_cliente CC on CC.nr_cliente = C.nr where C.nr ='$nr_cliente'");//We get the desired result from the table
        foreach ($credito_result as $resultado_credito) {        
            @$limite_credito = $resultado_credito['credito_disponible'];
        }
    
        switch($_POST['action'])
        {
            case "Confirmar": 
                $nr = $_POST['nr'];
                //echo "<script>alert ('NR: ".$nr."')</script>";
                $nr_cliente = $_POST['nr_cliente'];
                //echo "<script>alert ('Cliente: ".$nr_cliente."')</script>";
                $fecha_factura = $_POST['fecha_factura'];
                //echo "<script>alert ('Result: ".$fecha_factura."')</script>";
                $nr_condicion = $_POST['nr_condicion'];
                //echo "<script>alert ('Result: ".$nr_condicion."')</script>";
                $nr_sucursal = $_POST['nr_sucursal'];
                //echo "<script>alert ('Result: ".$nr_sucursal."')</script>";
                $nr_deposito = $_POST['nr_deposito'];
                //echo "<script>alert ('Result: ".$nr_deposito."')</script>";
                $nr_moneda = $_POST['nr_moneda'];
                //echo "<script>alert ('Result: ".$nr_moneda."')</script>";
                $fecha_vto = $_POST['fecha_vto'];
                //echo "<script>alert ('Result: ".$fecha_vto."')</script>";
                $nr_user = $_POST['nr_user'];
                //echo "<script>alert ('Result: ".$nr_user."')</script>";
                $cotizacion_compra = $_POST['cotizacion_compra'];
                //echo "<script>alert ('Result: ".$cotizacion_compra."')</script>";
                $cotizacion_venta = $_POST['cotizacion_venta'];
                //echo "<script>alert ('Result: ".$cotizacion_venta."')</script>";
                $total_exentas=$_POST['total_exentas'];
                //echo "<script>alert ('Result: ".$total_exentas."')</script>";
                $total_gravadas=$_POST['total_gravadas'];
                //echo "<script>alert ('Result: ".$total_gravadas."')</script>";
                $total_iva=$_POST['total_iva'];
                //echo "<script>alert ('Result: ".$total_iva."')</script>";
                $total_factura=$_POST['total_factura'];
                //echo "<script>alert ('Result: ".$total_factura."')</script>";
                $orden_venta=$_POST['orden_venta'];
                //echo "<script>alert ('Result: ".$orden_venta."')</script>";
                $tipo_factura=$_POST['tipo_factura'];
                //echo "<script>alert ('Result: ".$tipo_factura."')</script>";
                $nr_factura = $_POST['nr_factura'];
                //echo "<script>alert ('Result: ".$nr_factura."')</script>";
                $saldo_factura=$total_factura;
                //echo "<script>alert ('Result: ".$total_factura."')</script>";
                $estado=0;
                $total_costo=0;

                //@$limite_credito = $_POST['limite_credito'];

                if (($total_factura < $limite_credito) || ($dias == 0))
                {
                    $factura_venta->insert_cabecera_factura_venta($nr,$nr_factura,$nr_cliente,$nr_vendedor,$fecha_factura,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_vto,$total_exentas,$total_gravadas,$total_iva,$total_factura,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_venta,$saldo_factura,$estado,$tipo_factura,$total_costo);

                    $detalle_insert_result = $factura_venta->query("select * from detalle_orden_venta DOV join costos_productos CP on DOV.nr_producto = CP.nr_producto where DOV.nr = '$orden_venta'");//We get all the results from the associated OC
                    foreach($detalle_insert_result as $total_row):
                        //$nr = $_POST['nr'];
                        $nr_producto= $total_row['nr_producto'];
                        //echo $nr_producto;
                        $cantidad= $total_row['cantidad'];
                        //echo $cantidad;
                        $descuento= $total_row['descuento'];
                        //echo $descuento;
                        $precio_lista=$total_row['precio_lista'];
                        //echo $precio_lista;
                        $precio_final=$total_row['precio_final'];
                        //echo $precio_final;
                        $costo=$total_row['cpp'];
                        //echo $costo;
                        $nr_unidad= $total_row['nr_unidad'];
                        //echo $nr_unidad;
                        $impuesto= $total_row['impuesto'];
                        //echo $impuesto;
                        $total_gravadas_linea= $total_row['total_gravadas_linea'];
                        //echo $total_gravadas_linea;
                        $total_exentas_linea= $total_row['total_exentas_linea'];
                        //echo $total_exentas_linea;
                        $total_iva_linea= $total_row['total_iva_linea'];
                        //echo $total_iva_linea;
                        $total_linea= $total_row['total_linea'];
                        //echo $total_linea;
                        $total_costo=$total_costo+($costo*$cantidad);

                        $factura_venta->insert_detalle_factura_venta($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$costo,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
                    endforeach;
                    //Update Costo total
                    $factura_venta->query("update cabecera_factura_venta set total_costo = '$total_costo' where nr = '$nr'");
                    //Update the last Factura Venta number
                    if ($tipo_factura == 'A')
                    {
                        $nr_factura_venta = substr($nr_factura, 9, 7);
                        $factura_venta->query("update comprobantes set ultimo_nro_impreso = '$nr_factura_venta' where nr_sucursal = '$nr_sucursal' and aplicado_a='factura_venta'");
                    }
                }else{
                    echo "<script>alert('El cliente no posee credito suficiente para confirmar la compra.')</script>";
                    echo "<script>window.close()</script>";
                }

                //Update tabla 'Cuenta Cliente'
                //$factura_venta->insert_detalle_factura_venta($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
            break;
        }
    }
?>