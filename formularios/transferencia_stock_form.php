<?php
	require '../login/control_login.php'; /*Check if the user is logged into the system*/
	require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
    require '../css/nombre_empresa.html'; /*Show the Company name*/

	require '../clases/formularios/transferencia_stock.class.php';
    $transferencia_stock = transferencia_stock::singleton();

    $page_name="transferencia_stock_form.php"; 

	//Declaring variables
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $fecha_transferencia=isset($_POST['fecha_transferencia']) ? $_POST['fecha_transferencia'] : '';
    $usuario=isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $nr_sucursal=isset($_POST['nr_sucursal']) ? $_POST['nr_sucursal'] : '';
    $nr_deposito_origen=isset($_POST['nr_deposito_origen']) ? $_POST['nr_deposito_origen'] : '';
    $nr_deposito_destino=isset($_POST['nr_deposito_destino']) ? $_POST['nr_deposito_destino'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $cantidad=isset($_POST['cantidad']) ? $_POST['cantidad'] : '';
    $nr_unidad=isset($_POST['nr_unidad']) ? $_POST['nr_unidad'] : '';
    $id_unidad=isset($_POST['id_unidad']) ? $_POST['id_unidad'] : '';
    $costo=isset($_POST['costo']) ? $_POST['costo'] : 0;
    $total_linea=isset($_POST['total_linea']) ? $_POST['total_linea'] : 0;
    $total_transferencia=isset($_POST['total_transferencia']) ? $_POST['total_transferencia'] : 0;
    $sub_total=isset($_POST['sub_total']) ? $_POST['sub_total'] : 0;
    $total_general=isset($_POST['total_general']) ? $_POST['total_general'] : 0;
    $obs=isset($_POST['obs']) ? $_POST['obs'] : '';
    $cantidad_detalle=isset($_POST['cantidad_detalle']) ? $_POST['cantidad_detalle'] : '';
    $detalle_total_general=isset($_POST['detalle_total_general']) ? $_POST['detalle_total_general'] : 0;
    $detalle_iva_general=isset($_POST['detalle_iva_general']) ? $_POST['detalle_iva_general'] : 0;

    //Defining Order # checking the next value from the sequence
    $transferencia_result = $transferencia_stock->query("select nextval('cabecera_transferencia_stock_nr_seq')");
    //$transferencia_result = $transferencia_stock->query("select last_value from cabecera_transferencia_stock_nr_seq");
    foreach ($transferencia_result as $resultado) {        
        @$nr = $resultado['nextval'];
    }

    //Get the configuration for Compras forms
    $configuracion_result = $transferencia_stock->query("select * from configuracion");//We get the desired result from the table
    foreach ($configuracion_result as $resultado_config) {        
        @$cantidad_f_compra = $resultado_config['cantidad_f_compra'];
        @$moneda_defecto = $resultado_config['moneda_defecto'];
        //echo $cantidad_f_compra;
    }

    $usuario = $_SESSION['id_user'];
    $user_result = $transferencia_stock->query("select * from users where id_user ='$usuario'");//We get the desired result from the table
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
		<script src="../js/transferencia_stock_script.js"></script> 
	</head>
	<body>
    <form id= "transferencia_stock_form" class="form-horizontal" action="transferencia_stock_form.php" method="POST" name = "transferencia_stock_form" autocomplete = "off">    
        <!-- AJAX section -->
        <script>
            //To show the list of items in Detalle
            function GetTotalOrden(){
                var orden_nr = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"orden_nr":orden_nr},
                    dataType : 'json',
                    success: function(resultado){
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#detalle_total_general').val(data[0]);
                        //$('#detalle_iva_general').val(data[1]);
                    }
                });
            }

            //Valid the Cabecera before inserting in the Cabecera
            function validarCabecera(){
                var sucursal_cabecera = document.getElementById("nr_sucursal").value;
                var deposito_origen_cabecera = document.getElementById("nr_deposito_origen").value;
                var deposito_destino_cabecera = document.getElementById("nr_deposito_destino").value;
                if (sucursal_cabecera == "")
                {
                    alert("Favor elegir una Sucursal.");
                    return false;
                }else if (deposito_origen_cabecera == ""){
                    alert("Favor elegir un Deposito de Origen.");
                    return false;
                }else if (deposito_destino_cabecera == ""){
                    alert("Favor elegir un Deposito de Destino.");
                    return false;    
                }
                return true;
            }

            //Check the available Stock
            function validarStock(){
                var action = "VerificarStock";
                var nr_sucursal =document.getElementById("nr_sucursal").value;
                //alert("Data2: " + nr_sucursal);
                var nr_deposito_origen =document.getElementById("nr_deposito_origen").value;
                //alert("Data4: " + nr_sucursal_destino);
                var nr_producto = document.getElementById("nr_producto").value;
                //alert("Data5: " + nr_producto);
                var cantidad = document.getElementById("cantidad").value;
                //alert("Data5: " + cantidad);
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"action":action,"nr_sucursal":nr_sucursal,"nr_deposito_origen":nr_deposito_origen,"nr_producto":nr_producto}, 
                    dataType : 'json',
                    success: function(stockData)
                    {
                        //alert("Result Cabecera: " + stockData);
                        //data = JSON.stringify(stockData);
                        //alert("Result JSON: " + data);
                        var data = stockData.split(",");
                        $('#stock_actual').val(data[0]);
                    }
                });
            }

            //Valid the line before inserting in the Detalle
            function validarLinea(){
                var cantidad_transferencia = document.getElementById("cantidad").value;
                var precio_transferencia = document.getElementById("costo").value;
                var stock_actual = parseFloat(document.getElementById("stock_actual").value);
                //alert("El stock disponible es de = " + stock_actual );
                if (cantidad_transferencia <= 0)
                {
                    alert("La cantidad debe ser mayor a cero.");
                    return false;
                }else if (cantidad_transferencia > stock_actual){
                    alert("El stock disponible es de = " + stock_actual +", no se puede confirmar");
                    return false;
                }else if (precio_transferencia <= 0){
                    alert("El costo del producto debe ser mayor a cero.");
                    return false;
                }
                return true;
            }

            //If the button Cancelar is pressed or if you leave or refresh the page
            function confirmarCancelar()
            {
                var cancelar;
                //cancelar.returnValue = 'Are you sure?';
                cancelar=confirm("¿Deseas dejar de cargar la Transferencia de Stock?");
                if (cancelar)
                {
                    var action = "EliminarTransferencia";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_transferencia_stock_ajax.php",
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
            function ListaProductospedido(){
                var listar_transferencia = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"listar_transferencia":listar_transferencia},
                    success: function(data){
                        //alert("Result data: " + data);
                        $("#detalle-list").show();
                        $("#detalle-list").html(data);
                        $("listar_transferencia").css("background","#FFF");
                    }
                });
            }

            function onClick(e) {
                ListaProductospedido();
                GetTotalOrden();
                getCantidadDetalle();
            }

            if (window.addEventListener) {
                document.addEventListener('click', onClick);
            }

            //For dependent Selects Sucursal 
            $(document).ready(function()
            {
                $(".sucursal").change(function()
                {
                    var sucursal=$(this).val();
                    $.ajax
                    ({
                        type: "POST",
                        url: "../ajax/transferencia_stock_ajax.php",
                        data: {"sucursal":sucursal},
                        cache: false,
                        success: function(html)
                        {
                            $(".deposito_origen").html(html);
                        } 
                    });
              });
            });

            function getDepositoDestino(value){
                var deposito_origen = value;
                var sucursal_actual =document.getElementById("nr_sucursal").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"deposito_origen":deposito_origen,"sucursal_actual":sucursal_actual},
                    cache: false,
                    success: function(html)
                    {
                        $(".deposito_destino").html(html);
                    }
                });
            }

            function getProducto(){
                var producto = document.getElementById("id_producto").value;
                //alert(producto);
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"producto":producto},
                    dataType : 'json',
                    success: function(resultado)
                    {
                        //alert("Result: " + resultado);
                        var data = resultado.split(",");
                        $('#nr_producto').val(data[0]);
                        $('#descripcion_producto').val(data[1]);
                        $("#impuesto").val(data[2]);
                        $("#nr_unidad").val(data[3]);
                        $("#id_unidad").val(data[4]);
                        $("#costo").val(data[5]);
                    }
                });
            }

            //SEARCH SECTION
            //Producto
            // AJAX call for autocomplete 
            $(document).ready(function(){
                $("#id_producto").keyup(function(){
                    $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
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
                $('#costo').val(0);
                $('#total_linea').val(0);
            }
    
            //Store Cabecera and Detalle
            function GenerarCabecera(){
                if (validarCabecera())
                {
                    var action = "GenerarCabecera";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    var fecha_transferencia = document.getElementById("fecha_transferencia").value;
                    //alert("Data1: " + fecha_transferencia);
                    var nr_sucursal =document.getElementById("nr_sucursal").value;
                    //alert("Data2: " + nr_sucursal);
                    var nr_deposito_origen =document.getElementById("nr_deposito_origen").value;
                    //alert("Data4: " + nr_sucursal_destino);
                    var nr_deposito_destino = document.getElementById("nr_deposito_destino").value;
                    //alert("Data5: " + nr_deposito_destino);
                    var total_transferencia = document.getElementById("total_transferencia").value;
                    //alert("Data12: " + total_transferencia);
                    var obs = document.getElementById("obs").value;
                    //alert("Data13: " + obs);
                    nr_user = <?php echo json_encode($nr_user) ?>;
                    //var data = new Array();
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_transferencia_stock_ajax.php",
                        data: {"action":action,"nr":nr,"fecha_transferencia":fecha_transferencia,"nr_sucursal":nr_sucursal,"nr_deposito_origen":nr_deposito_origen,
                        "nr_deposito_destino":nr_deposito_destino,"total_transferencia":total_transferencia,"nr_user":nr_user,"obs":obs}, 
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
                            $("#costo_lista").val(data[4]);
                            $("#nr_proveedor").val(data[5]);*/
                        }
                    });
                    ListaProductospedido();
                }
            }

            function GenerarDetalle(){
                //First store the Cabecera Detalle if not exist yet
                //GenerarCabecera();
                if (validarLinea())
                {
                    ListaProductospedido();
                    var action = "GenerarDetalle";
                    var nr = document.getElementById("nr").value;
                    //alert("Data0: " + nr);
                    var nr_producto = document.getElementById("nr_producto").value;
                    //alert("Data1: " + nr_producto);
                    var cantidad = document.getElementById("cantidad").value;
                    //alert("Data2: " + cantidad);
                    var nr_unidad = document.getElementById("nr_unidad").value;
                    //alert("Data3: " + nr_unidad);
                    var costo = document.getElementById("costo").value;
                    //alert("Data4: " + costo);
                    var total_linea = document.getElementById("total_linea").value;
                    //alert("Data5: " + total_linea);
                    $.ajax({
                        type: "POST",
                        url: "../ajax/guardar_transferencia_stock_ajax.php",
                        data: {"action":action,"nr":nr,"nr_producto":nr_producto,"cantidad":cantidad,"nr_unidad":nr_unidad,"costo":costo,"total_linea":total_linea},
                        dataType : 'json',
                        success: function(detalleResultado)
                        {
                            limpiarDetalle();
                            //alert("Result Detalle: " + detalleResultado);
                            //alert("Producto Agregado");
                            // Clear the form
                            //$("#detallerow")[0].reset();
                            var data = detalleResultado.split(",");
                            $('#nr_producto').val(data[1]);
                            $('#id_producto').val(data[2]);
                            $('#descripcion_producto').val(data[3]);
                            $('#cantidad').val(data[4]);
                            $('#nr_unidad').val(data[5]);
                            $('#id_unidad').val(data[6]);
                            $('#costo').val(data[7]);
                            $('#total_linea').val(data[8]);

                            ListaProductospedido();
                            GetTotalOrden();
                            getCantidadDetalle();
                        }
                    });
                }
            }

            /*function createjson() {
                objectArray = [];
                
                    var arrayID = $(this).attr("id");
                    //alert (arrayID);
                    var arrayValue = $(this).val();
                    //alert (arrayValue);

                    detallearray = {}
                    detallearray ["arrayID"] = arrayID;
                    detallearray ["arrayValue"] = arrayValue;

                    objectArray.push(detallearray);
                });

                console.log(objectArray);
                var tbl = $('table#detalle tr').get().map(function(row) {
                  return $(row).find('td').get().map(function(cell) {
                    return $(cell).html();
                  });
                });

                var objectArray = [];
                $('#detalle').find('tbody tr').each(function(){
                    var detallearray = {},
                        $td = $(this).find('td'),
                        key = $td.eq(0).text(),
                        //val = parseInt( $td.eq(2).text(), 10 );
                        val = $td.eq(0).text(),
                    detallearray[key] = val;
                    objectArray.push(detallearray);
                });

                var tbl=$("<table/>").attr("id","mytable");
                $("#div1").append(tbl);
                for(var i=0;i<objectArray.length;i++)
                {
                    var tr="<tr>";
                    var td1="<td>"+objectArray[i]["key"]+"</td>";
                    var td2="<td>"+objectArray[i]["val"]+"</td>";
                   $("#mytable").append(tr+td1+td2); 
                }


                /*var jsonData = JSON.parse(jsonObj);
                for (var i = 0; i < jsonData.counters.length; i++) {
                    var counter = jsonData.counters[i];
                    console.log(counter.id_producto);
                }*/
            //}

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

             //To show the list of items in Detalle
            function getCantidadDetalle(){
                var transferencia = document.getElementById("nr").value;
                $.ajax({
                    type: "POST",
                    url: "../ajax/transferencia_stock_ajax.php",
                    data: {"transferencia":transferencia},
                    dataType : 'json',
                    success: function(data){
                        //alert("Result data: " + data);
                        var data = data.split(",");
                        $('#cantidad_detalle').val(data[0]);
                    }
                });
            }

            //Check if the Form has at least one detail before Confirm
            $('#transferencia_stock_form').submit(function() {
                getCantidadDetalle();
                var cantidad_detalle = document.getElementById("cantidad_detalle").value;
                //alert("Detalle: " + cantidad_detalle);
                if(cantidad_detalle < 1)
                {
                    alert("La Transferencia debe tener por lo menos un producto.");
                    return false;
                }else {
                    return true;
                }
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
		  <h1>Transferencia Stock</h1>	
        </header>
        <article>
        <!--Javascript result section-->
        <div>
        <p hidden><input class="detallerow" type="number" id="nr_unidad" name="nr_unidad[]" value="<?php echo $nr_unidad;?>">
        <p hidden><input type="number" id="total_transferencia" name="total_transferencia[]" value="<?php echo $total_transferencia;?>">
        <p hidden><input type="text" id="stock_actual" name="stock_actual[]" value="<?php echo $stock_actual;?>">
        <p hidden><input type="text" id="cantidad_detalle" name="cantidad_detalle" value="<?php echo $cantidad_detalle;?>">
        <p hidden><input type="number" id="detalle_total_general" name="detalle_total_general" value="<?php echo $detalle_total_general;?>">
        <p hidden><input type="number" id="detalle_iva_general" name="detalle_iva_general" value="<?php echo $detalle_iva_general;?>">
        </div>
        
		<table class="cabecera">
			<tr>          
                <!--Column1-->
				<th>Transferencia Stock #</th>
				<td><input type="number" id="nr" name="nr" value="<?php echo $nr;?>" readonly></td>
                <!--Column2-->
				<th>Usuario</th>
				<td><input type="text" id="usuario" name="usuario" value="<?php echo $usuario;?>" readonly></td>
			</tr>
			<tr>
                <!--Column1-->
				<th>Fecha</th>
                <td><input type="date" id="fecha_transferencia" name="fecha_transferencia" value="<?php echo date("Y-m-d");?>" class="boxes" required></td>
                <!--Column2-->

			</tr>
			<tr>
                <!--Column1-->
                <th>Sucursal</th>
                <td>
                    <select id="nr_sucursal" name="nr_sucursal" required class="sucursal">
                        <option value="">Seleccione:</option>';
                        <?php 
                            $sucursal_result = $transferencia_stock->get_sucursal();//We get all the results from the table
                            foreach ($sucursal_result as $row) {
                            $sucursal_nr = $row['nr'];    
                            echo '<option value="'.$row['nr'].'"';
                            if ($nr_sucursal==$sucursal_nr) echo 'selected="selected"';
                            echo '>'.$row['descripcion'].'</option>';
                        }?>
                    </select><p>
                </td>
                <!--Column2-->
                
            </tr>
			<tr>
                <!--Column1-->
                <th>Deposito Stock Origen</th>
                <td><!--Here we display the values according to the Sucursal selected-->
                    <select id="nr_deposito_origen" name="nr_deposito_origen" required class="deposito_origen" onchange="getDepositoDestino(this.value)">
                    <option selected="selected">Seleccione:</option>;
                    </select><p>
                </td>
                <!--Column2-->
                <th>Deposito Stock Destino</th>
                <td><!--Here we display the values according to the Sucursal selected-->
                    <select id="nr_deposito_destino" name="nr_deposito_destino" required class="deposito_destino" >
                    <option selected="selected">Seleccione:</option>;
                    </select><p>
                </td>
			</tr>
			<tr>
                <!--Column1-->
				
                <!--Column2-->
                <th>Obs.</th>
                <td><input type="text" id="obs" name="obs" value="<?php echo $obs ;?>" class="boxes"></td>                
			</tr>
		</table>
        <div id="hideContent" style="display: none;">
        </div>
        <div>
        <input type="button" value="Guardar" id="guardar_cabecera" class="add_detail" onclick="GenerarCabecera()">
        </div>
        <table id="detalle"  class="detalle">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Cant.</th>
                    <th>Unid.Med.</th>
					<th>Costo Gs.</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody class = "detallerow">
				<tr>
					<td>
                    <div class="ProductoSearch">
                        <p hidden><input class="detallerow" type="text" id="nr_producto" name="nr_producto[]" value="<?php echo $nr_producto;?>"></p>
                        <input class="detallerow" type="text" id="id_producto" name="id_producto" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto()" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        <div id="producto-suggestion-box"></div>
                    </div>
                    </td>
                    <td><input class="detallerow" type="text" id="descripcion_producto" name="descripcion_producto[]" value="<?php echo $descripcion_producto;?>" readonly disabled></td>
					<td><input class="detallerow" type="number" id="cantidad" step="any" name="cantidad[]" value="<?php echo $cantidad;?>" onchange="validarStock()"></td>
                    <td><input class="detallerow" type="text" id="id_unidad" name="id_unidad[]" value="<?php echo $id_unidad;?>" readonly disabled></td>
					<td><input class="detallerow" type="number" id="costo" step="any" name="costo[]" value="<?php echo number_format(floatval($costo),0,",",".");?>" readonly disabled></td>
					<td><input class="detallerow" type="number" id="total_linea" step="any" name="total_linea[]" value="<?php echo number_format(floatval($total_linea),0,",",".");?>" readonly ></td>
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
			<table class="balance">
				<tr>
					<th><span balance>Total General</th>
					<td><input type="number" id="total_general" name="total_general" value="<?php echo number_format(floatval($total_general),0,",",".");?>" readonly></td>
				</tr>
			</table>
		</article>
		<aside>
			<div contenteditable>
			</div>
		</aside>
        <div class="form-actions">
            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Confirmar">
            <a class="btn" href="transferencia_stock.php" onclick="return confirmarCancelar()";>Cancelar</a>
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
                $obj = $_POST['data'];
                echo
                $nr = $_POST['nr'];
                $query = "select sum(total_linea) as total_transferencia from detalle_transferencia_stock DTS where DTS.nr = '$nr'";
                $update_result = $transferencia_stock -> query($query);//We get all the results from the table
                @$qty_register = $transferencia_stock -> rowCount($query);
                
                if($qty_register > 0){
                    foreach($update_result as $row):
                        $total_transferencia = $row['total_transferencia'];
                    endforeach;
                }

                $transferencia_stock->update_transferencia_stock($nr,$total_transferencia);
                echo "<script>window.close()</script>";
            break;
        }
    }
?>