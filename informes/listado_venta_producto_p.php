<?php
    require '../tablas/menu.php';
    
    require '../clases/formularios/factura_venta.class.php';
    $factura_venta = factura_venta::singleton();

    $page_name="listado_venta_producto_p.php"; 

    $fecha_inicio=isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin=isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $nr_producto=isset($_POST['nr_producto']) ? $_POST['nr_producto'] : '';
    $id_producto=isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
    $descripcion_producto=isset($_POST['descripcion_producto']) ? $_POST['descripcion_producto'] : '';
    $nr_cliente=isset($_POST['nr_cliente']) ? $_POST['nr_cliente'] : '';
    $id_cliente=isset($_POST['id_cliente']) ? $_POST['id_cliente'] : '';
    $descripcion_cliente=isset($_POST['descripcion_cliente']) ? $_POST['descripcion_cliente'] : '';
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_informes.css" />
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action= "listado_venta_producto_p.php" method="POST" name = "listado_venta_producto_p" autocomplete="off">
<h2>Listado de Venta por Producto</h2>
<!-- AJAX section -->
<script>
    function getProducto(){
        var producto = document.getElementById("id_producto").value;
        $.ajax({
            type: "POST",
            url: "../ajax/orden_venta_ajax.php",
            data: {"producto":producto},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(";");
                $('#nr_producto').val(data[0]);
                $('#descripcion_producto').val(data[1]);
            }
        });
    }

    //SEARCH SECTION
    //producto
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

    //To select producto
    function selectProducto(val) {
        $("#id_producto").val(val);
        $("#producto-suggestion-box").hide();
        getProducto();
    }

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
</script>
<center>
    <b><label class="control-label">Rango de Fechas:</label></b><p><p>

    <b><label class="control-label">Fecha Inicio</label></b>
    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>
    <b><label class="control-label">Fecha Fin</label></b>
    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>

    <div class="productoSearch">
        <label class="control-label">Producto</label>
        <input type="hidden" id="nr_producto" name="nr_producto" value="<?php echo $nr_producto;?>" autofocus>
        <input type="text" id="id_producto" name="id_producto" placeholder="Buscar..." value="<?php echo $id_producto;?>" onchange="getProducto();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="boxes2" required>
        <input type="text" id="descripcion_producto" name="descripcion_producto" value="<?php echo $descripcion_producto;?>" readonly disabled class="boxes3">
        <div id="producto-suggestion-box" class="suggestion-box"></div>
    </div><p>
    <div class="ClienteSearch">
        <label class="control-label">Cliente</label>
        <input type="hidden" id="nr_cliente" name="nr_cliente" value="<?php echo $nr_cliente;?>" autofocus>
        <input type="text" id="id_cliente" name="id_cliente" placeholder="Buscar..." value="<?php echo $id_cliente;?>" onchange="getCliente();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="boxes2">
        <input type="text" id="descripcion_cliente" name="descripcion_cliente" value="<?php echo $descripcion_cliente;?>" readonly disabled class="boxes3">
        <div id="cliente-suggestion-box" class="suggestion-box"></div>
    </div><p>
    <p align="center"><input type="submit" class="btn-success" id="action" name="action" value ="Listar">

    <?php
        if (isset($_POST['action']))
        {
            @$producto = $_POST['nr_producto'];
            @$cliente = $_POST['nr_cliente'];
            echo '<script>window.location.assign("listado_venta_producto.php?fecha_inicio='.$fecha_inicio.'&fecha_fin='.$fecha_fin.'&producto='.$nr_producto.'&cliente='.$nr_cliente.'")</script>';
        }
    ?>
</center>
</form>
</body>
</html>