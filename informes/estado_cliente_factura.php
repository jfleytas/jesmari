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
	$cliente = $_GET['cliente'];
	$vendedor = $_GET['vendedor'];
	
	$sql = "select C.nr nr_cliente, C.id_cliente, C.razon_social, FV.nr, FV.nr_factura, FV.fecha_vto, FV.fecha_factura, FV.saldo_factura, FV.nr_moneda, M.id_moneda,
		M.descripcion descripcion_moneda, FV.cotizacion_compra, FV.cotizacion_venta, FV.nr_vendedor, V.id_personal
		from clientes C join cabecera_factura_venta FV on C.nr = FV.nr_cliente
		join moneda M on M.nr = FV.nr_moneda
		join personal V on V.nr = FV.nr_vendedor
		where FV.saldo_factura > 0 and
		FV.fecha_factura >= '".$fecha_inicio."' and FV.fecha_factura <= '".$fecha_fin."'";

	if(!empty($cliente))
    {
    	$sql = $sql . " and FV.nr_cliente = ".$cliente;	
    }

    if(!empty($vendedor))
    {
    	$sql = $sql . " and FV.nr_vendedor = ".$vendedor;	
    }
	
	$sql = $sql . " order by FV.fecha_vto";

	$result = $productos->get_productos($sql);

	$decimals = 0;
	$logo = "../img/logo.jpg";

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
    $pdf->Cell(60,20,"Reporte generado por ".$usuario." el: ".date('d-m-Y'),0,0,'C');

    //Line break	
	$pdf->Ln();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->SetY(15);
	$pdf->SetX(80);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(60);
	$pdf->Cell(70,6,'Estado de Cliente por Factura');

	//Line break	
	$pdf->Ln();
    $font_size = 9;
	$pdf->SetFont('Arial','',$font_size);	

	$pdf->SetY(46);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Cliente: '.$cliente);


	//Set the Font
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Cliente','Razon Social','Fecha Factura','Factura Nro.','Vencimiento','Saldo a Cobrar','Moneda');
	$w=array(30,45,26,26,24,28,15);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);

	$total_general_gs = 0;
	$total_vencido_gs = 0;
	$total_a_vencer_gs = 0;
	$vencido = '';
	foreach($result as $row) {
		if ($row['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}
		$id_cliente = $row['id_cliente'];
		$total_general_gs = $total_general_gs + ($row['saldo_factura']*$row['cotizacion_compra']);
		$fecha = date_format(date_create($row['fecha_factura']),"d/m/Y");
		$fecha_vto = date_format(date_create($row['fecha_vto']),"d/m/Y");

		if(strtotime(date("Y-m-d")) > strtotime($row['fecha_vto']))
		{
			$total_vencido_gs = $total_vencido_gs + ($row['saldo_factura']*$row['cotizacion_compra']);
			$vencido = '**';
		}else{
			$total_a_vencer_gs = $total_a_vencer_gs + ($row['saldo_factura']*$row['cotizacion_compra']);
			$vencido = '';
		}

		$pdf->Cell(30,6,$id_cliente,1,0,'L');
		$pdf->Cell(45,6,$row['razon_social'],1,0,'L');
		$pdf->Cell(26,6,$fecha,1,0,'C');
		$pdf->Cell(26,6,$row['nr_factura'],1,0,'R');
		$pdf->Cell(24,6,$fecha_vto,1,0,'C');
		$pdf->Cell(28,6,number_format(floatval(($row['saldo_factura']*$row['cotizacion_compra'])),$decimals,",","."),1,0,'R');
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