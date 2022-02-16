<?php
	require '../clases/tablas/precio_producto_proveedor.class.php';
	$precio_producto_proveedor = precio_producto_proveedor::singleton();

	//Get the data from Proveedor;
	@$proveedor = $_POST['proveedor'];
	if (isset($_POST['proveedor'])){
		@$query = "select * from proveedor where id_proveedor ='$proveedor'";
	    $proveedor_result = $precio_producto_proveedor->query($query);//We get the desired result from the table
	    $qty_register = $precio_producto_proveedor->rowCount($query);
		if($qty_register>0) {
		    foreach ($proveedor_result as $resultado_proveedor) {        
		        $nr_proveedor = $resultado_proveedor['nr'];
		        $id_proveedor = $resultado_proveedor['id_proveedor'];
		        $descripcion_proveedor = $resultado_proveedor['descripcion'];
		    }
			echo json_encode($nr_proveedor.",".$id_proveedor.",".$descripcion_proveedor);
		}else{
			//echo 'alert("No existe el Proveedor")';
		}
	}

	//Get the data from Producto;
	@$producto = $_POST['producto'];
	if (isset($_POST['producto'])){
		@$query = "select * from productos P where P.id_producto ='$producto'";
	    $producto_result = $precio_producto_proveedor->query($query);//We get the desired result from the table
	    $qty_register = $precio_producto_proveedor->rowCount($query);
		if($qty_register>0) {
			//echo $producto_result;
			foreach ($producto_result as $resultado_producto) {        
			    $nr_producto = $resultado_producto['nr'];
			    $id_producto = $resultado_producto['id_producto'];
			    $descripcion_producto = $resultado_producto['descripcion'];
			}
	    	echo json_encode($nr_producto.",".$id_producto.",".$descripcion_producto);
		}else{
			//echo 'alert("No existe el Producto")';
		}
	}

	//Search section
	if(!empty($_POST["buscar_proveedor"])) {
		//The variable buscar_proveedor is already in uppercase
		@$buscar_proveedor = $_POST["buscar_proveedor"];
		//We also uppercase the database columns to match with the variable->buscar_proveedor
		@$query = "select * from proveedor P where upper(descripcion) like '%".$buscar_proveedor."%' order by descripcion";
		//echo $query;
		$proveedor_result = $precio_producto_proveedor->query($query);
		$qty_register = $precio_producto_proveedor->rowCount($query);
		if($qty_register>0) {
			echo '<ul id="proveedor-list">';
			foreach($proveedor_result as $result_proveedor) {
				?>
				<li onClick="selectProveedor('<?php echo $result_proveedor["id_proveedor"]; ?>');">
				<?php echo $result_proveedor["descripcion"]; ?>
				</li>
				<?php
			}
			echo '</ul>';
		}
	}


	if(!empty($_POST["buscar_producto"])) {
		//We uppercase the searchbox
		@$buscar_producto = $_POST["buscar_producto"];
		//We also uppercase the database columns to match with the variable->buscar_producto
		@$query = "select * from productos P where upper(descripcion) like '%".$buscar_producto."%' order by descripcion";
		//echo $query;
		$producto_result = $precio_producto_proveedor->query($query);
		$qty_register = $precio_producto_proveedor->rowCount($query);
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