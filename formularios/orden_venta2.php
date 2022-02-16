<?php
    $page_name="orden_venta.php"; 

    //header("Refresh:10; url=$page_name");
    
    require '../tablas/menu.php';
    
    require '../clases/formularios/orden_venta.class.php';
    $orden_venta = orden_venta::singleton();

    //Declaring variables
    $orden=isset($_POST['orden']) ? $_POST['orden'] : '';
    
    //Pagination query
    //$limit=$_POST['limit']; // Read the limit value from query string.
    $limit=(isset($_POST['limit'])) ? $_POST['limit'] : 5; //If limit value is not available then let us use a default value
    
    // If there is a selection or value of limit then the list box should show that value , so we have to lock that options
    if(isset($_GET['limit']))
    {
    switch($limit)
    {
        case 10:
        $select5="";
        $select10="selected";
        $select50="";
        break;

        case 50:
        $select5="";
        $select10="";
        $select50="selected";
        break;

        case 5:
        $select5="selected";
        $select10="";
        $select50="";
        break;
    }
    } 

    @$start=$_GET['start'];

    // You can keep the below line inside the above form, if you want when user selection of number of 
    // records per page changes, it should not return to first page. 
    echo '<input type=hidden name=start value=$start>';

    $offset = ($start - 0); 

    $actual = $offset + $limit; 
    $back = $offset - $limit; 
    $next = $offset + $limit; 

    $query = "select OV.nr, OV.fecha_orden, C.razon_social descripcion_cliente, M.descripcion moneda_descripcion, OV.total_orden 
    from cabecera_orden_venta OV join clientes C on OV.nr_cliente = C.nr join moneda M on OV.nr_moneda = M.nr";
    //Search query
    $searchstring = null;    
    $qty_register = $orden_venta -> rowCount($query);
    
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
    }  

    $query = $query . " order by $sort $sort_order limit 150"; 
    //$query = $query . " order by $sort $sort_order limit $limit offset $offset"; 
    echo $query;   

    $total_result = $orden_venta->query($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $orden_venta->get_orden_venta("select * from cabecera_orden_venta order by nr");//We get the desired result from the table
        foreach($result as $row):
            /*$nr = $row['nr'];
            $fecha = $row['fecha'];
            $cotizacion_venta = $row['cotizacion_venta'];
            $cotizacion_venta = $row['cotizacion_venta'];
            $nr_moneda = $row['nr_moneda'];*/
        endforeach;
    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
<!--<link rel="stylesheet" type="text/css" href="../css/estilo.pagination.css" />-->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
<script src="../js/jquery.js"></script>
<!-- Including our busqueda scripting file. 
<script src="../js/busqueda.js"></script>-->
</head>
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
            /*if($qty_register > 0){
                foreach($total_result as $total_row):
                echo '<tr>';
                $orden = $total_row['nr'];
                echo '<td>'. $orden . '</td>';
                $fecha_orden = date_format(date_create($total_row['fecha_orden']),"d/m/Y");
                echo '<td>'. $fecha_orden . '</td>';
                echo '<td>'. $total_row['descripcion_cliente'] . '</td>';
                echo '<td>'. $total_row['moneda_descripcion'] . '</td>';
                $total_orden = number_format($total_row['total_orden'],2,",",".");
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
            }*/
        echo '</tbody>';
	echo '</table>';

    //Pagination
    /*echo "<table align = 'center' width='50%'><tr><td  align='left' width='30%'>";
    //If our variable $back is equal to 0 or more then only we will display the link to move back
    if($back >=0) 
    { 
        print "<a href='?start=$back&limit=$limit'><font face='Verdana' size='2'>ANT.</font></a>"; 
    } 
    //Let us display the page links at  center. We will not display the current page as a link
    echo "</td><td align=center width='30%'>";
    $i=0;
    $l=1;
    for($i=0;$i < $qty_register;$i=$i+$limit)
    {
        if($i <> $offset)
        {
            echo " <a href='?start=$i&limit=$limit'><font face='Verdana' size='2'>$l</font></a> ";
        }else{ 
            echo "<font face='Verdana' size='4' color=red>$l</font>";
        }//Current page is not displayed as link and given font color red
        $l=$l+1;
    }

    echo "</td><td  align='right' width='30%'>";
    //If we are not in the last page then Next link will be displayed. Here we check that
    if($actual < $qty_register)
    { 
        print "<a href='?start=$next&limit=$limit'><font face='Verdana' size='2'>SIG.</font></a>";
    } 
    echo "</td></tr></table>";*/
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