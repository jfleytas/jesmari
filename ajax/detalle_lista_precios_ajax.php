<?php
	require '../clases/tablas/detalle_lista_precios.class.php';
	$detalle_lista_precios = detalle_lista_precios::singleton();

	//Get the data from Producto;
	@$producto = $_POST['producto'];
	if (isset($_POST['producto'])){
		@$query = "select * from productos P left join costos_productos CP on P.nr = CP.nr_producto where P.id_producto ='$producto'";
	    $producto_result = $detalle_lista_precios->query($query);//We get the desired result from the table
	    $qty_register = $detalle_lista_precios->rowCount($query);
		if($qty_register>0) {
			//echo $producto_result;
			foreach ($producto_result as $resultado_producto) {        
			    $nr_producto = $resultado_producto['nr'];
			    $id_producto = $resultado_producto['id_producto'];
			    $descripcion_producto = $resultado_producto['descripcion'];
			    $costo_producto = $resultado_producto['cpp'];
			}
		}else{
			echo 'alert("No existe el Producto")';
		}
		echo json_encode($nr_producto.",".$id_producto.",".$descripcion_producto.",".$costo_producto);
	}

	//Search section
	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' order by descripcion";
		//echo $query;
		$producto_result = $detalle_lista_precios->query($query);
		$qty_register = $detalle_lista_precios->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="producto-list">';
			foreach($producto_result as $result_producto) {
				?>
				<li onClick="selectProducto('<?php echo $result_producto["id_producto"]; ?>');">
				<?php echo $result_producto["descripcion"]; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}
?>