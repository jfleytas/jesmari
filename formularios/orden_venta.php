<?php  
$page_name="orden_venta2.php"; 

$full_page_name=$page_name;

//header("Refresh:10; url=$page_name");

require '../tablas/menu.php';

require '../clases/formularios/orden_venta.class.php';
$orden_venta = orden_venta::singleton();

//Set the limit of records per page
$limit = 500;  
if (isset($_GET["page"])) 
  {
  $page  = $_GET["page"]; 
  }
else
  { 
  $page=1;
  }; 
//Set the offset 
$start_from = ($page-1) * $limit; 
//Set other variables for the pagination
$previous_page = $page - 1;
$next_page = $page + 1;
$adjacents = "2";

//Order query
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'nr';  
              
$sort_order = 'desc';  
if(isset($_GET['sort_by']))  
{  
    if($_GET['sort_by'] == 'asc')  
    {  
        $sort_order = 'desc';  
    } else{  
        $sort_order = 'asc';  
    }
    $full_page_name=$full_page_name."?sort_by=".$sort_order; 
}  
          
switch($sort)  
{  
    case 'nr':  
        $order_by = 'nr';  
    break; 
    case 'fecha_orden':  
        $order_by = 'fecha_orden';  
    break;  
    case 'descripcion_cliente':  
        $order_by = 'descripcion_cliente';  
    break;
    case 'moneda_descripcion':  
        $order_by = 'moneda_descripcion';  
    break;   
    case 'total_factura':  
        $order_by = 'total_factura';  
    break;  
    $full_page_name=$full_page_name."?sort=".$order_by;     
} 
//echo $full_page_name;
  
$query = "select OV.nr, OV.fecha_orden, C.razon_social descripcion_cliente, M.descripcion moneda_descripcion, OV.total_orden 
    from cabecera_orden_venta OV join clientes C on OV.nr_cliente = C.nr join moneda M on OV.nr_moneda = M.nr";

$qty_register = $orden_venta -> rowCount($query); 
//echo  $qty_register;    

$query = $query . " order by $sort $sort_order limit " . $limit . " offset " . $start_from; 
//echo $query;
$total_result = $orden_venta->query($query);//We get all the results from the table

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>-->
  <link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
  <script src="../js/jquery.js"></script>
</head>
<body>
<body>
<form class="form-horizontal" action= "orden_venta.php" method="POST" name = "orden_venta">
<h2>Orden de venta</h2>
<script>
    function pop_up(url){
        window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no') 
    }

    function VerificarFactura(value1,value2){
        var orden_estado = value1;
        //alert("Data0: " + value1);
        var accion = value2;
        //alert("Data1: " + value2);
        $.ajax({
            type: "POST",
            url: "../ajax/orden_venta_ajax.php",
            data: {"orden_estado":orden_estado},
            dataType : 'json',
            success: function(resultado)
            {
                //alert("Result: " + resultado);
                var data = resultado.split(",");
                var factura = data[1];
                if (factura == 1){
                    alert("La Orden de venta Nro. " + orden_estado + " ya fue facturada");
                    window.location.href = "orden_venta.php";
                }else if (accion=="1"){
                    url = "editar_orden_venta_form.php?orden="+orden_estado;
                    window.location.href=url;
                }else if (accion=="0"){
                    url = "factura_venta_form.php?orden="+orden_estado;
                    window.location.href=url;
                }
            }
        });
    }

$(document).ready(function() 
  {
  //On pressing a key on "Search box" in "orden_venta.php" file. This function will be called.
  $("#searchstring").keyup(function()
  //window.onload = function()
    {
    //Assigning search box value to javascript variable named as "name".
    var name = $('#searchstring').val();
    //Validating, if "name" is empty.
    if (name == "") 
      {
      //Assigning empty value to "display" div in "search.php" file.
      $("#display").html("");
      }
    //If name is not empty.
    else 
      {
      //AJAX is called.
      $.ajax({
        //AJAX type is "Post".
        type: "POST",
        //Data will be sent to "orden_venta_ajax.php".
        url: "../ajax/orden_venta_ajax.php",
		dataType : "json",
        //Data, that will be sent to "orden_venta_ajax.php".
        data: {searchstring: name},
        //If result found, this funtion will be called.
        /*success: function(html) 
        //success: function(result) 
          {
          //Assigning result to "display" div in "search.php" file.
          $("#display").html(html).show();
          //$("#table_data").html(result);
          }*/
        success: function(result)          //on recieve of reply
          {
          var id = result[0];              //get id
          var vname = result[1];           //get name
          var  oventa_result = JSON.parse(result);
          alert(oventa_result["descripcion_cliente"]);
          //--------------------------------------------------------------------
          // 3) Update html content
          //--------------------------------------------------------------------
          //$('#table_data').html("<b>id: </b>"+id+"<b> name: </b>"+vname); //Set output element html
          } 
        });
      };
    });
});


/*$(function () 
  {
    var name = $('#searchstring').val();
    $.ajax({                                      
      url: '../ajax/orden_venta_ajax.php',                  //the script to call to get data          
      data: {"searchstring": name},                        //you can insert url argumnets here to pass to api.php
      dataType: 'json',                //data format      
      success: function(data)          //on recieve of reply
      {
        var id = data[0];              //get id
        var vname = data[1];           //get name
        //--------------------------------------------------------------------
        // 3) Update html content
        //--------------------------------------------------------------------
        $('#output').html("<b>id: </b>"+id+"<b> name: </b>"+vname); //Set output element html
      } 
    });
  });*/ 
