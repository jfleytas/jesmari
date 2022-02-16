<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
	require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/factura_compra.class.php';
    $factura_compra = factura_compra::singleton();

    $page_name="factura_compra_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $orden_compra=isset($_POST['orden_compra']) ? $_POST['orden_compra'] : '';
    $nr_factura=isset($_POST['nr_factura']) ? $_POST['nr_factura'] : '';
    $fecha_factura=isset($_POST['fecha_factura']) ? $_POST['fecha_factura'] : '';
    $fecha_vto=isset($_POST['fecha_vto']) ? $_POST['fecha_vto'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_proveedor=isset($_POST['nr_proveedor']) ? $_POST['nr_proveedor'] : '';
    $id_proveedor=isset($_POST['id_proveedor']) ? $_POST['id_proveedor'] : '';
    $descripcion_proveedor=isset($_POST['descripcion_proveedor']) ? $_POST['descripcion_proveedor'] : '';
    $nr_condicion=isset($_POST['nr_condicion']) ? $_POST['nr_condicion'] : '';
    $descripcion_condicion=isset($_POST['descripcion_condicion']) ? $_POST['descripcion_condicion'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $parametro_sucursal=isset($_POST['parametro_sucursal']) ? $_POST['parametro_sucursal'] : '';
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
    $impuesto=isset($_POST['impuesto']) ? $_POST['impuesto'] : '';
    $descuento=isset($_POST['descuento']) ? $_POST['descuento'] : '';
    $total_exentas=isset($_POST['total_exentas']) ? $_POST['total_exentas'] : 0;
    $total_gravadas=isset($_POST['total_gravadas']) ? $_POST['total_gravadas'] : 0;
    $total_iva=isset($_POST['total_iva']) ? $_POST['total_iva'] : 0;
    $total_exentas_linea=isset($_POST['total_exentas_linea']) ? $_POST['total_exentas_linea'] : 0;
    $total_gravadas_linea=isset($_POST['total_gravadas_linea']) ? $_POST['total_gravadas_linea'] : 0;
    $total_iva_linea=isset($_POST['total_iva_linea']) ? $_POST['total_iva_linea'] : 0;
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : '';
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : '';
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : '';
    $total_factura=isset($_POST['total_factura']) ? $_POST['total_factura'] : '';
    $saldo_factura=isset($_POST['saldo_factura']) ? $_POST['saldo_factura'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : 1;
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : 1;
    $dias=isset($_POST['dias']) ? $_POST['dias'] : '';
    $obs=isset($_POST['obs']) ? $_POST['obs'] : '';
    $estado=isset($_POST['estado']) ? $_POST['estado'] : 0;

    //Defining Order # checking the next value from the sequence
    $factura_result = $factura_compra->query("select nextval('cabecera_factura_compra_nr_seq')");
    //$factura_result = $factura_compra->query("select last_value from cabecera_factura_compra_nr_seq");
    foreach ($factura_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the configuration for Compras forms
    $configuracion_result = $factura_compra->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $factura_compra->query("select * from users where id_user ='$usuario'");//We get the desired result from the table
    foreach ($user_result as $resultado_user) {        
        $nr_user = $resultado_user['nr'];
        $id_user = $resultado_user['id_user'];
        $nombre_apellido = $resultado_user['nombre_apellido'];
    }

    //Get the Orden de Compra
    if(isset($_GET['orden']))
    {
        //Verify if the Orden has a Factura
        $orden_compra = $_GET['orden'];

        
        /* Lista Cabecera section */
        $cabecera_result = $factura_compra->query("select * from cabecera_orden_compra where nr = '$orden_compra'");//We get all the results from the associated OC
        /* Lista Detalle section */
        $detalle_result = $factura_compra->query("select * from detalle_orden_compra where nr = '$orden_compra'");//We get all the results from the associated OC
        /* Quantity of lines in Detalle section */
        $detalle_result_list = $factura_compra->query("select OC.nr, OC.nr_producto, P.id_producto, P.descripcion descripcion_producto, OC.cantidad, OC.precio_lista, OC.precio_final, OC.descuento, OC.impuesto, OC.nr_unidad, UM.descripcion descripcion_unidad, 
            OC.total_gravadas_linea, OC.total_exentas_linea, OC.total_iva_linea, OC.total_linea from detalle_orden_compra OC join productos P on OC.nr_producto = P.nr join unidad_medida UM on OC.nr_unidad = UM.nr where OC.nr = '$orden_compra'");//We get all the results from the associated OC
        @$qty_register = $factura_compra -> rowCount("select * from detalle_orden_compra where nr = '$orden_compra'");

        //Get the values from Cabecera
        foreach($cabecera_result as $total_row):
            $nr_proveedor= $total_row['nr_proveedor'];
            $nr_condicion= $total_row['nr_condicion'];
            $nr_sucursal= $total_row['nr_sucursal'];
            $nr_deposito= $total_row['nr_deposito'];
            $nr_moneda= $total_row['nr_moneda'];
            $cotizacion_compra= $total_row['cotizacion_compra'];
            $cotizacion_venta= $total_row['cotizacion_venta'];
            $total_exentas= $total_row['total_exentas'];
            $total_gravadas= $total_row['total_gravadas'];
            $total_iva= $total_row['total_iva'];
            $total_factura= $total_row['total_orden'];
            $orden_compra=$total_row['nr'];
        endforeach;

        //Get the values from Detalle
        foreach($detalle_result as $total_row):
            $nr_producto= $total_row['nr_producto'];
            $cantidad= $total_row['cantidad'];
            $precio_lista=$total_row['precio_lista'];
            $descuento= $total_row['descuento'];
            $precio_final=$total_row['precio_final'];
            $nr_unidad= $total_row['nr_unidad'];
            $impuesto= $total_row['impuesto'];
            $total_gravadas_linea= $total_row['total_gravadas_linea'];
            $total_exentas_linea= $total_row['total_exentas_linea'];
            $total_iva_linea= $total_row['total_iva_linea'];
            $total_linea= $total_row['total_linea'];
        endforeach;

        //Get the Proveedor code and descripcion
        $proveedor_result = $factura_compra->query("select * from proveedor where nr = '$nr_proveedor'");//We get all the results from the table
        foreach($proveedor_result as $total_row):
            $id_proveedor= $total_row['id_proveedor'];
            $descripcion_proveedor= $total_row['descripcion'];
        endforeach;

        //Get the Producto code and descripcion
        $producto_result = $factura_compra->query("select * from productos where nr = '$nr_producto'");//We get all the results from the table
        foreach($producto_result as $total_row):
            $id_producto= $total_row['id_producto'];
            $descripcion_producto= $total_row['descripcion'];
        endforeach;

        //Get Condicion Compra Venta
        $condicion_result = $factura_compra->query("select * from condicion_compra_venta where nr = '$nr_condicion'");//We get all the results from the table
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
    <form class="form-horizontal" action="factura_compra_form.php" method="POST" name = "factura_compra_form" autocomplete = "off">
        <header>
		  <h1>Factura Compra</h1>	
        </header>
        <article>
        <!--Totales section -->
        <p hidden><input type="text" id="total_exentas" name="total_exentas" value="<?php echo $total_exentas;?>"></p>
        <p hidden><input type="text" id="total_gravadas" name="total_gravadas" value="<?php echo $total_gravadas;?>"></p>
		<table class="cabecera">
			<tr>          
                <!--Column1-->
				<th><span>Factura #</span></th>
                <p hidden><input type="text" id="orden_compra" name="orden_compra" value="<?php echo $orden_compra;?>"></p>
				<td><span><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></span></td>
                <!--Column2-->
				<th><span>Usuario</span></th>
                <p hidden><input type="text" id="nr_user" name="nr_user" value="<?php echo $nr_user;?>"></p>
				<td><span><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly></span></td>
			</tr>
			<tr>
                <!--Column1-->
				<th><span>Nro. Oficial</span></th>
                <td><span><input type="text" id="nr_factura" name="nr_factura" value="<?php echo $nr_factura;?>" class="boxes" required></span></td>
                <!--Column2-->
                <th><span>Sucursal</span></th>
              	<td><span>
              		<select id="nr_sucursal" name="nr_sucursal" required class="boxes">
                        <option value="">Seleccione:</option>';
                        <?php 
                            $sucursal_result = $factura_compra->query("select * from sucursal order by descripcion");//We get all the results from the table
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
                            $dato_sucursal = $factura_compra->query("select * from depositos_stock where nr_sucursal = '$nr_sucursal'");//We get all the results from the table
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
                <th><span>Cod. Proveedor</span></th>
                <p hidden><input type="text" id="nr_proveedor" name="nr_proveedor" value="<?php echo $nr_proveedor;?>"></p>
                <td><span><input type="text" id="id_proveedor" name="id_proveedor" value="<?php echo $id_proveedor;?>" onchange="getProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required></span>
                <span><input type="text" id="descripcion_proveedor" name="descripcion_proveedor" value="<?php echo $descripcion_proveedor;?>" readonly disabled></span></td>
                <!--Column2-->
				<th><span>Moneda</span></th>
                <td><span>
                	<select id="nr_moneda" name="nr_moneda" required class="boxes" onChange= "mostrarCotizacion(this.value)">
                        <?php 
                            $moneda_result = $factura_compra->query("select * from moneda order by descripcion");//We get all the results from the table
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
                <th><span>Cond. de Compra</span></th>
                <td><span>
                    <select id="nr_condicion" name="nr_condicion" required class="boxes">
                        <?php 
                            $condicion_result = $factura_compra->query("select * from condicion_compra_venta order by descripcion");//We get all the results from the table
                            foreach ($condicion_result as $row) {
                            $condicion_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_condicion==$condicion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
                <!--Column2-->
                <th><span>Fecha de vto.</span></th>
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
            <tr>
                <!--Column1-->
                <th>Obs.</th>
                <td><input type="text" id="obs" name="obs" value="<?php echo $obs ;?>" class="boxes"></td>                
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
                $detalle_result_list = $factura_compra->query("select OC.nr, OC.nr_producto, P.id_producto, P.descripcion descripcion_producto, OC.cantidad, OC.precio_lista, OC.precio_final, OC.descuento, OC.impuesto, OC.nr_unidad, UM.descripcion descripcion_unidad, OC.total_gravadas_linea, OC.total_exentas_linea, OC.total_iva_linea, OC.total_linea from detalle_orden_compra OC join productos P on OC.nr_producto = P.nr join unidad_medida UM on OC.nr_unidad = UM.nr where OC.nr = '$orden_compra'");//We get all the results from the associated OC
                @$qty_register = $factura_compra -> rowCount("select * from detalle_orden_compra where nr = '$orden_compra'");

                if($qty_register > 0){
                    foreach($detalle_result_list as $detalle):
                        @$i= $i + 1;
                        if ($nr_moneda==1)
                        {
                            $decimal = 0;
                        }else{
                            $decimal = 2;
                        }
                        echo '<tr>';
                        echo '<td>'. $i . '</td>';
                        echo '<td>'. $detalle['id_producto'] . '</td>';
                        echo '<td>'. $detalle['descripcion_producto'] . '</td>';
                        echo '<td>'. number_format(floatval($detalle['cantidad']),$decimal,",","."). '</td>';
                        echo '<td>'. $detalle['descripcion_unidad'] . '</td>';
                        echo '<td>'. number_format(floatval($detalle['precio_lista']),$decimal,",",".") . '</td>';
                        echo '<td>'. $detalle['impuesto'] . '</td>';
                        echo '<td>'. $detalle['descuento'] . '</td>';
                        echo '<td>'. number_format(floatval($detalle['precio_final']),$decimal,",",".") . '</td>';
                        echo '<td>'. number_format(floatval($detalle['total_linea']),$decimal,",",".") . '</td>';
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
            <a class="btn" href="factura_compra.php" onclick="window.close()";>Cancelar</a>
        </div>
    </form>
	</body>
</html>

<?php
    if (isset($_POST['action']))
    {
        switch($_POST['action'])
        {
            case "Confirmar": 
                $nr = $_POST['nr'];
                $nr_factura = $_POST['nr_factura'];
                $nr_proveedor = $_POST['nr_proveedor'];
                $fecha_factura = $_POST['fecha_factura'];
                $nr_condicion = $_POST['nr_condicion'];
                $nr_sucursal = $_POST['nr_sucursal'];
                $nr_deposito = $_POST['nr_deposito'];
                $nr_moneda = $_POST['nr_moneda'];
                $fecha_vto = $_POST['fecha_vto'];
                $nr_user = $_POST['nr_user'];
                $cotizacion_compra = $_POST['cotizacion_compra'];
                $cotizacion_venta = $_POST['cotizacion_venta'];
                $total_exentas=$_POST['total_exentas'];
                $total_gravadas=$_POST['total_gravadas'];
                $total_iva=$_POST['total_iva'];
                $total_factura=$_POST['total_factura'];
                $orden_compra=$_POST['orden_compra'];
                $obs=$_POST['obs'];
                $estado=0;
                $saldo_factura=$total_factura;

                $factura_compra->insert_cabecera_factura_compra($nr,$nr_factura,$nr_proveedor,$fecha_factura,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_vto,$total_exentas,$total_gravadas,$total_iva,$total_factura,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_compra,$obs,$estado,$saldo_factura);

                $detalle_insert_result = $factura_compra->query("select * from detalle_orden_compra where nr = '$orden_compra'");//We get all the results from the associated OC
                foreach($detalle_insert_result as $total_row):
                    //$nr = $_POST['nr'];
                    $nr_producto= $total_row['nr_producto'];
                    $cantidad= $total_row['cantidad'];
                    $descuento= $total_row['descuento'];
                    $precio_lista=$total_row['precio_lista'];
                    $precio_final=$total_row['precio_final'];
                    $nr_unidad= $total_row['nr_unidad'];
                    $impuesto= $total_row['impuesto'];
                    $total_gravadas_linea= $total_row['total_gravadas_linea'];
                    $total_exentas_linea= $total_row['total_exentas_linea'];
                    $total_iva_linea= $total_row['total_iva_linea'];
                    $total_linea= $total_row['total_linea'];

                    $factura_compra->insert_detalle_factura_compra($nr,$nr_producto,$cantidad,$precio_lista,$descuento,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad);
                endforeach;
            break;
        }
    }
?>