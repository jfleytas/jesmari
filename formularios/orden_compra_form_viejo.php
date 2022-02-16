<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
	require '../css/nombre_empresa.html';

	require '../clases/formularios/orden_compra.class.php';
    $orden_compra = orden_compra::singleton();

    $page_name="orden_compra_form.php"; 

	//Declaring variables
    //$nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $fecha_orden=isset($_POST['fecha_orden']) ? $_POST['fecha_orden'] : '';
    $fecha_entrega=isset($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_proveedor=isset($_POST['nr_proveedor']) ? $_POST['nr_proveedor'] : '';
    $id_proveedor=isset($_POST['id_proveedor']) ? $_POST['id_proveedor'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $descripcion_proveedor=isset($_POST['descripcion_proveedor']) ? $_POST['descripcion_proveedor'] : '';
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
    $precio_producto=isset($_POST['precio_producto']) ? $_POST['precio_producto'] : '';
    $impuesto=isset($_POST['impuesto']) ? $_POST['impuesto'] : '';
    $descuento=isset($_POST['descuento']) ? $_POST['descuento'] : '';
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : '';
    $total_iva=isset($_POST['total_iva']) ? $_POST['total_iva'] : '';
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : '';
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : '';
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : '';

    //Defining Order # checking the next value from the sequence
    //$order_result = pg_query("select nextval('cabecera_orden_compra_nr_seq')")or die("Can't execute 4th query");//We get the desired result from the table
    $order_result = $orden_compra->query("select last_value from cabecera_orden_compra_nr_seq");
    foreach ($order_result as $resultado) {        
        @$nr_1 = $resultado['last_value'];
    }
    if (isset($nr))
    {
    echo "<script>alert ('Valor')".$nr."</script>";    
}else{
    echo "<script>alert ('No funca')</script>";
}
    
    //$nr = $nr + 1;
    if ($order_result != false) {
        $row = pg_fetch_row($order_result);
        echo "id is " . $row[0] . "<br />";
        }
        else {
         echo ("No")
        }

    //echo ($order_result);

    //Get the configuration for Compras forms
    $configuracion_result = $orden_compra->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];

    /* Javascript result section */
    //Result from Sucursal
    if(isset($_GET['sucursal']))
    {
        $sucursal = $_GET['sucursal'];
        $nr_sucursal = $sucursal;
        $dato_sucursal = $orden_compra->get_deposito_stock($sucursal);//We get all the results from the table
    }

    if(isset($_POST['nr_sucursal']))
    {
        echo 'alert("SIIII")';
        $sucursal = $_POST['nr_sucursal'];
        $nr_sucursal = $sucursal;
        $dato_sucursal = $orden_compra->get_deposito_stock($sucursal);//We get all the results from the table
    }

    @$i = 1;
?>

<!-- Javascript section -->
<script type="text/javascript">
    function getProveedor1(){
        var opcion = document.getElementById('id_proveedor').value;
        window.location.href = 'orden_compra_form.php?proveedor=' +opcion;
    }

    function getDepositos1(){
        var opcion = document.getElementById('nr_sucursal').value;
        var myURL = document.location;
        document.location = myURL + "&sucursal="+opcion;
        //window.location.href = 'orden_compra_form.php?sucursal=' +opcion;
    }

    function getProducto1(){
        var opcion = document.getElementById('id_producto').value;
        var myURL = document.location;
        document.location = myURL + "&producto="+opcion;
        //window.location.href = 'orden_compra_form.php?producto=' +opcion;
    }
</script> 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="../css/estilo_formulario.css" />
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
        <script src="../js/jquery.js"></script>
		<script src="../js/formulario_script.js"></script> 
	</head>
	<body>
        <!-- AJAX section -->
        <script>
            /*function prueba(value){
            //$("#action").click(function(){
                $.post("../ajax/orden_compra_ajax.php",
                {
                    id_producto: value,
                    descripcion: value
                },
                function(productodata, status){
                    //alert("Data: " + productodata + "\nStatus: " + status);
                    $("#descripcion_producto").val(productodata);
                });
            //});
            }*/
            function getProveedor(){
                var proveedor = document.getElementById("id_proveedor").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: "proveedor="+proveedor,
                    success: function(resultado)
                    {
                        //alert("Data: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_proveedor').val(data[0]);
                        $('#descripcion_proveedor').val(data[1]);
                        $("#nr_condicion").val(data[2]);
                    }
                });
            }

            function getProducto(){
                var producto = document.getElementById("id_producto").value;
                var nr_proveedor = document.getElementById("nr_proveedor").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: "producto="+producto + "&nr_proveedor="+nr_proveedor,
                    success: function(resultado)
                    {
                        //alert("Data: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_producto').val(data[0]);
                        $('#descripcion_producto').val(data[1]);
                        $("#impuesto").val(data[2]);
                        $("#id_unidad").val(data[3]);
                        $("#precio_producto").val(data[4]);
                        //$("#nr_proveedor").val(data[5]);
                    }
                });
            }

            function getDepositos(){
                var sucursal = document.getElementById("nr_sucursal").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: "sucursal="+sucursal,
                    success: function(resultado)
                    {
                        alert("Data: " + resultado);
                        $("#nr_deposito").val(resultado);
                    }
                });
            }

            /*$.post('test.php', { album: this.title }, function() {
                content.html(response);
            });*/

            /*$(document).ready(function() {
                $("#nr_sucursal").change(function(){
                    $.ajax({
                        type: 'POST',
                        data:  {nr_deposito:$('#nr_sucursal option:selected').val()};
                        alert($('#nr_sucursal option:selected').val());
                    });
                });
            });*/

            $(document).on('change',"select#nr_sucursal",function(){
                alert($('#nr_sucursal option:selected').val());
                $.ajax({
                    type: 'POST',
                    url: "../formularios/orden_compra_form.php",
                    data:  {nr_sucursal:$('#nr_sucursal option:selected').val()}
                });
            });

            /*function getProveedor(value){
                $.post("../ajax/orden_compra_ajax.php",
                {
                    id_proveedor: value
                },
                function(proveedordata, status){
                    alert("Data: " + proveedordata + "\nStatus: " + status);
                    $("#descripcion_proveedor").val(proveedordata);
                });
                //alert("Data: " + value);
            }*/
        </script>
        <header>
		  <h1>Orden de Compra</h1>	
        </header>
        <article>
		<table class="cabecera">
			<tr>          
                <!--Column1-->
				<th><span>Orden #</span></th>
				<td><span><input type="number" id="nr" name="nr" value="" readonly></span></td>
                <!--Column2-->
				<th><span>Usuario</span></th>
				<td><span><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly></span></td>
			</tr>
			<tr>
                <!--Column1-->
				<th><span>Fecha</span></th>
                <td><span><input type="date" id="fecha_orden" name="fecha_orden" value="<?php echo date("Y-m-d");?>" class="boxes" required></span></td>
                    <!--Column2-->
                <th><span>Sucursal</span></th>
              	<td><span>
              		<select id="nr_sucursal" name="nr_sucursal" required class="boxes">
                        <option value="">Seleccione:</option>';
                        <?php 
                            $sucursal_result = $orden_compra->get_sucursal();//We get all the results from the table
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
                <th><span>Cod. Proveedor</span></th>
                <p hidden><input type="text" id="nr_proveedor" name="nr_proveedor" value="<?php echo $nr_proveedor;?>"></p>
                <td><span><input type="text" id="id_proveedor" name="id_proveedor" value="<?php echo $id_proveedor;?>" onchange="getProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required></span>
                <span><input type="text" id="descripcion_proveedor" name="descripcion_proveedor" value="<?php echo $descripcion_proveedor;?>" readonly disabled></span></td>
                <!--Column2-->
                <th><span>Deposito de stock</span></th>
              	<td><span>
              		<select name="nr_deposito" required class="boxes">
                        <?php 
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
				<th><span>Cond. de Compra</span></th>
              	<td><span>
              		<select name="nr_condicion" required class="boxes">
                        <option value="">Seleccione:</option>';
                        <?php 
                            $condicion_result = $orden_compra->get_condicion_compra_venta();//We get all the results from the table
                            foreach ($condicion_result as $row) {
                            $condicion_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_condicion==$condicion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
                <!--Column2-->
				<th><span>Moneda</span></th>
                <td><span>
                	<select id="nr_moneda" name="nr_moneda" required class="boxes" onChange= "mostrarCotizacion(this.value)">
                        <?php 
                            $moneda_result = $orden_compra->get_monedas();//We get all the results from the table
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
                </span></td>
			</tr>
			<tr>
                <!--Column1-->
				<!--<th><span>Direccion</span></th>
				<td><input type="text" id="direccion" name="direccion" value="<?php echo $direccion ;?>" class="boxes" readonly></td>-->
                <!--Column2-->
                <th><span>Fecha de Entrega</span></th>
                <td><span><input type="date" id="fecha_entrega" name="fecha_entrega" value="<?php echo date("Y-m-d");?>" class="boxes" required></span></td>
			</tr>
		</table>
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

		<table class="detalle">
			<thead>
				<tr>
					<th><span detallerow>#</span></th>
					<th><span detallerow>Codigo</span></th>
					<th><span detallerow>Descripcion</span></th>
					<th><span detallerow>Cant.</span></th>
                    <th><span detallerow>Unid.Med.</span></th>
					<th><span detallerow>Precio</span></th>
					<th><span detallerow>I.</span></th>
					<th><span detallerow>Desc. %</span></th>
					<th><span detallerow>Total</span></th>
				</tr>
			</thead>
			<tbody class = "detallerow">
				<tr>
				    <td><span detallerow>1</span></td>
					<td><span detallerow><input type="text" id="id_producto" name="id_producto" value="<?php echo $id_producto;?>" onchange="getProducto()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"></span></td>
					<td><span detallerow><input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>" readonly disabled></span></td>
					<td><span class="detallerow-number"><input type="number" id="cantidad" name="cantidad" value="<?php echo $cantidad;?>"></span></td>
                    <td><span detallerow><input type="text" id="id_unidad" name="id_unidad" value="<?php echo $id_unidad;?>" readonly disabled></span></td>
					<td><span class="detallerow-number"><input type="number" id="precio_producto" name="precio_producto" value="<?php echo $precio_producto;?>"></span></td>
					<td><span class="detallerow-number"><input type="number" id="impuesto" name="impuesto" value="<?php echo $impuesto;?>"></span></td>
					<td><span class="detallerow-number"><input type="number" id="descuento" name="descuento" value="<?php echo $descuento;?>"></span></td>
					<td><span class="detallerow-number"><input type="number" id="total_linea" name="total_linea" value="<?php echo $total_linea;?>" readonly></span></td>
				</tr>
			</tbody>
			</table>
            <!--<input type="button" value="+" id="add" class="add_detail">-->
            <a class="add_detail">+</a>
            <?php
                /*if ($i <= $cantidad_f_compra) {
                    echo '<!-- Is commented because the max amount of lines is defined in the configuration -->';
                    echo '<a class="add_detail" onclick="AgregarDetalle()">+</a>';
                }*/
            ?>
			<table class="balance">
				<tr>
					<th><span balance>Sub-Total</span></th>
					<td><span detallerow-number><?php $nr_moneda?></span><span><input type="number" id="sub_total" name="sub_total" value="<?php echo $sub_total;?>" readonly></span></td>
				</tr>
				<tr>
					<th><span balance>I.V.A.</span></th>
					<td><span detallerow-number><?php $nr_moneda?></span><span><input type="number" id="total_iva" name="total_iva" value="<?php echo $total_iva;?>" readonly></span></td>
				</tr>
				<tr>
					<th><span balance>Total General</span></th>
					<td><span detallerow-number><?php $nr_moneda?></span><span><input type="number" id="total_general" name="total_general" value="<?php echo $total_general;?>" readonly></span></td>
				</tr>
			</table>
		</article>
		<aside>
			<div contenteditable>
				<p>Obs.:</p>
				<p>No valido como Comprobante de Compra</p>
			</div>
		</aside>
        <div class="form-actions">
            <p><p><input type="submit" class="btn-success" id="guardar" name="guardar" value ="Guardar">
            <a class="btn" href="orden_compra.php" onclick="window.close()";>Cancelar</a>
        </div>
	</body>
</html>

<?php
if(isset($_POST['guardar']))  
    {  
        $name=$_POST['name'];  
        $location=$_POST['location'];  
        pg_query("insert into cabecera_orden_compra(name,location) VALUES ('$name','$location')");  
        //$id=pg_insert_id();  
        for($i = 0; $i<count($_POST['productname']); $i++)  
        {  
            pg_query("INSERT INTO detalle_orden_compra  
            SET   
            order_id = '{$id}',  
            product_name = '{$_POST['productname'][$i]}',  
            quantity = '{$_POST['quantity'][$i]}',  
            price = '{$_POST['price'][$i]}',  
            discount = '{$_POST['discount'][$i]}',  
            amount = '{$_POST['amount'][$i]}'");   
        }  
    }   



?>