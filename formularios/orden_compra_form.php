<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
	require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/orden_compra.class.php';
    $orden_compra = orden_compra::singleton();

    $page_name="orden_compra_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $fecha_orden=isset($_POST['fecha_orden']) ? $_POST['fecha_orden'] : '';
    $fecha_entrega=isset($_POST['fecha_entrega']) ? $_POST['fecha_entrega'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_proveedor=isset($_POST['nr_proveedor']) ? $_POST['nr_proveedor'] : '';
    $id_proveedor=isset($_POST['id_proveedor']) ? $_POST['id_proveedor'] : '';
    $descripcion_proveedor=isset($_POST['descripcion_proveedor']) ? $_POST['descripcion_proveedor'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
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
    $precio_lista=isset($_POST['precio_lista']) ? $_POST['precio_lista'] : 0;
    $precio_final=isset($_POST['precio_final']) ? $_POST['precio_final'] : 0;
    $impuesto=isset($_POST['impuesto']) ? $_POST['impuesto'] : 0;
    $descuento=isset($_POST['descuento']) ? $_POST['descuento'] : 0;
    $total_exentas_linea=isset($_POST['total_exentas_linea']) ? $_POST['total_exentas_linea'] : 0;
    $total_gravadas_linea=isset($_POST['total_gravadas_linea']) ? $_POST['total_gravadas_linea'] : 0;
    $total_iva_linea=isset($_POST['total_iva_linea']) ? $_POST['total_iva_linea'] : 0;
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : '';
    $total_exentas=isset($_POST['total_exentas']) ? $_POST['total_exentas'] : 0;
    $total_gravadas=isset($_POST['total_gravadas']) ? $_POST['total_gravadas'] : 0;
    $total_iva=isset($_POST['total_iva']) ? $_POST['total_iva'] : 0;
    $total_orden=isset($_POST['total_orden']) ? $_POST['total_orden'] : 0;
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : 0;
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : 0;
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : 1;
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : 1;
    $obs=isset($_POST['obs']) ? $_POST['obs'] : '';
    $cantidad_detalle=isset($_POST['cantidad_detalle']) ? $_POST['cantidad_detalle'] : 0;
    $detalle_total_general=isset($_POST['detalle_total_general']) ? $_POST['detalle_total_general'] : 0;
    $detalle_iva_general=isset($_POST['detalle_iva_general']) ? $_POST['detalle_iva_general'] : 0;

    //Defining Order # checking the next value from the sequence
    $order_result = $orden_compra->query("select nextval('cabecera_orden_compra_nr_seq')");
    //$order_result = $orden_compra->query("select last_value from cabecera_orden_compra_nr_seq");
    foreach ($order_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the configuration for Compras forms
    $configuracion_result = $orden_compra->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $orden_compra->query("select U.nr nr_user, U.id_user, U.nombre_apellido, P.nr nr_personal, P.id_personal, P.nombre_apellido personal_nombre, 
    P.nr_sucursal from users U left join personal P on U.nr = P.nr_user where U.id_user = '$usuario'");//We get the desired result from the table
    foreach ($user_result as $resultado_user) {        
        $nr_user = $resultado_user['nr_user'];
        $id_user = $resultado_user['id_user'];
        $nombre_apellido = $resultado_user['nombre_apellido'];
        //$nr_sucursal = $resultado_user['nr_sucursal'];
        //echo '<script>getDepositos()</script>';
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="../css/estilo_formulario.css" />
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
        <script src="../js/jquery.js"></script>
		<script src="../js/orden_compra_script.js"></script> 
	</head>
	<body>
    <form id= "orden_compra_form" class="form-horizontal" action="orden_compra_form.php" method="POST" name = "orden_compra_form" autocomplete = "off">    
        <!-- AJAX section -->
        <script>
            //Valid the Cabecera before inserting in the Cabecera
            function validarCabecera(){
                var proveedor_cabecera = document.getElementById("nr_proveedor").value;
                var condicion_cabecera = document.getElementById("nr_condicion").value;
                var sucursal_cabecera = document.getElementById("nr_sucursal").value;
                if (proveedor_cabecera == "")
                {
                    alert("Favor elegir un Proveedor.");
                    return false;
                }else if (condicion_cabecera == ""){
                    alert("Favor elegir una Condicion de Compra.");
                    return false;
                }else if (sucursal_cabecera == ""){
                    alert("Favor elegir una Sucursal.");
                    return false;    
                }
                return true;
            }

            //Valid the line before inserting in the Detalle
            function validarLinea(){
                var cantidad_orden = document.getElementById("cantidad").value;
                var precio_orden = document.getElementById("precio_final").value;
                if (cantidad_orden <= 0)
                {
                    alert("La cantidad debe ser mayor a cero.");
                    return false;
                }else if (precio_orden <= 0){
                    alert("El precio del producto debe ser mayor a cero.");
                    return false;
                }
                return true;
            }

            //If the button Cancelar is pressed or if you leave or refresh the page
            function confirmarCancelar()
            {
                var cancelar;
                //cancelar.returnValue = 'Are you sure?';
                cancelar=confirm("¿Deseas dejar de cargar la Orden de Compra?");
                if (cancelar)
                {
                    var action = "EliminarOrden";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_orden_compra_ajax.php",
                        data: {"action":action,"nr":nr},
                        dataType : 'json',
                        success: function(detalleResultado)
                        {
                            //alert("Producto agregado");
                            //alert("Result Detalle: " + detalleResultado);
                        }
                    });
                    return true;
                }else{
                    window.close();
                    return false;
                }
            }

            //To show the list of items in Detalle
            function GetTotalOrden(){
                var orden_nr = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
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
                    url: "../ajax/orden_compra_ajax.php",
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

            //For dependent Selects
            $(document).ready(function()
            {
                var sucursal = document.getElementById("nr_sucursal").value;
                //$(".sucursal").change(function()
                //{
                    //var sucursal=$(this).val();
                    $.ajax
                    ({
                        type: "POST",
                        url: "../ajax/orden_compra_ajax.php",
                        data: {"sucursal":sucursal},
                        cache: false,
                        success: function(html)
                        {
                            $(".deposito").html(html);
                        } 
                    });
                //});
            });

            function getProveedor(){
                var proveedor = document.getElementById("id_proveedor").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: {"proveedor":proveedor},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_proveedor').val(data[0]);
                        $('#descripcion_proveedor').val(data[1]);
                        $("#nr_condicion").val(data[2]);
                    }
                });
            }

            function getProducto(){
                var producto = document.getElementById("id_producto").value;
                //alert(producto);
                var nr_proveedor = document.getElementById("nr_proveedor").value;
                //alert(nr_proveedor);
                var nr_moneda = document.getElementById("nr_moneda").value;
                //alert(nr_moneda);
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: {"producto":producto,"nr_proveedor":nr_proveedor,"nr_moneda":nr_moneda},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(";");
                        $('#nr_producto').val(data[0]);
                        $('#descripcion_producto').val(data[1]);
                        $("#impuesto").val(data[2]);
                        $("#nr_unidad").val(data[3]);
                        $("#id_unidad").val(data[4]);
                        $("#precio_lista").val(data[5]);
                    }
                });
            }

            //SEARCH SECTION
            //Proveedor
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_proveedor").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: {"buscar_proveedor":$(this).val()},
                    beforeSend: function(){
                        $("#id_proveedor").css("background","#FFF no-repeat 165px");
                    },
                    success: function(data){
                        $("#proveedor-suggestion-box").show();
                        $("#proveedor-suggestion-box").html(data);
                        $("#id_proveedor").css("background","#FFF");
                    }
                    });
                });
            });

            //To select proveedor
            function selectProveedor(val) {
                $("#id_proveedor").val(val);
                $("#proveedor-suggestion-box").hide();
                getProveedor();
            }

            //Producto
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_producto").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: {"buscar_producto":$(this).val()},
                    beforeSend: function(){
                        $("#id_producto").css("background","#FFF no-repeat 165px");
                    },
                    success: function(data){
                        $("#producto-suggestion-box").show();
                        $("#producto-suggestion-box").html(data);
                        $("#id_producto").css("background","#FFF");
                    }
                    });
                });
            });

            //To select Producto
            function selectProducto(val) {
                $("#id_producto").val(val);
                $("#producto-suggestion-box").hide();
                getProducto();
            }

            //Clean the form
            function limpiarDetalle(){
                $('#nr_producto').val('');
                $('#id_producto').val('');
                $('#descripcion_producto').val('');
                $('#cantidad').val(0);
                $('#nr_unidad').val('');
                $('#id_unidad').val('');
                $('#precio_lista').val(0);
                $('#impuesto').val('');
                $('#descuento').val(0);
                $('#total_linea').val(0);
            }
    
            //Store Cabecera and Detalle
            function GenerarCabecera(){
                if (validarCabecera())
                {
                    var action = "GenerarCabecera";
                    var nr = document.getElementById("nr").value;
                    //alert("Data: " + nr);
                    var nr_proveedor = document.getElementById("nr_proveedor").value;                
                    var fecha_orden = document.getElementById("fecha_orden").value;
                    var nr_condicion = document.getElementById("nr_condicion").value;
                    var nr_sucursal =document.getElementById("nr_sucursal").value;
                    var nr_deposito = document.getElementById("nr_deposito").value;
                    var nr_moneda = document.getElementById("nr_moneda").value;
                    var cotizacion_compra = document.getElementById("cotizacion_compra").value;
                    var cotizacion_venta = document.getElementById("cotizacion_venta").value;
                    var fecha_entrega = document.getElementById("fecha_entrega").value;
                    var total_exentas = document.getElementById("total_exentas").value;
                    var total_gravadas = document.getElementById("total_gravadas").value;
                    var total_iva = document.getElementById("total_iva").value;
                    var total_orden = document.getElementById("total_orden").value;
                    var obs = document.getElementById("obs").value;
                    nr_user = <?php echo json_encode($nr_user) ?>;
                    //var data = new Array();
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_orden_compra_ajax.php",
                        data: {"action":action,"nr":nr,"nr_proveedor":nr_proveedor,"fecha_orden":fecha_orden,"nr_condicion":nr_condicion,
                        "nr_sucursal":nr_sucursal,"nr_deposito":nr_deposito,"nr_moneda":nr_moneda,"cotizacion_compra":cotizacion_compra,
                        "cotizacion_venta":cotizacion_venta,"fecha_entrega":fecha_entrega,"total_exentas":total_exentas,
                        "total_gravadas":total_gravadas,"total_iva":total_iva,"total_orden":total_orden,"nr_user":nr_user,"obs":obs}, 
                        dataType : 'json',
                        success: function(cabeceraData)
                        {
                            alert("Datos Confirmados");
                            //alert("Result Cabecera: " + cabeceraData);
                            //data = JSON.stringify(cabeceraData);
                            //alert("Result JSON: " + data);
                            /*var data = cabeceraData.split(",");
                            $('#nr_producto').val(data[0]);
                            $('#descripcion_producto').val(data[1]);
                            $("#impuesto").val(data[2]);
                            $("#id_unidad").val(data[3]);
                            $("#precio_lista").val(data[4]);
                            $("#nr_proveedor").val(data[5]);*/
                        }
                    });
                    ListaProductosOrden();
                }
            }

            function GenerarDetalle(){
                //First store the Cabecera Detalle if not exist yet
                //GenerarCabecera();
                if (validarLinea())
                {
                    ListaProductosOrden();
                    var action = "GenerarDetalle";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    var nr_producto = document.getElementById("nr_producto").value;
                    //alert("Data1: " + nr_producto);
                    var cantidad = document.getElementById("cantidad").value;
                    //alert("Data2: " + cantidad);
                    var impuesto = document.getElementById("impuesto").value;
                    //alert("Data3: " + impuesto);
                    var nr_unidad =document.getElementById("nr_unidad").value;
                    //alert("Data4: " + nr_unidad);
                    var descuento = document.getElementById("descuento").value;
                    //alert("Data5: " + descuento);
                    var precio_lista = document.getElementById("precio_lista").value;
                    //alert("Data6: " + precio);
                    var precio_final = document.getElementById("precio_final").value;
                    //alert("Data7: " + precio);
                    var total_exentas_linea = document.getElementById("total_exentas_linea").value;
                    //alert("Data8: " + total_exentas_linea);
                    var total_gravadas_linea = document.getElementById("total_gravadas_linea").value;
                    //alert("Data9: " + total_gravadas_linea);
                    var total_iva_linea = document.getElementById("total_iva_linea").value;
                    //alert("Data10: " + total_iva_linea);
                    var total_linea = document.getElementById("total_linea").value;
                    //alert("Data11: " + total_linea);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_orden_compra_ajax.php",
                        data: {"action":action,"nr":nr,"nr_producto":nr_producto,"cantidad":cantidad,"impuesto":impuesto,
                        "nr_unidad":nr_unidad,"descuento":descuento,"precio_lista":precio_lista,"precio_final":precio_final,
                        "total_exentas_linea":total_exentas_linea,"total_gravadas_linea":total_gravadas_linea,"total_iva_linea":total_iva_linea,
                        "total_linea":total_linea},
                        dataType : 'json',
                        success: function(detalleResultado)
                        {
                            //limpiarDetalle();
                            //alert("Producto agregado");
                            //alert("Result Detalle: " + detalleResultado);
                            // Clear the form
                            //$("#detallerow")[0].reset();
                            var data = detalleResultado.split(",");
                            $('#nr_producto').val(data[1]);
                            $('#id_producto').val(data[2]);
                            $('#descripcion_producto').val(data[3]);
                            $('#cantidad').val(data[4]);
                            $('#nr_unidad').val(data[5]);
                            $('#id_unidad').val(data[6]);
                            $('#precio_lista').val(data[7]);
                            $('#precio_final').val(data[8]);
                            $('#impuesto').val(data[9]);
                            $('#descuento').val(data[10]);
                            $('#total_linea').val(data[11]);

                            ListaProductosOrden();
                            GetTotalOrden();
                            getCantidadDetalle();
                        }
                    });
                }
            }

            function EliminarDetalle(val1,val2){
                var action = "EliminarDetalle";
                var nr = val1;
                //alert("Data0: " + nr);
                var nr_producto = val2;
                //alert("Data1: " + nr_producto);
                $.ajax({
                    type: "POST",
                    url: "../ajax/guardar_orden_compra_ajax.php",
                    data: {"action":action,"nr":nr,"nr_producto":nr_producto},
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

            function EditarDetalle(val1,val2){
                var action = "EditarDetalle";
                var nr = val1;
                //alert("Data0: " + nr);
                var nr_producto = val2;
                //alert("Data1: " + nr_producto);
                $.ajax({
                    type: "POST",
                    url: "../ajax/guardar_orden_compra_ajax.php",
                    data: {"action":action,"nr":nr,"nr_producto":nr_producto},
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

            /*function ConfirmarOrden(){
                //First store the Cabecera Detalle if not exist yet
                var action = "ConfirmarOrden";
                $.ajax({
                    type: "POST",
                    url: "../ajax/guardar_orden_compra_ajax.php",
                    data: {"action":action},
                    dataType : 'json',
                    success: function(OrdenResultado)
                    {
                        alert("Result Orden: " + OrdenResultado);
                    }
                });
            }*/

            //Check if the window was closed, if true we delete everything
            /*window.onbeforeunload = function (e) 
            {
                var e = e || window.event;
                //IE & Firefox
                if (e) {
                    e.returnValue = 'Are you sure?';
                    confirmarCancelar();
                }
                // For Safari
                return 'Are you sure?';
            };*/

             //To show the list of items in Detalle
            function getCantidadDetalle(){
                var orden_compra_nr = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_compra_ajax.php",
                    data: {"orden_compra_nr":orden_compra_nr},
                    dataType : 'json',
                    success: function(data){
                        //alert("Result data: " + data);
                        var data = data.split(",");
                        $('#cantidad_detalle').val(data[0]);
                    }
                });
            }

            //Check if the Form has at least one detail before Confirm
            $('#orden_compra_form').submit(function() {
                getCantidadDetalle();
                var cantidad_detalle = document.getElementById("cantidad_detalle").value;
                //alert("Detalle: " + cantidad_detalle);
                if(cantidad_detalle < 1)
                {
                    alert("La orden debe tener por lo menos un producto.");
                    return false;
                }else {
                    return true;
                }
            });

        </script>
        <header>
		  <h1>Orden de Compra</h1>	
        </header>
        <article>
		<table class="cabecera">
			<tr>          
                <!--Column1-->
				<th>Orden #</th>
				<td><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></td>
                <!--Column2-->
				<th>Usuario</th>
				<td><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly></td>
			</tr>
			<tr>
                <!--Column1-->
				<th>Fecha</th>
                <td><input type="date" id="fecha_orden" name="fecha_orden" value="<?php echo date("Y-m-d");?>" class="boxes" required></td>
                <!--Column2-->
                <th>Sucursal</th>
              	<td>
              		<select id="nr_sucursal" name="nr_sucursal" required class="sucursal" >
                        <?php 
                            $sucursal_result = $orden_compra->get_sucursal();//We get all the results from the table
                            foreach ($sucursal_result as $row) {
                            $sucursal_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </td>
			</tr>
			<tr>
                <!--Column1-->
                <th>Cod. Proveedor</th>
                <p hidden><input type="text" id="nr_proveedor" name="nr_proveedor" value="<?php echo $nr_proveedor;?>"></p>
                <td>
                <div class="ProveedorSearch">
                    <input type="text" id="id_proveedor" name="id_proveedor" placeholder="Buscar..." value="<?php echo $id_proveedor;?>" onchange="getProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                    <input type="text" id="descripcion_proveedor" name="descripcion_proveedor" value="<?php echo $descripcion_proveedor;?>" class="dobleboxes" readonly disabled>
                    <div id="proveedor-suggestion-box"></div></td>
                </div>
                <!--Column2-->
                <th>Deposito de stock</th>
              	<td><!--Here we display the values according to the Sucursal selected-->
              		<select id="nr_deposito" name="nr_deposito" required class="deposito" >
                    <option selected="selected">Seleccione:</option>;
                    </select><p>
                </td>
            </tr>
			<tr>
                <!--Column1-->
				<th>Cond. de Compra</th>
              	<td>
              		<select id="nr_condicion" name="nr_condicion" required class="boxes">
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
                </td>
                <!--Column2-->
				<th>Moneda</th>
                <td>
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
                    <!--Only show this section if Moneda != MonedaBase is selected-->
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
                                <th>Cotizacion Compra</th>
                                <td><input type="number" id="cotizacion_compra" name="cotizacion_compra" step="any" value="<?php echo $cotizacion_compra;?>" class="boxes"></td>
                            </tr>
                            <tr>
                                <!--Column1-->
                                <th>Cotizacion Venta</th>
                                <td><input type="number" id="cotizacion_venta" name="cotizacion_venta" step="any" value="<?php echo $cotizacion_venta;?>" class="boxes"></td>
                            </tr>
                        </table>
                    </div>
                </td>
			</tr>
			<tr>
                <!--Column1-->
				<th>Fecha de Entrega</th>
                <td><input type="date" id="fecha_entrega" name="fecha_entrega" value="<?php echo date("Y-m-d");?>" class="boxes" required></td>
                <!--Column2-->
                <th>Obs.</th>
                <td><input type="text" id="obs" name="obs" value="<?php echo $obs ;?>" class="boxes"></td>                
			</tr>
		</table>
        <div>
        <input type="button" value="Guardar Datos Proveedor" id="guardar_cabecera" class="add_detail" onclick="GenerarCabecera()">
        </div>
        <table id="detalle"  class="detalle">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Cant.</th>
                    <th>Unid.Med.</th>
					<th>Precio Lista</th>
					<th>I.</th>
					<th>Desc. %</th>
                    <th>Precio Final</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody class = "detallerow">
				<tr>
					<td>
                    <div class="ProductoSearch">
                        <p hidden><input class="detallerow" type="text" id="nr_producto" name="nr_producto[]" value="<?php echo $nr_producto;?>"></p>
                        <input class="detallerow" type="text" id="id_producto" name="id_producto[]" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        <div id="producto-suggestion-box"></div>
                    </div>
                    </td>
                    <td><input class="detallerow" type="text" id="descripcion_producto" name="descripcion_producto[]" value="<?php echo $descripcion_producto;?>" readonly disabled></td>
					<td><input class="detallerow" type="number" id="cantidad" step="any" min="1" name="cantidad[]" value="<?php echo $cantidad;?>">
                    <!--Javascript result section-->
                    <p hidden><input class="detallerow" type="number" id="nr_unidad" name="nr_unidad[]" value="<?php echo $nr_unidad;?>">
                    <td><input class="detallerow" type="text" id="id_unidad" name="id_unidad[]" value="<?php echo $id_unidad;?>" readonly disabled></td>
					<td><input class="detallerow" type="number" id="precio_lista" step="any" name="precio_lista[]" value="<?php echo $precio_lista;?>"></td>
					<td><input class="detallerow" type="number" id="impuesto" step="any" name="impuesto[]" value="<?php echo $impuesto;?>" ></td>
					<td><input class="detallerow" type="number" id="descuento" step="any" name="descuento[]" value="<?php echo $descuento;?>" ></td>
                    <td><input class="detallerow" type="number" id="precio_final" step="any" name="precio_final[]" value="<?php echo $precio_final;?>" readonly disabled></td>
                    <!--Javascript result section-->
                    <p hidden><input class="detallerow" type="number" id="total_exentas_linea" name="total_exentas_linea[]" value="<?php echo $total_exentas_linea;?>"> 
                    <p hidden><input class="detallerow" type="number" id="total_gravadas_linea" name="total_gravadas_linea[]" value="<?php echo $total_gravadas_linea;?>">
                    <p hidden><input class="detallerow" type="number" id="total_iva_linea" name="total_iva_linea[]" value="<?php echo $total_iva_linea;?>">
                    <p hidden><input type="text" id="cantidad_detalle" name="cantidad_detalle" value="<?php echo $cantidad_detalle;?>">
                    <p hidden><input type="number" id="detalle_total_general" name="detalle_total_general" value="<?php echo $detalle_total_general;?>">
                    <p hidden><input type="number" id="detalle_iva_general" name="detalle_iva_general" value="<?php echo $detalle_iva_general;?>">
					<td><input class="detallerow" type="number" id="total_linea" step="any" name="total_linea[]" value="<?php echo $total_linea;?>" readonly ></td>
		      </tr>
        	</tbody>
			</table>
            <input type="button" value="Agregar" id="agregar_detalle" name="agregar_detalle" class="add_detail" onclick="GenerarDetalle()">
            <!--<input type="button" value="Agregar+1" id="agregar_detalle" name="agregar_detalle" class="add_detail" onclick="createjson()">-->
            <!--<input type="submit" class="btn-success" id="action" name="action" value ="Crear">
            <a class="add_detail">+</a>-->
            <!-- Lista Detalle section -->
             <div class="DetalleSection">
                    <div id="detalle-list"></div>
            </div>
            
            <?php
                /*if ($i <= $cantidad_f_compra) {
                    echo '<!-- Is commented because the max amount of lines is defined in the configuration -->';
                    echo '<a class="add_detail" onclick="AgregarDetalle()">+</a>';
                }*/
            ?>

            <!--Javascript result section-->
            <div>
                <p hidden><input type="number" id="total_exentas" name="total_exentas" value="<?php echo $total_exentas;?>">
                <p hidden><input type="number" id="total_gravadas" name="total_gravadas" value="<?php echo $total_gravadas;?>">  
                <p hidden><input type="number" id="total_orden" name="total_orden[]" value="<?php echo $total_orden;?>">
            </div>
			<table class="balance">
				<tr>
					<th><span balance>I.V.A.</th>
					<td><input type="number" id="total_iva" name="total_iva" value="<?php echo $total_iva;?>" readonly></td>
				</tr>
				<tr>
					<th><span balance>Total General</th>
					<td><input type="number" id="total_general" name="total_general" class="total_general" value="<?php echo $total_general;?>" readonly></td>
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
            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Confirmar" >
            <a class="btn" href="orden_compra.php" onclick="return confirmarCancelar()";>Cancelar</a>
        </div>
    </form>
	</body>
</html>

<?php
    if (isset($_POST['action']))
    {
        $nr = $_POST['nr'];
        /*$query = "select * from detalle_orden_compra DOC where DOC.nr = '$nr'";
        $query_result = $orden_compra -> query($query);//We get all the results from the table
        @$qty_register = $orden_compra -> rowCount($query);
        if ($qty_register > 0)
        {*/
            switch($_POST['action'])
            {
                case "Confirmar": 
                    $query = "select sum(total_linea) as total_orden, sum(total_exentas_linea) as total_exentas, sum(total_gravadas_linea) as total_gravadas, sum(total_iva_linea) as total_iva from detalle_orden_compra DOC where DOC.nr = '$nr'";
                    $update_result = $orden_compra -> query($query);//We get all the results from the table
                    @$qty_register = $orden_compra -> rowCount($query);
                    if($qty_register > 0){
                        foreach($update_result as $row):
                            $total_gravadas = $row['total_gravadas'];
                            $total_exentas = $row['total_exentas'];
                            $total_iva = $row['total_iva'];
                            $total_orden = $row['total_orden'];
                        endforeach;
                    }
                    $orden_compra->update_orden_compra($nr,$total_gravadas,$total_exentas,$total_iva,$total_orden);
                    echo "<script>window.close()</script>";
                break;
            }
        /*}else{
                echo "<script>alert('Debe existir al menos un articulo')</script>";
        }*/
    }
?>