<?php
	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();

	//Get the data from Clasificacion according to the Tipo Producto;
	@$nr_tipo = $_POST['nr_tipo'];
	if (isset($_POST['nr_tipo'])){
		$clasificacion_result = $productos->get_clasificacion($nr_tipo);
		foreach ($clasificacion_result as $row) {
            $clasificacion_nr = $row['nr'];
            echo '<option value="'.$row['nr'].'"';
            if (@$nr_clasificacion==$clasificacion_nr) echo 'selected="selected"';
            echo '>'.$row['descripcion'].'</option>'; 
        }
	}
?>