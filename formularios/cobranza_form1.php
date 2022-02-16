<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
	require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/cobranza.class.php';
    $cobranza = cobranza::singleton();

    $page_name="cobranza_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $nr_cobranza=isset($_POST['nr_cobranza']) ? $_POST['nr_cobranza'] : '';
    $fecha_cobranza=isset($_POST['fecha_cobranza']) ? $_POST['fecha_cobranza'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_cliente=isset($_POST['nr_cliente']) ? $_POST['nr_cliente'] : '';
    $id_cliente=isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $descripcion_cliente=isset($_POST['descripcion_cliente']) ? $_POST['descripcion_cliente'] : '';
    $direccion=isset($_POST['direccion']) ? $_POST['direccion'] : '';
    $telefono=isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $parametro_sucursal=isset($_POST['parametro_sucursal']) ? $_POST['parametro_sucursal'] : '';
    $nr_caja=isset($_POST['nr_caja']) ? $_POST['nr_caja'] : '';
    $nr_moneda=isset($_POST['nr_moneda']) ? $_POST['nr_moneda'] : '';
    $cotizacion_compra=isset($_POST['cotizacion_compra']) ? $_POST['cotizacion_compra'] : 1;
    $cotizacion_venta=isset($_POST['cotizacion_venta']) ? $_POST['cotizacion_venta'] : 1;
    $nr_factura_venta=isset($_POST['nr_factura_venta']) ? $_POST['nr_factura_venta'] : '';
    $nr_factura_venta_oficial=isset($_POST['nr_factura_venta_oficial']) ? $_POST['nr_factura_venta_oficial'] : '';
    $saldo_factura=isset($_POST['saldo_factura']) ? $_POST['saldo_factura'] : '';
    $nr_nota_credito=isset($_POST['nr_nota_credito']) ? $_POST['nr_nota_credito'] : '';
    $nr_nota_credito_oficial=isset($_POST['nr_nota_credito_oficial']) ? $_POST['nr_nota_credito_oficial'] : '';
    $nr_medio_pago=isset($_POST['nr_medio_pago']) ? $_POST['nr_medio_pago'] : '';
    $id_medio_pago=isset($_POST['id_medio_pago']) ? $_POST['id_medio_pago'] : '';
    $descripcion_medio_pago=isset($_POST['descripcion_medio_pago']) ? $_POST['descripcion_medio_pago'] : '';
    $monto_pago=isset($_POST['monto_pago']) ? $_POST['monto_pago'] : 0;
    $monto_aplicado=isset($_POST['monto_aplicado']) ? $_POST['monto_aplicado'] : 0;
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : 0;
    $total_linea_aplicado=isset($_POST['total_linea_aplicado']) ? $_POST['total_linea_aplicado'] : 0;
    $total_pago_aplicado=isset($_POST['total_pago_aplicado']) ? $_POST['total_pago_aplicado'] : 0;
    $total_pago=isset($_POST['total_pago']) ? $_POST['total_pago'] : 0;
    $obs=isset($_POST['obs']) ? $_POST['obs'] : '';
    $obs_detalle=isset($_POST['obs_detalle']) ? $_POST['obs_detalle'] : '';
    $estado=isset($_POST['estado']) ? $_POST['estado'] : 0;
    $nr_user=isset($_POST['nr_user']) ? $_POST['nr_user'] : '';
    $nr_cobrador=isset($_POST['nr_cobrador']) ? $_POST['nr_cobrador'] : '';
    $detalle_total_aplicado=isset($_POST['detalle_total_aplicado']) ? $_POST['detalle_total_aplicado'] : 0;
    $detalle_total_pago=isset($_POST['detalle_total_pago']) ? $_POST['detalle_total_pago'] : 0;
    $cantidad_pago=isset($_POST['cantidad_pago']) ? $_POST['cantidad_pago'] : 0;
    $cantidad_aplicacion=isset($_POST['cantidad_aplicacion']) ? $_POST['cantidad_aplicacion'] : 0;
    $diferencia=isset($_POST['diferencia']) ? $_POST['diferencia'] : 0;

    //Defining Order # checking the next value from the sequence
    $order_result = $cobranza->query("select nextval('cabecera_cobranza_nr_seq')");
    //$order_result = $cobranza->query("select last_value from cabecera_cobranza_nr_seq");
    foreach ($order_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the Moneda por defecto
    $configuracion_result = $cobranza->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$nr_nota_credito_oficial_f_compra = $resultado_config['nr_nota_credito_oficial_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $nr_nota_credito_oficial_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $cobranza->query("select U.nr nr_user, U.id_user, U.nombre_apellido, P.nr nr_personal, P.id_personal, P.nombre_apellido personal_nombre, 
    P.nr_sucursal from users U left join personal P on U.nr = P.nr_user where U.id_user = '$usuario'");//We get the desired result from the table
    foreach ($user_result as $resultado_user) {        
        $nr_user = $resultado_user['nr_user'];
        $id_user = $resultado_user['id_user'];
        $nombre_apellido = $resultado_user['nombre_apellido'];
        //$nr_sucursal = $resultado_user['nr_sucursal'];
        //echo '<script>getDepositos()</script>';
    }

    //Get the last Invoice Number
    $nr_sucursal=1;
    $cobranza_result = $cobranza->query("select * from comprobantes where nr_sucursal = '$nr_sucursal' and aplicado_a='recibo'");//We get the desired result from the table
    foreach ($cobranza_result as $resultado_cobranza) {        
        @$formato_recibo = $resultado_cobranza['formato_comprobante'];
        @$ultimo_nro_impreso = $resultado_cobranza['ultimo_nro_impreso'];
    }

    #Create the next Invoice number
    @$nr_recibo = $ultimo_nro_impreso + 1;
    $nr_cobranza = $formato_recibo.'-'.str_pad($nr_recibo, 7, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="../css/estilo_formulario.css" />
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
        <script src="../js/jquery.js"></script>
		<script src="../js/cobranza_script.js"></script> 
	</head>
	<body>
    <form id= "cobranza_form" class="form-horizontal" action="cobranza_form.php" method="POST" name = "cobranza_form" autocomplete = "off">    
        <!-- AJAX section -->
        <script>
            //Valid the Cabecera before inserting in the Cabecera
            function validarCabecera(){
                var cliente_cabecera = document.getElementById("nr_cliente").value;
                var sucursal_cabecera = document.getElementById("nr_sucursal").value;
                var cobrador_cabecera = document.getElementById("nr_cobrador").value;
                if (cliente_cabecera == "")
                {
                    alert("Favor elegir un Cliente.");
                    return false;
                }else if (sucursal_cabecera == ""){
                    alert("Favor elegir una Sucursal.");
                    return false;    
                }else if (cobrador_cabecera == ""){
                    alert("Favor elegir un Cobrador.");
                    return false;    
                }
                return true;
            }

            //Valid the line before inserting in the Detalle
            function validarLineaPago(){
                var monto_pago = document.getElementById("monto_pago").value;
                if (monto_pago <= 0){
                    alert("El monto del pago debe ser mayor a cero.");
                    return false;
                }
                return true;
            }

            //Valid the Aplication line before inserting in the Detalle Aplicacion Pago
            function validarLineaAplicacionPago(){
                var nr_factura_venta_oficial = document.getElementById("nr_factura_venta_oficial").value;
                //alert("Result data: " + nr_factura_oficial);
                var monto_aplicado = Number(document.getElementById("monto_aplicado").value);
                //alert("Result data: " + monto_aplicado);
                var saldo_factura = Number(document.getElementById("saldo_factura").value);
                //alert("Result data: " + saldo_factura);
                if (nr_factura_venta_oficial == "")
                {
                    alert("Favor ingresar un Nro. de Factura.");
                    return false;
                }else if (monto_aplicado <= 0)
                {
                    alert("El monto aplicado debe ser mayor a cero.");
                    return false;
                }
                else if (monto_aplicado > saldo_factura)
                {
                    alert("El monto aplicado no puede ser mayor al saldo.");
                    return false;
                }
                return true;
            }

            //To show the list of items in Detalle
            function GetTotalOrden(){
                var orden_nr = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"orden_nr":orden_nr},
                    dataType : 'json',
                    success: function(resultado){
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#detalle_total_aplicado').val(data[0]);
                        $('#detalle_total_pago').val(data[1]);
                    }
                });
            }

            //If the button Cancelar is pressed or if you leave or refresh the page
            function confirmarCancelar(){
                var cancelar;
                //cancelar.returnValue = 'Are you sure?';
                cancelar=confirm("¿Desea dejar de cargar el Recibo?");
                if (cancelar)
                {
                    var action = "EliminarOrden";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_cobranza_ajax.php",
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

            //To show the list of items in Detalle Pago
            function ListaDetallePagoOrden(){
                var listar_pago = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"listar_pago":listar_pago},
                    success: function(data){
                        //alert("Result data: " + data);
                        $("#detalle-pago-list").show();
                        $("#detalle-pago-list").html(data);
                        $("listar_pago").css("background","#FFF");
                    }
                });
            }

            //To show the list of items in Detalle Aplicacion Pago
            function ListaDetalleAplicacionPago(){
                var listar_aplicacion_pago = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"listar_aplicacion_pago":listar_aplicacion_pago},
                    success: function(data){
                        //alert("Result data: " + data);
                        $("#detalle-aplicacion-list").show();
                        $("#detalle-aplicacion-list").html(data);
                        $("listar_aplicacion_pago").css("background","#FFF");
                    }
                });
            }

            function onClick(e){
                ListaDetallePagoOrden();
                ListaDetalleAplicacionPago();
                GetTotalOrden();
                getCantidadDetalle();
            }

            if (window.addEventListener){
                document.addEventListener('click', onClick);
            }

            //For dependent Selects
            $(document).ready(function(){
                var sucursal = document.getElementById("nr_sucursal").value;
                //$(".sucursal").change(function()
                //{
                    //var sucursal=$(this).val();
                    $.ajax
                    ({
                        type: "POST",
                        url: "../ajax/cobranza_ajax.php",
                        data: {"sucursal":sucursal},
                        cache: false,
                        success: function(html)
                        {
                            $(".caja").html(html);
                        } 
                    });
                //});
            });

            function getCliente(){
                var cliente = document.getElementById("id_cliente").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"cliente":cliente},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_cliente').val(data[0]);
                        $('#descripcion_cliente').val(data[1]);
                    }
                });
            }

            function getCaja(){
                var sucursal = document.getElementById("nr_sucursal").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"sucursal":sucursal},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        $("#parametro_sucursal").val(resultado);
                    }
                });
            }

            function getMedioPago(){
                var medio_pago = document.getElementById("id_medio_pago").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"medio_pago":medio_pago},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_medio_pago').val(data[0]);
                        $('#descripcion_medio_pago').val(data[1]);
                    }
                });
            }

            function getFacturaVenta(){
                var factura_venta = document.getElementById("nr_factura_venta_oficial").value;
                var cliente = document.getElementById("nr_cliente").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"factura_venta":factura_venta},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_factura_venta').val(data[0]);
                        $('#nr_factura_venta_oficial').val(data[1]);
                        $('#saldo_factura').val(data[2]);
                    }
                });
            }

            //SEARCH SECTION
            //cliente
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_cliente").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"buscar_cliente":$(this).val()},
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
            function selectCliente(val){
                $("#id_cliente").val(val);
                $("#cliente-suggestion-box").hide();
                getCliente();
            }

            //Factura Compra
            // AJAX call for autocomplete 
            //var cliente = document.getElementById("nr_cliente").value;
            $(document).ready(function(){
                $("#nr_factura_venta_oficial").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"buscar_factura":$(this).val()},
                    beforeSend: function(){
                        $("#nr_factura_venta_oficial").css("background","#FFF no-repeat 165px");
                    },
                    success: function(data){
                        $("#factura-suggestion-box").show();
                        $("#factura-suggestion-box").html(data);
                        $("#nr_factura_venta_oficial").css("background","#FFF");
                    }
                    });
                });
            });

            //To select Factura Compra
            function selectFacturaVenta(val){
                $("#nr_factura_venta_oficial").val(val);
                $("#factura-suggestion-box").hide();
                getFacturaVenta();
            }

            //Medio Pago
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_medio_pago").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"buscar_medio_pago":$(this).val()},
                    beforeSend: function(){
                        $("#medio_pago").css("background","#FFF no-repeat 165px");
                    },
                    success: function(data){
                        $("#medio-pago-suggestion-box").show();
                        $("#medio-pago-suggestion-box").html(data);
                        $("#medio_pago").css("background","#FFF");
                    }
                    });
                });
            });

            //To select Medio Pago
            function selectMedioPago(val){
                $("#id_medio_pago").val(val);
                $("#medio-pago-suggestion-box").hide();
                getMedioPago();
            }

            //Clean the form
            function limpiarDetalle(){
                $('#nr_factura').val('');
                $('#nr_factura_venta_oficial').val('');
                $('#nr_nota_credito').val('');
                $('#nr_nota_credito_oficial').val(0);
                $('#nr_medio_pago').val('');
                $('#descripcion_medio_pago').val('');
                $('#monto_aplicado').val(0);
                $('#impuesto').val('');
                $('#descuento').val(0);
                $('#total_linea').val(0);
            }
    
            //Store Cabecera and Detalle
            function GuardarCabecera(){
                if (validarCabecera())
                {
                    var action = "GuardarCabecera";
                    var nr = document.getElementById("nr").value;
                    //alert("NR: " + nr);
                    var nr_cobranza = document.getElementById("nr_cobranza").value;
                    //alert("nr_cobranza: " + nr_cobranza);
                    var fecha_cobranza = document.getElementById("fecha_cobranza").value;
                    //alert("Fecha Pago: " + fecha_cobranza);
                    var nr_cliente = document.getElementById("nr_cliente").value; 
                    //alert("cliente: " + nr_cliente);  
                    var nr_sucursal =document.getElementById("nr_sucursal").value; 
                    //alert("Sucursal: " + nr_sucursal);            
                    var nr_caja = document.getElementById("nr_caja").value;
                    //alert("Caja: " + nr_caja);
                    var nr_moneda = document.getElementById("nr_moneda").value;
                    //alert("Moneda: " + nr_moneda);
                    var cotizacion_compra = document.getElementById("cotizacion_compra").value;
                    //alert("Cotizacion: " + cotizacion_compra);
                    var cotizacion_venta = document.getElementById("cotizacion_venta").value;
                    //alert("Cotizacion: " + cotizacion_venta);
                    var total_pago = document.getElementById("total_pago").value;
                    //alert("Total Pago: " + total_pago);
                    var obs = document.getElementById("obs").value;
                    //alert("OBS: " + obs);
                    var nr_cobrador = document.getElementById("nr_cobrador").value;
                    //alert("nr_cobrador: " + nr_cobrador);
                    var estado = document.getElementById("estado").value;
                    //alert("Estado: " + estado);
                    nr_user = <?php echo json_encode($nr_user) ?>;
                    //alert("User: " + nr_user);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_cobranza_ajax.php",
                        data: {"action":action,"nr":nr,"nr_cobranza":nr_cobranza,"fecha_cobranza":fecha_cobranza,"nr_cliente":nr_cliente,"nr_sucursal":nr_sucursal,"nr_caja":nr_caja,
                        "nr_moneda":nr_moneda,"cotizacion_compra":cotizacion_compra,"cotizacion_venta":cotizacion_venta,"total_pago":total_pago,
                        "nr_user":nr_user,"obs":obs,"nr_cobrador":nr_cobrador,"estado":estado}, 
                        dataType : 'json',
                        success: function(cabeceraData)
                        {
                            alert("Datos Confirmados");
                            //alert("Result Cabecera: " + cabeceraData);
                            //data = JSON.stringify(cabeceraData);
                            //alert("Result JSON: " + data);
                            /*var data = cabeceraData.split(",");
                            $('#nr_factura').val(data[0]);
                            $('#nr_nota_credito').val(data[1]);
                            $("#impuesto").val(data[2]);
                            $("#descripcion_medio_pago").val(data[3]);
                            $("#monto_aplicado").val(data[4]);
                            $("#nr_cliente").val(data[5]);*/
                        }
                    });
                    ListaDetallePagoOrden();
                    ListaDetalleAplicacionPago();
                }
            }

            function GenerarDetallePago(){
                //First store the Cabecera Detalle if not exist yet
                //GuardarCabecera();
                if (validarLineaPago())
                {
                    ListaDetallePagoOrden();
                    var action = "GenerarDetallePago";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                     var nr_medio_pago =document.getElementById("nr_medio_pago").value;
                    //alert("Data3: " + nr_medio_pago);
                    var monto_pago = document.getElementById("monto_pago").value;
                    //alert("Data4: " + precio);
                    var total_linea = document.getElementById("total_linea").value;
                    //alert("Data5: " + total_linea);
                    var obs_detalle = document.getElementById("obs_detalle").value;
                    //alert("Data5: " + obs_detalle);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_cobranza_ajax.php",
                        data: {"action":action,"nr":nr,"nr_medio_pago":nr_medio_pago,"monto_pago":monto_pago,"total_linea":total_linea,"obs_detalle":obs_detalle},
                        dataType : 'json',
                        success: function(detalleResultado)
                        {
                            limpiarDetalle();
                            //alert("Medio de Pago agregado");
                            //alert("Result Detalle: " + detalleResultado);
                            // Clear the form
                            //$("#detallerow")[0].reset();
                            var data = detalleResultado.split(",");
                            $('#nr_medio_pago').val(data[1]);
                            $('#descripcion_medio_pago').val(data[2]);
                            $('#monto_pago').val(data[3]);
                            $('#total_linea').val(data[4]);
                            $('#obs_detalle').val(data[5]);

                            ListaDetallePagoOrden();
                            ListaDetalleAplicacionPago();
                            GetTotalOrden();
                            getCantidadDetalle();
                        }
                    });
                }
            }

            function GenerarDetalleAplicacionPago(){
                //First store the Cabecera Detalle if not exist yet
                //GuardarCabecera();
                if (validarLineaAplicacionPago())
                {
                    ListaDetalleAplicacionPago();
                    var action = "GenerarDetalleAplicacionPago";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    var nr_factura_venta = document.getElementById("nr_factura_venta").value;
                    //alert("Data1: " + nr_factura_venta);
                    var nr_nota_credito_oficial = document.getElementById("nr_nota_credito_oficial").value;
                    //alert("Data2: " + nr_nota_credito_oficial);
                    var monto_aplicado = document.getElementById("monto_aplicado").value;
                    //alert("Data4: " + precio);
                    var total_linea = document.getElementById("total_linea_aplicado").value;
                    //alert("Data5: " + total_linea);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_cobranza_ajax.php",
                        data: {"action":action,"nr":nr,"nr_factura_venta":nr_factura_venta,"nr_nota_credito_oficial":nr_nota_credito_oficial,
                        "monto_aplicado":monto_aplicado,"total_linea":total_linea},
                        dataType : 'json',
                        success: function(detalleResultado)
                        {
                            limpiarDetalle();
                            //alert("Pago Aplicado");
                            //alert("Result Detalle: " + detalleResultado);
                            // Clear the form
                            //$("#detallerow")[0].reset();
                            var data = detalleResultado.split(",");
                            $('#nr_factura').val(data[0]);
                            $('#nr_factura_venta_oficial').val(data[1]);
                            $('#nr_nota_credito').val(data[2]);
                            $('#nr_nota_credito_oficial').val(data[3]);
                            $('#nr_medio_pago').val(data[4]);
                            $('#descripcion_medio_pago').val(data[6]);
                            $('#monto_aplicado').val(data[7]);
                            $('#total_linea_aplicado').val(data[8]);
                            $('#saldo_factura').val(data[8]);

                            ListaDetallePagoOrden();
                            ListaDetalleAplicacionPago();
                            GetTotalOrden();
                            getCantidadDetalle();
                        }
                    });
                }
            }

            function ConfirmarOrden(){
                //First store the Cabecera Detalle if not exist yet
                var action = "ConfirmarOrden";
                $.ajax({
                    type: "POST",
                    url: "../ajax/guardar_cobranza_ajax.php",
                    data: {"action":action},
                    dataType : 'json',
                    success: function(OrdenResultado)
                    {
                        alert("Result Orden: " + OrdenResultado);
                    }
                });
            }

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
            //To show the list of items in Detalle Pago
            function getCantidadDetalle(){
                var orden = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/cobranza_ajax.php",
                    data: {"orden":orden},
                    dataType : 'json',
                    success: function(data){
                        //alert("Result data: " + data);
                        var data = data.split(",");
                        $('#cantidad_pago').val(data[0]);
                        $('#cantidad_aplicacion').val(data[1]);
                    }
                });
            }

            //Check if the Form has at least one detail before Confirm
            $('#cobranza_form').submit(function(){
                getCantidadDetalle();
                var cantidad_pago = document.getElementById("cantidad_pago").value;
                var cantidad_aplicacion = document.getElementById("cantidad_aplicacion").value;
                var diferencia = document.getElementById("diferencia").value;
                //alert("Detalle: " + cantidad_detalle);
                if(cantidad_pago < 1)
                {
                    alert("El Recibo debe tener por lo menos un pago.");
                    return false;
                }else if(cantidad_aplicacion < 1){
                    alert("El Recibo debe tener por lo menos una factura aplicada.");
                    return false;
                }else if(diferencia > 0){
                    alert("El monto de pago y el monto aplicado no coinciden.");
                    return false;
                }else{
                    return true;
                }
            });
        </script>
        <header>
		  <h1>Recibo</h1>	
        </header>
        <article>
        <div>
            <table id="cabecera" class="cabecera">
            <tr>          
                <!--Column1-->
                <th><span>Orden #</span></th>
                <p hidden><input type="text" id="estado" name="estado" value="<?php echo $estado;?>"></p>
                <td><span><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></span></td>
                <!--Column2-->
                <th><span>Usuario</span></th>
                <td><span><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly></span></td>
            </tr>
            <tr>
                <!--Column1-->
                <th><span>Fecha</span></th>
                <td><span><input type="date" id="fecha_cobranza" name="fecha_cobranza" value="<?php echo date("Y-m-d");?>" class="boxes" required></span></td>
                <!--Column2-->
                <th><span>Sucursal</span></th>
                <td><span>
                    <select id="nr_sucursal" name="nr_sucursal" onchange="getCaja();" required class="sucursal" >
                        <?php 
                            $sucursal_result = $cobranza->get_sucursal();//We get all the results from the table
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
                <th><span>Nr. Oficial</span></th>
                <td><span><input type="text" id="nr_cobranza" name="nr_cobranza" value="<?php echo $nr_cobranza;?>"></span></td>
                <!--Column2-->
                <th><span>Caja</span></th>
                <td><span><!--Here we display the values according to the Sucursal selected-->
                    <select id="nr_caja" name="nr_caja" required class="caja" >
                    <option selected="selected">Seleccione:</option>;
                    </select><p>
                </span></td>
            </tr>
            <tr>
                <!--Column1-->
                <th><span>Cod. Cliente</span></th>
                <p hidden><input type="text" id="nr_cliente" name="nr_cliente" value="<?php echo $nr_cliente;?>"></p>
                <td>
                <div class="ClienteSearch">
                    <span><input type="text" id="id_cliente" name="id_cliente" placeholder="Buscar..." value="<?php echo $id_cliente;?>" onchange="getProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required></span>
                    <span><input type="text" id="descripcion_cliente" name="descripcion_cliente" value="<?php echo $descripcion_cliente;?>" class="dobleboxes" readonly disabled></span>
                    <div id="cliente-suggestion-box"></div>
                </div>
                </td>
                <!--Column2-->
                <th><span>Moneda</span></th>
                <td><span>
                    <select id="nr_moneda" name="nr_moneda" required class="boxes" onChange= "mostrarCotizacion(this.value)">
                        <?php 
                            $moneda_result = $cobranza->get_monedas();//We get all the results from the table
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
                </span></td>
            </tr>
            <tr>
                <!--Column1-->
                <th><span>Cobrador</span></th>
                <td><span>
                    <select id="nr_cobrador" name="nr_cobrador" class="boxes" required>
                        <option value="">Seleccione:</option>';
                        <?php 
                            $cobrador_result = $cobranza->get_cobrador();//We get all the results from the table
                            foreach ($cobrador_result as $row){
                            $cobrador_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_cobrador==$cobrador_nr) echo 'selected="selected"';
                            echo '>'.$row['nombre_apellido'].'</option>';
                            }
                        ?>
                    </select>
                </span></td>
                <!--Column2-->
                <th><span>Obs.</span></th>
                <td><span><input type="text" id="obs" name="obs" value="<?php echo $obs ;?>" class="boxes"></span></td>                
            </tr>
        </table>
    </div>
    <div>
        <input type="button" value="Guardar" id="guardar_cabecera" name="guardar_cabecera" class="add_detail" onclick="GuardarCabecera()">
    </div>
    <div>
        <p class = "titulos">Cobranzas</p>
        <table id="detalle"  class="detalle">
			<thead>
				<tr>
                    <th>Medio de Pago</th>
					<th>Monto Pago</th>
                    <th>Obs.</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody class = "detallerow">
				<tr>
					<!--Javascript result section-->
                    <p hidden><input type="text" id="nr_medio_pago" name="nr_medio_pago" value="<?php echo $nr_medio_pago;?>"></p>
                    <p hidden><input type="number" id="detalle_total_aplicado" name="detalle_total_aplicado" value="<?php echo $detalle_total_aplicado;?>">
                    <p hidden><input type="number" id="detalle_total_pago" name="detalle_total_pago" value="<?php echo $detalle_total_pago;?>">
                    <p hidden><input type="number" id="cantidad_pago" name="cantidad_pago" value="<?php echo $cantidad_pago;?>">
                    <p hidden><input type="number" id="cantidad_aplicacion" name="cantidad_aplicacion" value="<?php echo $cantidad_aplicacion;?>">
                    <td>
                    <div class="MedioPagoSearch">
                        <input type="text" id="id_medio_pago" name="id_medio_pago" placeholder="Buscar..." value="<?php echo $id_medio_pago;?>" onchange="getMedioPago();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                        <input type="text" id="descripcion_medio_pago" name="descripcion_medio_pago" value="<?php echo $descripcion_medio_pago;?>" class="dobleboxes" readonly disabled>
                        <div id="medio-pago-suggestion-box"></div>
                    </div>
                    </td>
                    <td><input class="detallerow" type="number" id="monto_pago" step="any" name="monto_pago[]" value="<?php echo $monto_pago;?>" onchange="validarLineaPago()"></td>
                    <td><input class="detallerow" type="number" id="obs_detalle" step="any" name="obs_detalle[]" value="<?php echo $obs_detalle;?>" ></td>
                    <td><input class="detallerow" type="number" id="total_linea" step="any" name="total_linea[]" value="<?php echo $total_linea;?>" readonly ></td>
		        </tr>
        	</tbody>
		</table>
    </div>
    <div>
        <input type="button" value="Agregar" id="agregar_detalle" name="agregar_detalle" class="add_detail" onclick="GenerarDetallePago()">
    </div>
    <!-- Lista Detalle section -->
    <p>
        <div class="DetalleSection">
            <div id="detalle-pago-list"></div>
        </div>
    <p>
    <div>
        <p class = "titulos">Aplicaciones de Cobranzas</p>
        <table id="detalle_aplicacion"  class="detalle_aplicacion">
        <thead>
            <tr>
                <th>Factura Venta Nro.</th>
                <th>Nota de Credito Nro.</th>
                <th>Saldo Factura</th>
                <th>Monto aplicado</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody class = "detalleaplicacionrow">
            <tr>
                <td>
                    <div class="FacturaCompraSearch">
                        <p hidden><input class="detalleaplicacionrow" type="text" id="nr_factura_venta" name="nr_factura_venta[]" value="<?php echo $nr_factura_venta;?>"></p>
                        <input class="detalleaplicacionrow" type="text" id="nr_factura_venta_oficial" name="nr_factura_venta_oficial[]" placeholder="Buscar..." value="<?php echo $nr_factura_venta_oficial;?>" onchange="getFacturaVenta()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        <div id="factura-suggestion-box"></div>
                    </div>
                </td>
                <p hidden><input class="detalleaplicacionrow" type="text" id="nr_nota_credito" name="nr_nota_credito[]" value="<?php echo $nr_nota_credito;?>" readonly disabled>
                <td><input class="detalleaplicacionrow" type="text" id="nr_nota_credito_oficial" step="any" min="1" name="nr_nota_credito_oficial[]" value="<?php echo $nr_nota_credito_oficial;?>">
                <!--Javascript result section-->
                <td><input class="detalleaplicacionrow" type="number" id="saldo_factura" step="any" name="saldo_factura[]" value="<?php echo $saldo_factura;?>" readonly disabled></td>
                <td><input class="detalleaplicacionrow" type="number" id="monto_aplicado" step="any" name="monto_aplicado[]" value="<?php echo $monto_aplicado;?>" onchange="validarLineaAplicacionPago()"></td>
                <td><input class="detalleaplicacionrow" type="number" id="total_linea_aplicado" step="any" name="total_linea_aplicado[]" value="<?php echo $total_linea_aplicado;?>" readonly ></td>
            </tr>
        </tbody>
        </table>
    </div>
    <div>
        <input type="button" value="Aplicar" id="agregar_detalle_aplicacion" name="agregar_detalle_aplicacion" class="add_aplication_detail" onclick="GenerarDetalleAplicacionPago()">
    </div>
    <!-- Lista Detalle section -->
    <div class="DetalleAplicacionSection">
        <div id="detalle-aplicacion-list"></div>
    </div>

	<table class="balance">
		<tr>
			<th><span balance>Total Recibo</th>
			<td><input type="number" id="total_pago" name="total_pago" class="total_pago" value="<?php echo $total_pago;?>" readonly></td>
		</tr>
        <tr>
            <th><span balance>Total Aplicado</th>
            <td><input type="number" id="total_pago_aplicado" name="total_pago_aplicado" class="total_pago_aplicado" value="<?php echo $total_pago_aplicado;?>" readonly></td>
        </tr>
        <tr>
            <th><span balance>Diferencia</th>
            <td><input type="number" id="diferencia" name="diferencia" class="diferencia" value="<?php echo $diferencia;?>" readonly></td>
        </tr>
	</table>
	</article>
    <div class="form-actions">
        <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Confirmar" onclick="confirmarOrden()">
        <a class="btn" href="cobranza.php" onclick="return confirmarCancelar()";>Cancelar</a>
    </div>
    </form>
	</body>
</html>

<?php
    if (isset($_POST['action']))
    {
        $nr = $_POST['nr'];
        /*$query = "select * from detalle_cobranza DOC where DOC.nr = '$nr'";
        $query_result = $cobranza -> query($query);//We get all the results from the table
        @$qty_register = $cobranza -> rowCount($query);
        if ($qty_register > 0)
        {*/
            switch($_POST['action'])
            {
                case "Confirmar": 
                    $query = "select sum(total_linea) as total_pago from detalle_cobranza DC where DC.nr = '$nr'";
                    $update_result = $cobranza -> query($query);//We get all the results from the table
                    @$qty_register = $cobranza -> rowCount($query);
                    if($qty_register > 0){
                        foreach($update_result as $row):
                            $total_pago = $row['total_pago'];
                        endforeach;
                    }
                    $cobranza->update_cobranza($nr,$total_pago);
                    //Update the official number
                    $nr_recibo = substr($nr_factura, 9, 7);
                    $cobranza->query("update comprobantes set ultimo_nro_impreso = '$nr_recibo' where nr_sucursal = '$nr_sucursal' and aplicado_a='recibo'");
                    echo "<script>window.close()</script>";
                break;
            }
    }
?>