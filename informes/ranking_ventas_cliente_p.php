<?php
    require '../tablas/menu.php';
    
    require '../clases/formularios/factura_venta.class.php';
    $factura_venta = factura_venta::singleton();

    $page_name="ranking_ventas_cliente_p.php"; 

    $fecha_inicio=isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin=isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $nr_vendedor=isset($_POST['nr_vendedor']) ? $_POST['nr_vendedor'] : '';
    $id_vendedor=isset($_POST['id_vendedor']) ? $_POST['id_vendedor'] : '';
    $descripcion_vendedor=isset($_POST['descripcion_vendedor']) ? $_POST['descripcion_vendedor'] : '';
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_informes.css" />
<script src="../js/jquery.js"></script>
</head>
<body>
<form class="form-horizontal" action= "ranking_ventas_cliente_p.php" method="POST" name = "ranking_ventas_cliente_p" autocomplete="off">
<h2>Ranking de Venta por Cliente</h2>

<!-- AJAX section -->
<script>
   function getVendedor(){
        var vendedor = document.getElementById("id_vendedor").value;
        $.ajax({
            type: "POST",
            url: "../ajax/orden_venta_ajax.php",
            data: {"vendedor":vendedor},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                $('#nr_vendedor').val(data[0]);
                $('#descripcion_vendedor').val(data[1]);
            }
        });
    }

    //SEARCH SECTION
    //Vendedor
    // AJAX call for autocomplete 
    $(document).ready(function(){
        $("#id_vendedor").keyup(function(){
            $.ajax({
                type: "POST",
                url: "../ajax/orden_venta_ajax.php",
                data:{"buscar_vendedor":$(this).val()},
                beforeSend: function(){
                    $("#id_vendedor").css("background","#FFF no-repeat 165px");
                },
            success: function(data){
                $("#vendedor-suggestion-box").show();
                $("#vendedor-suggestion-box").html(data);
                $("#id_vendedor").css("background","#FFF");
            }
            });
        });
    });

    //To select vendedor
    function selectVendedor(val) {
        $("#id_vendedor").val(val);
        $("#vendedor-suggestion-box").hide();
        getVendedor();
    }

</script>
<center>
    <b><label class="control-label">Rango de Fechas:</label></b><p><p>

    <b><label class="control-label">Fecha Inicio</label></b>
    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>
    <b><label class="control-label">Fecha Fin</label></b>
    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo date("Y-m-d");?>" class="boxes" required><p>
    <div class="VendedorSearch">
        <label class="control-label">Vendedor</label>
        <input type="hidden" id="nr_vendedor" name="nr_vendedor" value="<?php echo $nr_vendedor;?>" autofocus>
        <input type="text" id="id_vendedor" name="id_vendedor" placeholder="Buscar..." value="<?php echo $id_vendedor;?>" onchange="getVendedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="boxes2">
        <input type="text" id="descripcion_vendedor" name="descripcion_vendedor" value="<?php echo $descripcion_vendedor;?>" readonly disabled class="boxes3">
        <div id="vendedor-suggestion-box" class="suggestion-box"></div>
    </div><p>

    <p align="center"><input type="submit" class="btn-success" id="action" name="action" value ="Listar">

    <?php
        if (isset($_POST['action']))
        {
            @$fecha_inicio = $_POST['fecha_inicio'];
            @$fecha_fin = $_POST['fecha_fin'];
            @$vendedor = $_POST['vendedor'];
            echo '<script>window.location.assign("ranking_ventas_cliente.php?fecha_inicio='.$fecha_inicio.'&fecha_fin='.$fecha_fin.'&vendedor='.$nr_vendedor.'")</script>';
        }
    ?>
</center>
</form>
</body>
</html>