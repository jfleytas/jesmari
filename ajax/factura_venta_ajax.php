<?php
	require '../clases/formularios/factura_venta.class.php';
	$factura_venta = factura_venta::singleton();

	//Get the data from Deposito Stock for the specific Sucursal;
	@$tipo_factura = $_POST['tipo_factura'];
	if (isset($_POST['tipo_factura'])){
		echo json_encode($tipo_factura.','.'Tipo');
	}

?>