<?php
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/orden_pago.class.php';
	
	$orden_pago = orden_pago::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array
	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$orden_pago = $_GET['orden_pago'];

	$result_cabecera = $productos->query("select COP.nr, to_char(COP.fecha_pago, 'DD-MM-YYYY') fecha_pago, COP.nr_proveedor, P.id_proveedor, P.descripcion descripcion_proveedor, COP.nr_sucursal, 
		S.descripcion descripcion_sucursal, COP.nr_caja, C.descripcion descripcion_caja, COP.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, 
		COP.total_pago, COP.nr_user, U.id_user
        from cabecera_orden_pago COP join proveedor P on COP.nr_proveedor = P.nr
        join sucursal S on COP.nr_sucursal = S.nr
        join cajas C on COP.nr_caja = C.nr
        join moneda M on COP.nr_moneda = M.nr
        join users U on COP.nr_user = U.nr
		where COP.nr = '$orden_pago'");

	$result_detalle = $productos->query("select DOP.nr, DOP.nr_medio_pago, MP.id_medio_pago, MP.descripcion descripcion_medio_pago, DOP.monto_pago, DOP.total_linea, DOP.obs
        from detalle_orden_pago DOP join medio_pago MP on DOP.nr_medio_pago = MP.nr
        where DOP.nr = '$orden_pago'");

	$result_aplicacion_detalle = $productos->query("select DAP.nr, DAP.nr_factura_compra, CFC.nr_factura, DAP.monto_aplicado, DAP.total_linea
        from detalle_aplicacion_pago DAP join cabecera_factura_compra CFC on DAP.nr_factura_compra = CFC.nr
        where DAP.nr = '$orden_pago'");

	@$decimals = 0;

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	//Set the logo
	$logo = "../img/logo.jpg";
	$pdf->Cell(10,10, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 20.78), 0, 0, 'L', false);
	$pdf->SetY(15);
	$pdf->SetX(80);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(60);
	$pdf->Cell(70,6,'Orden de Pago Nro.:');
	$pdf->SetY(25);
	$pdf->SetX(112);
	$pdf->Cell(130,6,$orden_pago);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha Pago:');
	$pdf->SetY(40);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Proveedor: ');
	$pdf->SetY(35);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Sucursal:');
	$pdf->SetY(40);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Caja:');

	
	//Now show the columns
	//Line break	
	//$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_cabecera as $cabecera) {
		$total_general = $cabecera['total_pago'];
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
		$pdf->Cell(15,6,$cabecera['fecha_pago'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(40);
		$pdf->Cell(45,6,$datos_proveedor,0,0,'L');
		$pdf->Ln();
		$pdf->SetY(35);
		$pdf->SetX(140);
		$pdf->Cell(118,6,$cabecera['descripcion_sucursal'],0,0,'L');
		$pdf->Ln();	
		$pdf->SetY(40);
		$pdf->SetX(140);
		$pdf->Cell(133,6,$cabecera['descripcion_caja'],0,0,'L');
		//Line break	
		$pdf->Ln();
	}

	//Line break	
	$pdf->Ln();
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Codigo','Medio de Pago','Monto Pago','Obs.');
	$w=array(30,50,30,50);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_detalle as $detalle) {
		$pdf->Cell(30,6,$detalle['id_medio_pago'],1,0,'L');
		$pdf->Cell(50,6,$detalle['descripcion_medio_pago'],1,0,'L');
		$pdf->Cell(30,6,number_format(floatval($detalle['monto_pago']),$decimals,",","."),1,0,'R');
		$pdf->Cell(50,6,$detalle['obs'],1,0,'R');	
		//Line break	
		$pdf->Ln();
	}

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$titulo = 'Total General '. $id_moneda;
	$pdf->Cell(80,6,$titulo,1,0,'L');
	$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();

	//Aplicacion Pago section
	//Detalle Header
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Factura Compra','Monto Aplicado');
	$w=array(55,55);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_aplicacion_detalle as $detalle) {
		$pdf->Cell(55,6,$detalle['nr_factura'],1,0,'L');
		$pdf->Cell(55,6,number_format(floatval($detalle['monto_aplicado']),$decimals,",","."),1,0,'R');	
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(55,6,$titulo,1,0,'L');
	$pdf->Cell(55,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');

	$pdf->Output();
?>