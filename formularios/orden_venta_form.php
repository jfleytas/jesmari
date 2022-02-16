 <?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
    require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/orden_venta.class.php';
    $orden_venta = orden_venta::singleton();

    $page_name="orden_venta_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $fecha_orden=isset($_POST['fecha_orden']) ? $_POST['fecha_orden'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_cliente=isset($_POST['nr_cliente']) ? $_POST['nr_cliente'] : '';
    $id_cliente=isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $descripcion_cliente=isset($_POST['descripcion_cliente']) ? $_POST['descripcion_cliente'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $limite_credito=isset($_POST['limite_credito']) ? $_POST['limite_credito'] : '';
    $cant_dias=isset($_POST['cant_dias']) ? $_POST['cant_dias'] : '';
    $nr_grupo=isset($_POST['nr_grupo']) ? $_POST['nr_grupo'] : '';
    $nr_lista_precios=isset($_POST['nr_lista_precios']) ? $_POST['nr_lista_precios'] : '';
    $id_lista_precios=isset($_POST['id_lista_precios']) ? $_POST['id_lista_precios'] : '';
    $nr_condicion=isset($_POST['nr_condicion']) ? $_POST['nr_condicion'] : '';
    $descripcion_condicion=isset($_POST['descripcion_condicion']) ? $_POST['descripcion_condicion'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $nr_deposito=isset($_POST['nr_deposito']) ? $_POST['nr_deposito'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $descripcion_moneda=isset($_POST['descripcion_moneda']) ? $_POST['descripcion_moneda'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : 1;
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : 1;
    $nr_vendedor=isset($_POST['nr_vendedor']) ? $_POST['nr_vendedor'] : '';
    $descripcion_vendedor=isset($_POST['descripcion_vendedor']) ? $_POST['descripcion_vendedor'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $stock_actual=isset($_POST['stock_actual']) ? $_POST['stock_actual'] : '';
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
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : 0;
	$total_exentas=isset($_POST['total_exentas']) ? $_POST['total_exentas'] : 0;
    $total_gravadas=isset($_POST['total_gravadas']) ? $_POST['total_gravadas'] : 0;
    $total_iva=isset($_POST['total_iva']) ? $_POST['total_iva'] : 0;
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : 0;
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : 0;
    $total_orden=isset($_POST['total_orden']) ? $_POST['total_orden'] : 0;
    $obs=isset($_POST['obs']) ? $_POST['obs'] : '';
    $cantidad_detalle=isset($_POST['cantidad_detalle']) ? $_POST['cantidad_detalle'] : 0;
    $detalle_total_general=isset($_POST['detalle_total_general']) ? $_POST['detalle_total_general'] : 0;
    $detalle_iva_general=isset($_POST['detalle_iva_general']) ? $_POST['detalle_iva_general'] : 0;

    //Defining Order # checking the next value from the sequence
    $order_result = $orden_venta->query("select nextval('cabecera_orden_venta_nr_seq')");
    //$order_result = $orden_venta->query("select last_value from cabecera_orden_venta_nr_seq");
    foreach ($order_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the configuration for Ventas forms
    $configuracion_result = $orden_venta->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $orden_venta->query("select * from users where id_user ='$usuario'");//We get the desired result from the table
    foreach ($user_result as $resultado_user) {        
        $nr_user = $resultado_user['nr'];
        $id_user = $resultado_user['id_user'];
        $nombre_apellido = $resultado_user['nombre_apellido'];
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="../css/estilo_formulario.css" />
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
        <script src="../js/jquery.js"></script>
		<script src="../js/orden_venta_script.js"></script> 
	</head>
	<body>
    <form id = "orden_venta_form" class="form-horizontal" action="orden_venta_form.php" method="POST" name = "orden_venta_form" autocomplete = "off">    
        <!-- AJAX section -->
        <script>
            //Valid the Cabecera before inserting it
            function validarCabecera(){
                var cliente_cabecera = document.getElementById("nr_cliente").value;
                var condicion_cabecera = document.getElementById("nr_condicion").value;
                var sucursal_cabecera = document.getElementById("nr_sucursal").value;
                var limite_credito_cabecera = document.getElementById("limite_credito").value;
                var cant_dias_limite = document.getElementById("cant_dias").value;
                if (cliente_cabecera == "")
                {
                    alert("Favor elegir un Cliente.");
                    return false;
                }else if (condicion_cabecera == ""){
                    alert("Favor elegir una Condicion de Compra.");
                    return false;
                }else if (sucursal_cabecera == ""){
                    alert("Favor elegir una Sucursal.");
                    return false;    
                }else if ((limite_credito_cabecera == 0) && (cant_dias_limite > 0)){
                    alert("El cliente no posee saldo suficiente para realizar una compra.");
                    return false;    
                }
                return true;
            }

            //Valid the line before inserting in the Detalle
            function validarLinea(){
                var cantidad_orden = document.getElementById("cantidad").value;
                var precio_orden = document.getElementById("precio_final").value;
                var stock_actual = parseFloat(document.getElementById("stock_actual").value);
                if (cantidad_orden <= 0)
                {
                    alert("La cantidad debe ser mayor a cero.");
                    return false;
                }else if (cantidad_orden > stock_actual){
                    alert("El stock disponible es de = " + stock_actual +", no se puede confirmar");
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
                cancelar=confirm("¿Deseas dejar de cargar la Orden de Venta?");
                if (cancelar)
                {
                    var action = "EliminarOrden";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_orden_venta_ajax.php",
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
                        url: "../ajax/orden_venta_ajax.php",
                        data: {"sucursal":sucursal},
                        cache: false,
                        success: function(html)
                        {
                            $(".deposito").html(html);
                        } 
                    });
                //});
            });

            function getCliente(){
                var cliente = document.getElementById("id_cliente").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_venta_ajax.php",
                    data: {"cliente":cliente},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(";");
                        $('#nr_cliente').val(data[0]);
                        $('#descripcion_cliente').val(data[1]);
                        $("#nr_condicion").val(data[2]);
                        $("#nr_grupo").val(data[3]);
                        $("#nr_lista_precios").val(data[4]);
                        $("#id_lista_precios").val(data[5]);
                        $("#nr_moneda").val(data[6]);
                        $("#descripcion_moneda").val(data[7]);
                        $("#nr_vendedor").val(data[8]);
                        $("#descripcion_vendedor").val(data[9]);
                        $("#descuento").val(data[10]);
                        $("#limite_credito").val(data[11]);
                        $("#cant_dias").val(data[12]);
                    }
                });
            }

            function getProducto(){
                var producto = document.getElementById("id_producto").value;
                var nr_lista_precios = document.getElementById("nr_lista_precios").value;
                var nr_moneda = document.getElementById("nr_moneda").value;
                var nr_condicion = document.getElementById("nr_condicion").value;
                var nr_sucursal =document.getElementById("nr_sucursal").value;
                var nr_deposito =document.getElementById("nr_deposito").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/orden_venta_ajax.php",
                    data: {"producto":producto,"nr_lista_precios":nr_lista_precios,"nr_moneda":nr_moneda,"nr_sucursal":nr_sucursal,"nr_deposito":nr_deposito},
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
                        $("#stock_actual").val(data[6]);
                    }
                });
            }

            //SEARCH SECTION
            //Cliente
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_cliente").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/orden_venta_ajax.php",
                    data:{"buscar_cliente":$(this).val()},
                    beforeSend: function(){
                        $("#id_cliente").css("background","#FFF no-repeat 165px");
                    },
                    success: function(data){
                        $("#cliente-suggestion-box").show();
                        $("#cliente-suggestion-box").html(data);
                        $("#id_cliente").css("background","#FFF");
                    }
                    });
                });
            });

            //To select cliente
            function selectCliente(val) {
                $("#id_cliente").val(val);
                $("#cliente-suggestion-box").hide();
                getCliente();
            }

            //Producto
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_producto").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/orden_venta_ajax.php",
                    data:{"buscar_producto":$(this).val()},
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

            //Store Cabecera and Detalle
            function GuardarCabecera(){
                if (validarCabecera())
                {
                    var action = "Crear";
                    var nr = document.getElementById("nr").value;
                    var nr_cliente = document.getElementById("nr_cliente").value;
                    //alert("Cliente NR.: " + nr_cliente);
                    var fecha_orden = document.getElementById("fecha_orden").value;
                    var nr_vendedor = document.getElementById("nr_vendedor").value;
                    //alert("Data: " + nr_vendedor);
                    var nr_condicion = document.getElementById("nr_condicion").value;
                    var nr_sucursal =document.getElementById("nr_sucursal").value;
                    //alert("Data: " + nr_sucursal);
                    var nr_deposito = document.getElementById("nr_deposito").value;
                    var nr_moneda = document.getElementById("nr_moneda").value;
                    var nr_lista_precios = document.getElementById("nr_lista_precios").value;
                    var total_exentas = document.getElementById("total_exentas").value;
                    var total_gravadas = document.getElementById("total_gravadas").value;
                    var total_iva = document.getElementById("total_iva").value;
                    var total_orden = document.getElementById("total_orden").value;
                    nr_user = <?php echo json_encode($nr_user) ?>;
                    var cotizacion_compra = document.getElementById("cotizacion_compra").value;
                    var cotizacion_venta = document.getElementById("cotizacion_venta").value;
                    var obs = document.getElementById("obs").value;
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_orden_venta_ajax.php",
                        data: {"action":action,"nr":nr,"nr_cliente":nr_cliente,"fecha_orden":fecha_orden,"nr_vendedor":nr_vendedor,
                        "nr_condicion":nr_condicion,"nr_sucursal":nr_sucursal,"nr_deposito":nr_deposito,"nr_moneda":nr_moneda,
                        "nr_lista_precios":nr_lista_precios,"cotizacion_compra":cotizacion_compra,"cotizacion_venta":cotizacion_venta,"total_exentas":total_exentas,
                        "total_gravadas":total_gravadas,"total_iva":total_iva,"total_orden":total_orden,"nr_user":nr_user,"obs":obs},
                        dataType : 'json',
                        success: function(resultado)
                        {
                            alert("Datos Confirmados");
                            //alert("Result Cabecera: " + resultado);
                            /*var data = resultado.split(",");
                            $('#nr_producto').val(data[0]);
                            $('#descripcion_producto').val(data[1]);
                            $("#impuesto").val(data[2]);
                            $("#id_unidad").val(data[3]);
                            $("#precio_producto").val(data[4]);
                            $("#nr_cliente").val(data[5]);*/
                        }
                    });
                    ListaProductosOrden();
                }
            }

            function GuardarDetalle(){
                //First store the Cabecera Detalle if not exist yet
                //GuardarCabecera();
                if (validarLinea())
                {
                    ListaProductosOrden();
                    var action = "AgregarDetalle";
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
                    //alert("Data6: " + precio_lista);
                    var precio_final = document.getElementById("precio_final").value;
                    //alert("Data7: " + precio_final);
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
                        url: "../ajax/guardar_orden_venta_ajax.php",
                        data: {"action":action,"nr":nr,"nr_producto":nr_producto,"cantidad":cantidad,"impuesto":impuesto,
                        "nr_unidad":nr_unidad,"descuento":descuento,"precio_lista":precio_lista,"precio_final":precio_final,
                        "total_exentas_linea":total_exentas_linea,"total_gravadas_linea":total_gravadas_linea,"total_iva_linea":total_iva_linea,    
                        "total_linea":total_linea},
                        dataType : 'json',
                        success: function(resultado)
                        {
                            //alert("Producto Agregado");
                            //alert("Result Detalle: " + resultado);
                            var data = resultado.split(",");
                            $('#nr_producto').val(data[2]);
                            $('#id_producto').val(data[3]);
                            $('#descripcion_producto').val(data[4]);
                            $('#cantidad').val(data[5]);
                            $('#nr_unidad').val(data[6]);
                            $('#id_unidad').val(data[7]);
                            $('#precio_lista').val(data[8]);
                            $('#precio_final').val(data[9]);
                            $('#impuesto').val(data[10]);
                            //$('#descuento').val(data[11]);
                            $('#total_linea').val(data[12]);
                            $('#stock_actual').val(data[13]);

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
                    url: "../ajax/guardar_orden_venta_ajax.php",
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
                    url: "../ajax/guardar_orden_venta_ajax.php",
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

            $(document).ready(function() {
                // Setting focus on first textbox
                $('input:text:eighth').focus();
                // binding keydown event to textbox
                $('input:text').bind('keydown', function(e) {
                    // detecting keycode returned from keydown and comparing if its equal to 13 (enter key code)
                    if (e.keyCode == 13) {
                    // by default if you hit enter key while on textbox so below code will prevent that default behaviour
                        e.preventDefault();
                        // getting next index by getting current index and incrementing it by 1 to go to next textbox
                        var nextIndex = $('input:text').index(this) + 1;
                        // getting total number of textboxes on the page to detect how far we need to go
                        var maxIndex = $('input:text').length;
                        // check to see if next index is still smaller then max index
                        if (nextIndex < maxIndex) {
                        // setting index to next textbox using CSS3 selector of nth child
                        $('input:text:eq(' + nextIndex+')').focus();
                        }
                    } 
                });
            });

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
            $('#orden_venta_form').submit(function() {
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

            $(document).ready(function () {
                $("input").not($(":button")).keypress(function (evt) {
                    if (evt.keyCode == 13) {
                        itype = $(this).attr('type');
                        if (itype !== 'Guardar') {
                            var fields = $(this).parents('form:eq(0),body').find('button, input, textarea, select');
                            var index = fields.index(this);
                            if (index > -1 && (index + 1) < fields.length) {
                                fields.eq(index + 1).focus();
                            }
                            return false;
                        }
                    }
                });
            });

            //Check if the window was closed, if true we delete everything
            /*window.onbeforeunload = function (e) {
                var e = e || window.event;
                //IE & Firefox
                if (e) {
                    e.returnValue = 'Are you sure?';
                }
                // For Safari
                return 'Are you sure?';
            };*/
        </script>
        <header>
		  <h1>Orden de Venta</h1>	
        </header>
        <article>
        <!--Javascript result section-->
        <div>
        <p hidden><input type="text" id="nr_moneda" name="nr_moneda" value="<?php echo $nr_moneda;?>">
        <p hidden><input type="text" id="nr_unidad" name="nr_unidad" value="<?php echo $nr_unidad;?>">
        <p hidden><input type="text" id="nr_vendedor" name="nr_vendedor" value="<?php echo $nr_vendedor;?>">
        <p hidden><input type="text" id="nr_lista_precios" name="nr_lista_precios" value="<?php echo $nr_lista_precios;?>">
        <p hidden><input type="number" id="total_orden" name="total_orden" value="<?php echo $total_orden;?>">
        <p hidden><input type="number" id="total_exentas" name="total_exentas" value="<?php echo $total_exentas;?>">
        <p hidden><input type="number" id="total_exentas_linea" name="total_exentas_linea" value="<?php echo $total_exentas_linea;?>">
        <p hidden><input type="number" id="total_gravadas" name="total_gravadas" value="<?php echo $total_gravadas;?>">
        <p hidden><input type="number" id="total_gravadas_linea" name="total_gravadas_linea" value="<?php echo $total_gravadas_linea;?>">
        <p hidden><input type="number" id="total_iva_linea" name="total_iva_linea" value="<?php echo $total_iva_linea;?>">
        <p hidden><input type="number" id="limite_credito" name="limite_credito" value="<?php echo $limite_credito;?>">
        <p hidden><input type="text" id="cant_dias" name="cant_dias" value="<?php echo $cant_dias;?>">
        <p hidden><input type="text" id="cantidad_detalle" name="cantidad_detalle" value="<?php echo $cantidad_detalle;?>">
        <p hidden><input type="number" id="detalle_total_general" name="detalle_total_general" value="<?php echo $detalle_total_general;?>">
        <p hidden><input type="number" id="detalle_iva_general" name="detalle_iva_general" value="<?php echo $detalle_iva_general;?>">
        </div>
        
		<table class="cabecera">
			<tr>          
                <!--Column1-->
				<th><span>Orden #</span></th>
				<td><span><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></span></td>
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
              		<select id="nr_sucursal" name="nr_sucursal" required class="sucursal" >
                        <!--<option value="">Seleccione:</option>';-->
                        <?php 
                            $sucursal_result = $orden_venta->get_sucursal();//We get all the results from the table
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
                <th><span>Cod. Cliente</span></th>
                <p hidden><input type="text" id="nr_cliente" name="nr_cliente" value="<?php echo $nr_cliente;?>"></p>
                <td><span>
                <div class="ClienteSearch">
                    <input type="text" id="id_cliente" name="id_cliente" placeholder="Buscar..." value="<?php echo $id_cliente;?>" onchange="getCliente();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus><span>
                    <span><input type="text" id="descripcion_cliente" name="descripcion_cliente" value="<?php echo $descripcion_cliente;?>" readonly disabled></span>
                    <div id="cliente-suggestion-box"></div>
                </div>
                </td>
                <!--Column2-->
                <th><span>Deposito de stock</span></th>
              	<td><!--Here we display the values according to the Sucursal selected-->
              		<select id="nr_deposito" name="nr_deposito" required class="deposito" >
                        <option selected="selected">Seleccione:</option>;
                    </select><p>
                </td>
            </tr>
			<tr>
                <!--Column1-->
				<th><span>Cond. de Venta</span></th>
              	<td><span>
              		<select id="nr_condicion" name="nr_condicion" required class="boxes">
                        <option value="">Seleccione:</option>';
                        <?php 
                            $condicion_result = $orden_venta->get_condicion_compra_venta();//We get all the results from the table
                            foreach ($condicion_result as $row){
                            $condicion_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_condicion==$condicion_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </span></td>
                <!--Column2-->
				<th><span>Moneda</span></th>
                <td><span><input type="text" id="descripcion_moneda" name="descripcion_moneda" value="<?php echo $descripcion_moneda ;?>" class="boxes" onChange= "mostrarCotizacion(this.value)" readonly disabled></span></td>
                <!--Only show this section if Moneda != MonedaBase -->

                <?php 
                /*$nr_moneda = $_POST['nr_moneda'];
                echo 'Moneda: '.$nr_moneda ;
                if ($nr_moneda != 1)
                {
                    echo '<script>element = document.getElementById("hideContent");</script>';
                    $valor_moneda_defecto = $moneda_defecto;
                    $valor_moneda = $nr_moneda;
                    //document.write(valor_moneda);
                    if ($valor_moneda == $valor_moneda_defecto)
                    //if (valor_moneda == '1')
                    {
                        echo '<script>element.style.display="none";</script>';
                    }else {
                        echo '<script>element.style.display="block";</script>';
                    }
                } */?>  
			</tr>
			<tr>
                <!--Column1-->
                <th><span>Lista de Precio</span></th>
                <td><span><input type="text" id="id_lista_precios" name="id_lista_precios" value="<?php echo $id_lista_precios;?>" class="boxes" readonly disabled></span></td>
				<!--Column2-->
                <th><span>Vendedor</span></th>
                <td><span><input type="text" id="descripcion_vendedor" name="descripcion_vendedor" value="<?php echo $descripcion_vendedor ;?>" class="boxes" readonly disabled></span></td>
			</tr>
            <tr>
                <!--Column1-->
                
                <!--Column2-->
                <th><span>Obs.</span></th>
                <td><input type="text" id="obs" name="obs" value="<?php echo $obs ;?>" class="boxes"></td>
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
        <div>
        <input type="button" value="Guardar" id="guardar_cabecera" class="add_detail" onclick="GuardarCabecera()">
        </div>
        <table class="detalle">
			<thead>
				<tr>
					<th width="5%">Codigo</th>
					<th width="30%">Descripcion</th>
                    <th width="5%">Stock</th>
					<th width="5%">Cant.</th>
                    <th width="10%">Unid.Med.</th>
					<th width="10%">Precio Lista</th>
					<th width="5%">I.</th>
					<th width="5%">Desc. %</th>
                    <th width="10%">Precio Final</th>
					<th width="20%">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
                    <div class="ProductoSearch">
                        <p hidden><input type="text" id="nr_producto" name="nr_producto" value="<?php echo $nr_producto;?>"></p>
                        <input type="text" id="id_producto" name="id_producto" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        <div id="producto-suggestion-box"></div>
                    </div>
                    </td>
                    <td><input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>"readonly disabled></td>
                    <td><input type="number" id="stock_actual" name="stock_actual" value="<?php echo $stock_actual;?>" readonly disabled  style="font-weight: bold;"></td>
					<td><input type="number" id="cantidad" min="1" name="cantidad" value="<?php echo $cantidad;?>"></td>
                    <td></p><input type="text" id="id_unidad" name="id_unidad" value="<?php echo $id_unidad;?>" readonly disabled></td>
					<td><input type="number" id="precio_lista" name="precio_lista" value="<?php echo $precio_lista;?>"></td>
					<td><input type="number" id="impuesto" name="impuesto" value="<?php echo $impuesto;?>" readonly disabled></td>
					<td><input type="number" id="descuento" name="descuento" value="<?php echo $descuento;?>"></td>
                    <td><input type="number" id="precio_final" name="precio_final" value="<?php echo $precio_final;?>" readonly disabled></td>
					<td><input type="number" id="total_linea" name="total_linea" value="<?php echo $total_linea;?>" readonly ></td>
				</tr>
			</tbody>
			</table>
            <input type="button" value="Agregar" id="agregar_detalle" name="agregar_detalle" class="add_detail" onclick="GuardarDetalle()">
            <!--<input type="submit" class="btn-success" id="action" name="action" value ="Crear">
            <a class="add_detail">+</a>-->

            <!-- Lista Detalle section -->
             <div class="detalle_aplicacion">
                    <div id="detalle-list"></div>
            </div>
            
            <?php
                /*if ($i <= $cantidad_f_compra) {
                    echo '<!-- Is commented because the max amount of lines is defined in the configuration -->';
                    echo '<a class="add_detail" onclick="AgregarDetalle()">+</a>';
                }*/
            ?>
			<table class="balance">
				<tr>
					<th><span balance>I.V.A.</span></th>
					<td><span detallerow-number><input type="number" id="total_iva" name="total_iva" value="<?php echo $total_iva;?>" readonly></span></td>
				</tr>
				<tr>
					<th><span balance>Total General</span></th>
					<td><span detallerow-number><input type="number" id="total_general" name="total_general" value="<?php echo $total_general;?>" readonly></span></td>
				</tr>
			</table>
		</article>
		<aside>
			<div contenteditable>
				<p>Obs.:</p>
				<p>No valido como Comprobante de Venta</p>
			</div>
		</aside>
        <div class="form-actions">
            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Confirmar">
            <a class="btn" href="orden_venta.php" onclick="return confirmarCancelar()";>Cancelar</a>
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
                $query = "select sum(total_linea) as total_orden, sum(total_exentas_linea) as total_exentas, sum(total_gravadas_linea) as total_gravadas, sum(total_iva_linea) as total_iva from detalle_orden_venta DOV where DOV.nr = '$nr'";
                $update_result = $orden_venta -> query($query);//We get all the results from the table
                @$qty_register = $orden_venta -> rowCount($query);
                
                if($qty_register > 0){
                    foreach($update_result as $row):
                        $total_gravadas = $row['total_gravadas'];
                        $total_exentas = $row['total_exentas'];
                        $total_iva = $row['total_iva'];
                        $total_orden = $row['total_orden'];
                    endforeach;
                }

                if (($total_orden > $limite_credito) && ($cant_dias > 0))
                {
                    echo "<script>alert('El cliente no posee credito suficiente para confirmar la compra.')</script>";
                    $orden_venta->delete_orden_venta($nr);
                    echo "<script>window.close()</script>";
                }else{
                    $orden_venta->update_orden_venta($nr,$total_gravadas,$total_exentas,$total_iva,$total_orden);
                    echo "<script>window.close()</script>";
                }
            break;
        }
    }
?>