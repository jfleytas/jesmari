<?php
	session_start();
	//require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/

	require '../clases/php/fpdf183/fpdf.php';
	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();

	$usuario = $_SESSION['id_user'];
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$result = $productos->get_productos('select P.nr nr_producto, sum(S.stock_actual) as stock_total, P.id_producto, P.descripcion, CP.cpp
		from productos P left join stock_deposito_sucursal S on P.nr = S.nr_producto
		left join sucursal SU on S.nr_sucursal = SU.nr
		left join depositos_stock DS on S.nr_deposito = DS.nr 
		left join costos_productos CP on CP.nr_producto = P.nr
		where P.nr_tipo = 0 
		group by P.nr, P.id_producto, P.descripcion, CP.cpp
		order by P.descripcion');

	$decimals= 0;
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
	$pdf->Cell(70,6,'Lista de Stock Valorizado');

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Codigo','Descripcion','Stock Total','C. P. P. Gs.','Total');
	$w=array(30,80,20,30,30);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	$total_general = 0;
	foreach($result as $row) {
		$total_linea =($row['stock_total']*$row['cpp']);
		$total_general = $total_general + $total_linea;
		$pdf->Cell(30,6,$row['id_producto'],1,0,'L');
		$pdf->Cell(80,6,$row['descripcion'],1,0,'L');
		$pdf->Cell(20,6,number_format(floatval($row['stock_total']),$decimals,",","."),1,0,'R');
		$pdf->Cell(30,6,number_format(floatval($row['cpp']),$decimals,",","."),1,0,'R');
		$pdf->Cell(30,6,number_format(floatval($total_linea),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(160,6,'Total General Gs.',1,0,'L');
	$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');

	$pdf->Output();
?>