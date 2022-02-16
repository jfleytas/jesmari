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

	$result = $productos->get_productos('select CFV.nr_vendedor, P.nombre_apellido vendedor, sum(coalesce(CFV.total_factura,0)) as total_general
		from cabecera_factura_venta CFV right join personal P on CFV.nr_vendedor = P.nr
		    where CFV.fecha_factura >= '.$fecha_inicio.' and CFV.fecha_factura <= '.$fecha_fin.'
		    group by nr_vendedor, vendedor;');

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
	$pdf->Cell(70,6,'Ventas por Vendedor');

	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Nro.','Vendedor','Total General.');
	$w=array(15,30,40);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);

	foreach($result as $row) {
		//$pdf->Cell(20,6,$row['nr_sucursal'],1,0,'L');
		$pdf->Cell(15,6,$i,1,0,'L');
		$pdf->Cell(30,6,number_format(floatval($row['vendedor']),$decimals,",","."),1,0,'R');
		$font_size = 10;
		$pdf->SetFont('Arial','B',$font_size);
		$pdf->SetTextColor(194,8,8);
		$pdf->Cell(20,6,number_format(floatval($row['total_general']),$decimals,",","."),1,0,'R');
		$font_size = 10;
		$pdf->SetFont('Arial','',$font_size);
		$pdf->SetTextColor(0,0,0);
		//Line break	
		$pdf->Ln();
	}

	//Line break	
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(100,6,'==Todos los valores son expresados en Gs.==',0,1,'C');

	$pdf->Output();
?>