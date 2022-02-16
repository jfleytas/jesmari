<?php
	require '../clases/tablas/grupo_clientes.class.php';
	$grupo_clientes = grupo_clientes::singleton();

	//Get the data from Moneda;
	@$lista_precios_nr = $_POST['lista_precios_nr'];
	if (isset($_POST['lista_precios_nr'])){
		@$query = "select LP.nr_moneda, M.descripcion from lista_de_precios LP join moneda M on LP.nr_moneda = M.nr where LP.nr ='$lista_precios_nr'";
	    $moneda_result = $grupo_clientes->query($query);//We get the desired result from the table
	    $qty_register = $grupo_clientes->rowCount($query);
		if($qty_register>0) {
		    foreach ($moneda_result as $resultado_moneda) {        
		        $nr_moneda = $resultado_moneda['nr_moneda'];
		        $descripcion_moneda = $resultado_moneda['descripcion'];
		    }
			echo json_encode($nr_moneda.",".$descripcion_moneda);
		}else{
			echo 'alert("No existe la Moneda")';
		}
	}
?>