</script>
<!-- <a href="orden_venta_form.php" onclick="pop_up(this);" class = "add">Nuevo</a> -->
<a target="_blank" href="orden_venta_form.php" class = "add">Nuevo</a>
<center>
    <!--<p class="left-style">Nro. de registros por pag.:<select name="limit" id= "limit" onchange="this.form.submit();">
        <?php 
        echo '<option value="5"'.$select5.'>5</option>';
        echo '<option value="10"'.$select10.'>10</option>';
        echo '<option value="50"'.$select50.'>50</option>';
        ?>
    </select>-->
	<!-- Search box. -->
    Busqueda: 
    <input type="text" id="searchstring" name="searchstring" class="right-style" placeholder="Busqueda" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
    <!--<input type="submit" id="search" name="search" value="Buscar"></p>-->
<!-- Suggestions will be displayed in below div. -->
<div id="display">
<?php
    echo '<table id="orden_venta_result" class="list">';
	   echo '<thead>';
            echo '<tr>';
              	echo '<th><a href="?sort=nr&sort_by='.$sort_order.'">Nr. Orden</th>';
                echo '<th><a href="?sort=fecha_orden&sort_by='.$sort_order.'">Fecha</th>';
                echo '<th><a href="?sort=descripcion_cliente&sort_by='.$sort_order.'">Cliente</th>';
               	echo '<th><a href="?sort=moneda_descripcion&sort_by='.$sort_order.'">Moneda</th>';
                echo '<th><a href="?sort=total_orden&sort_by='.$sort_order.'">Total General </th>';
              	echo '<th>Facturar</th>';               
                echo '<th>Editar</th>';
                echo '<th>Eliminar</th>';
                echo '<th>Imprimir</th>';
            echo '</tr>';
        echo '</thead>';
    	echo '<tbody id="table_data">';
            //Check if more than 0 records were found
            if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $orden = $total_row['nr'];
                echo '<td>'. $orden . '</td>';
                $fecha_orden = date_format(date_create($total_row['fecha_orden']),"d/m/Y");
                echo '<td>'. $fecha_orden . '</td>';
                echo '<td>'. $total_row['descripcion_cliente'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_orden = number_format(floatval($total_row['total_orden']),2,",",".");
                echo '<td class="monto">'. $total_orden . '</td>';
                //echo '<td class = "buttons-column"><a target="_blank" href="factura_venta_form.php?orden='.$orden.'" title="Facturar" class = "Facturar" onclick="VerificarFactura('.$orden.')">Facturar</a></td>';
                echo '<td class = "buttons-column"><a href="#" title="Facturar" class = "Facturar" onclick="VerificarFactura('.$orden.',0)">Facturar</a></td>';
                echo '<td class = "buttons-column"><a href="#" title="Editar" class = "Edit" onclick="VerificarFactura('.$orden.',1)"></a></td>';
                echo '<td class = "buttons-column"><a onclick="return confirmarBorrado('.$orden.')" href="'.$page_name.'?delete='.$orden.'"  title="Borrar" class = "delete"></a></td>';
                echo '<td><a target="_blank" href="../informes/imprimir_orden_venta.php?orden_venta='.$orden.'">Imprimir</a></td>';
                echo '</tr>';
                endforeach;
            }else{
                echo '<div class = "no_record">No se encontraron registros</div>';
            }
        echo '</tbody>';
	echo '</table>';
?>
</center>
</div> 
    <script language="Javascript">
        function confirmarBorrado(value)
        {
            var orden = value;
            eliminar=confirm("¿Deseas eliminar la Orden de Venta Nro. " + orden +"?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "orden_venta.php";
        }
    </script>
</form>
</body>
</html>

<?php  
//Pagination section
$total_pages = ceil($qty_register / $limit); 
$second_last = $total_pages - 1; // total pages minus 1
//echo  $total_pages;
/*$pagLink = "<ul class='pagination'>";  
for ($i=1; $i<=$total_pages; $i++) {
  $pagLink .= "<li><a href='".$page_name."?page=".$i."'>".$i."</a></li>";	
}
echo $pagLink . "</ul>";  
?>
<ul class="pagination">
<?php if($page > 1){
echo "<li><a href='?page=1'>First Page</a></li>";
}*/ ?>
    
<li <?php if($page <= 1){ echo "class='disabled'"; } ?>>
<a <?php if($page > 1){
echo "href='".$page_name."?page=$previous_page'";
} ?>>Previous</a>
</li>
    
<li <?php if($page >= $total_pages){
echo "class='disabled'";
} ?>>
<a <?php if($page < $total_pages) {
echo "href='".$page_name."?page=$next_page'";
} ?>>Next</a>
</li>

<?php if($page < $total_pages){
echo "<li><a href='".$page_name."?page=$total_pages'>Last &rsaquo;&rsaquo;</a></li>";
} ?>
</ul>

<?php
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $orden_venta->delete_orden_venta($nr);
    }

    if (isset($_GET['editar'])) //Check if the Delete variable has a value and delete a row
    {
        $orden_nr = $_GET['editar'];
        $orden_venta->delete_orden_venta($nr);
    }
?>