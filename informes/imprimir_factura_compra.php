<?php
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/factura_compra.class.php';
	
	$factura_compra = orden_compra::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array
	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$factura_compra = $_GET['factura_compra'];

	$result_cabecera = $productos->query("select CFC.nr, to_char(CFC.fecha_factura, 'DD-MM-YYYY') fecha_factura, CFC.nr_proveedor, P.id_proveedor, P.descripcion descripcion_proveedor, CFC.nr_condicion, C.descripcion descripcion_condicion, CFC.nr_sucursal, 
		S.descripcion descripcion_sucursal, CFC.nr_deposito, DS.descripcion descripcion_deposito, CFC.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, to_char(CFC.fecha_vto, 'DD-MM-YYYY') fecha_vto, 
		CFC.total_exentas, CFC.total_gravadas, CFC.total_iva, CFC.total_factura, CFC.nr_user, U.id_user
        from cabecera_factura_compra CFC join proveedor P on CFC.nr_proveedor = P.nr
        join condicion_compra_venta C on CFC.nr_condicion = C.nr 
        join sucursal S on CFC.nr_sucursal = S.nr
        join depositos_stock DS on CFC.nr_deposito = DS.nr
        join moneda M on CFC.nr_moneda = M.nr
        join users U on CFC.nr_user = U.nr
		where CFC.nr = '$factura_compra'");

	$result_detalle = $productos->query("select DFC.nr, DFC.nr_producto, P.id_producto, P.descripcion descripcion_producto, DFC.cantidad, DFC.descuento, DFC.precio_lista, DFC.precio_final, DFC.impuesto, DFC.nr_unidad, UM.id_unidad_medida id_unidad,
		DFC.total_exentas_linea, DFC.total_gravadas_linea, DFC.total_iva_linea, DFC.total_linea
        from detalle_factura_compra DFC join productos P on DFC.nr_producto = P.nr
        join unidad_medida UM on DFC.nr_unidad = UM.nr where DFC.nr = '$factura_compra'");

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->SetY(15);
	$pdf->SetX(80);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(60);
	$pdf->Cell(70,6,'Factura de Compra Nro.:');
	$pdf->SetY(25);
	$pdf->SetX(120);
	$pdf->Cell(130,6,$factura_compra);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha Factura:');
	$pdf->SetY(40);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Proveedor: ');
	$pdf->SetY(45);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Fecha Vto.:');
	$pdf->SetY(35);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Sucursal:');
	$pdf->SetY(40);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Deposito:');

	
	//Now show the columns
	//Line break	
	//$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_cabecera as $cabecera) {
		$total_general = $cabecera['total_factura'];
		$id_moneda = $cabecera['id_moneda'];
		if ($cabecera['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		$datos_proveedor = $cabecera['descripcion_proveedor'].'  ('.$cabecera['id_proveedor'].')';
		$pdf->SetY(35);
		$pdf->SetX(40);
		$pdf->Cell(15,6,$cabecera['fecha_factura'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(40);
		$pdf->Cell(45,6,$datos_proveedor,0,0,'L');
		$pdf->Ln();
		$pdf->SetY(45);
		$pdf->SetX(40);
		$pdf->Cell(70,6,$cabecera['fecha_vto'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(35);
		$pdf->SetX(140);
		$pdf->Cell(118,6,$cabecera['descripcion_sucursal'],0,0,'L');
		$pdf->Ln();	
		$pdf->SetY(40);
		$pdf->SetX(140);
		$pdf->Cell(133,6,$cabecera['descripcion_deposito'],0,0,'L');
		//Line break	
		$pdf->Ln();
	}

	//Line break	
	$pdf->Ln();
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Codigo','Descripcion','Cantidad','Precio Unitario','IVA','Total');
	$w=array(30,70,20,25,15,30);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($detalle_header);$i++)
	$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_detalle as $detalle) {
		$pdf->Cell(30,6,$detalle['id_producto'],1,0,'L');
		$pdf->Cell(70,6,$detalle['descripcion_producto'],1,0,'L');
		$pdf->Cell(20,6,number_format(floatval($detalle['cantidad']),$decimals,",","."),1,0,'R');
		$pdf->Cell(25,6,number_format(floatval($detalle['precio_final']),$decimals,",","."),1,0,'R');
		$pdf->Cell(15,6,$detalle['impuesto'],1,0,'R');	
		$pdf->Cell(30,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$titulo = 'Total General '. $id_moneda;
	$pdf->Cell(160,6,$titulo,1,0,'L');
	$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');

	$pdf->Output();
?>