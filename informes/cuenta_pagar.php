<?php
	session_start();
	//require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/

	require '../clases/php/fpdf183/fpdf.php';
	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();

	$usuario = $_SESSION['id_user'];

	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}
	
	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];

	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	@$result = $productos->get_productos("select P.nr nr_proveedor, P.id_proveedor, P.descripcion, FC.nr, FC.nr_factura, FC.fecha_factura, FC.fecha_vto, FC.saldo_factura, FC.nr_moneda, M.id_moneda,
		M.descripcion descripcion_moneda, FC.cotizacion_compra, FC.cotizacion_venta
		from proveedor P join cabecera_factura_compra FC on P.nr = FC.nr_proveedor
		join moneda M on M.nr = FC.nr_moneda
		where FC.saldo_factura > 0 and
		FC.fecha_factura >= '".$fecha_inicio."' and FC.fecha_factura <= '".$fecha_fin."'
		group by P.nr, P.id_proveedor, P.descripcion, FC.nr, FC.nr_factura, FC.fecha_factura, FC.fecha_vto, FC.saldo_factura, FC.nr_moneda, M.id_moneda,
		M.descripcion, FC.cotizacion_compra, FC.cotizacion_venta
		order by P.id_proveedor, FC.fecha_vto");

	$decimals = 0;
	$logo = "../img/logo.jpg";
	$fecha_actual = date('d/m/Y');

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A4');
	$pdf->AddPage();
	$font_size = 9;
	$pdf->SetFont('Arial','',$font_size);
	//Set the logo
	$pdf->Cell(10,10, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 20.78), 0, 0, 'L', false);
	//Title
	$pdf->SetY(0);
	$pdf->SetX(141);
    $pdf->Cell(60,20,"Reporte generado por ".$usuario." el: ".$fecha_actual,0,0,'C');

    //Line break	
	$pdf->Ln();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->SetY(15);
	$pdf->SetX(80);
	$pdf->Cell(60,10,$nombre_empresa,0,0,'C');
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(60);
	$pdf->Cell(70,6,'Cuentas a Pagar',0,0,'C');

	//Line break	
	$pdf->Ln();
	$font_size = 9;
	$pdf->SetFont('Arial','',$font_size);

	$pdf->SetY(40);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Fecha Inicio: '.date_format(date_create($fecha_inicio),"d-m-Y"));

	//Line break	
	$pdf->Ln();
	$pdf->SetY(46);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Fecha Fin: '.date_format(date_create($fecha_fin),"d-m-Y"));

	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Proveedor','Razon Social','Fecha Factura','Factura Nro.','Vencimiento','Saldo a Pagar','Moneda');
	$w=array(30,45,26,26,24,28,15);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);

	@$total_general_gs = 0;
	@$total_vencido_gs = 0;
	@$total_a_vencer_gs = 0;
	@$vencido = '';
	foreach($result as $row) {
		if ($row['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}
		@$id_proveedor = $row['id_proveedor'];
		$total_general_gs = $total_general_gs + ($row['saldo_factura']*$row['cotizacion_venta']);
		$fecha = date_format(date_create($row['fecha_factura']),"d/m/Y");
		@$fecha_vto = date_format(date_create($row['fecha_vto']),"d/m/Y");

		if(strtotime(date("Y-m-d")) > strtotime($row['fecha_vto']))
		{
			$total_vencido_gs = $total_vencido_gs + ($row['saldo_factura']*$row['cotizacion_venta']);
			$vencido = '**';
		}else{
			$total_a_vencer_gs = $total_a_vencer_gs + ($row['saldo_factura']*$row['cotizacion_venta']);
			$vencido = '';
		}

		$pdf->Cell(30,6,$id_proveedor,1,0,'L');
		$pdf->Cell(45,6,$row['descripcion'],1,0,'L');
		$pdf->Cell(26,6,$fecha,1,0,'C');
		$pdf->Cell(26,6,$row['nr_factura'],1,0,'R');
		$pdf->Cell(24,6,$fecha_vto,1,0,'C');
		$pdf->Cell(28,6,number_format(floatval(($row['saldo_factura']*$row['cotizacion_venta'])),$decimals,",","."),1,0,'R');
		$pdf->Cell(15,6,$row['id_moneda'],1,0,'L');
		$pdf->Cell(4,6,$vencido,0,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(127,6,'Total Vencido Gs.',1,0,'C');
	$pdf->Cell(67,6,number_format(floatval($total_vencido_gs),$decimals,",","."),1,0,'C');
	//Line break	
	$pdf->Ln();
	$pdf->Cell(127,6,'Total A Vencer Gs.',1,0,'C');
	$pdf->Cell(67,6,number_format(floatval($total_a_vencer_gs),$decimals,",","."),1,0,'C');
	//Line break	
	$pdf->Ln();
	$pdf->Cell(127,6,'Total General Gs.',1,0,'C');
	$pdf->Cell(67,6,number_format(floatval($total_general_gs),$decimals,",","."),1,0,'C');

	//Line break	
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell(35,6,'** Factura Vencida',0,1,'R');

	//Line break	
	$pdf->Ln();
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(200,6,'==Todos los valores son expresados en Gs.==',0,1,'C');

	$pdf->Output();
?>