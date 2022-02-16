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

	$sql = "select CC.nr, CC.fecha_cobranza, CC.nr_cobranza, CC.nr_cliente, CC.nr_moneda, V.nr, V.id_personal, 
		CC.total_pago, C.razon_social, M.id_moneda, M.descripcion descripcion_moneda, CC.cotizacion_compra
		from cabecera_cobranza CC join moneda M on CC.nr_moneda = M.nr
		join clientes C on CC.nr_cliente = C.nr
		join personal V on CC.nr_cobrador = V.nr
		where CC.fecha_cobranza >= '".$fecha_inicio."' and CC.fecha_cobranza <= '".$fecha_fin."'";

	if(!empty($cliente))
    {
    	$sql = $sql . " and CC.nr_cliente = ".$cliente;	
    }

    if(!empty($cobrador))
    {
    	$sql = $sql . " and CC.nr_cobrador = ".$cobrador;	
    }

	$sql = $sql . " order by CC.fecha_cobranza";

	$result = $productos->get_productos($sql);

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
	$pdf->Cell(70,6,'Lista de Cobranzas');

	$font_size = 9;
	$pdf->SetFont('Arial','',$font_size);

	//Line break	
	$pdf->Ln();
	$pdf->SetY(40);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Fecha Inicio: '.date_format(date_create($fecha_inicio),"d-m-Y"));

	//Line break	
	$pdf->Ln();
	$pdf->SetY(46);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Fecha Fin: '.date_format(date_create($fecha_fin),"d-m-Y"));

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('#','Fecha','Nro. Recibo','Cliente', 'Monto', 'Mon.','Cobr.');
	$w=array(10,21,30,75,30,13,13);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	//Declarations
	$i= 0;
	@$total_general = 0;
	@$decimals = 0;
	foreach($result as $row) {
		$fecha = date_format(date_create($row['fecha_cobranza']),"d/m/Y");
		$i= $i+1;
		$pdf->Cell(10,6,$i,1,0,'C');
		$pdf->Cell(21,6,$fecha,1,0,'L');
		$pdf->Cell(30,6,$row['nr_cobranza'],1,0,'L');
		$pdf->Cell(75,6,$row['razon_social'],1,0,'L');
		$monto_pago = number_format(floatval($row['total_pago']),$decimals,",",".");
		$pdf->Cell(30,6,$monto_pago,1,0,'R');
		$pdf->Cell(13,6,$row['id_moneda'],1,0,'C');
		$pdf->Cell(13,6,$row['id_personal'],1,0,'C');
		//Total General
		$total_general = $total_general + ($row['total_pago']*$row['cotizacion_compra']);
		//Line break	
		$pdf->Ln();
	}

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(136,6,'Total General Gs.',1,0,'C');
	$pdf->Cell(56,6,number_format(floatval($total_general),$decimals,",","."),1,0,'C');

	$pdf->Output();
?>