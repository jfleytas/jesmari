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

	$producto = $_GET['producto'];
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	$sql ="select P.id_producto, P.descripcion, S.stock_actual, S.stock_a_recibir, S.stock_a_facturar, S.nr_sucursal, SU.descripcion sucursal_descripcion, S.nr_deposito, DS.id_deposito, DS.descripcion deposito_descripcion
		from productos P join stock_deposito_sucursal S on P.nr = S.nr_producto
		left join sucursal SU on S.nr_sucursal = SU.nr
		left join depositos_stock DS on S.nr_deposito = DS.nr 
		where P.nr_tipo = 0 ";

	if(!empty($producto))
    {
    	$sql = $sql . " and P.nr = ".$producto;	
    }

    $sql = $sql . " order by P.descripcion, SU.descripcion, DS.id_deposito";

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
	$pdf->Cell(70,6,'Lista de Stock por Deposito y Sucursal');

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Codigo','Descripcion','Sucurs.','Depos.','Actual', 'A Recibir', 'A Fact.');
	$w=array(30,70,16,16,20,20,20,10,15,15,15,15,15);
	$pdf->SetY(35);
	$pdf->SetX(142);
	$pdf->Cell(60,6,'STOCK',1,0,'C');
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result as $row) {
		$pdf->Cell(30,6,$row['id_producto'],1,0,'L');
		$pdf->Cell(70,6,$row['descripcion'],1,0,'L');
		$font_size = 7;
		$pdf->SetFont('Arial','',$font_size);
		$pdf->Cell(16,6,$row['sucursal_descripcion'],1,0,'C');
		$font_size = 10;
		$pdf->SetFont('Arial','',$font_size);
		$pdf->Cell(16,6,$row['id_deposito'],1,0,'C');
		$pdf->Cell(20,6,$row['stock_actual'],1,0,'R');
		$pdf->Cell(20,6,$row['stock_a_recibir'],1,0,'R');
		$pdf->Cell(20,6,$row['stock_a_facturar'],1,0,'R');
		//Line break	
		$pdf->Ln();
	}

	$pdf->Output();
?>