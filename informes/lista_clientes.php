<?php
	session_start();
	//require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
	require '../clases/php/fpdf183/fpdf.php';
	require '../clases/tablas/clientes.class.php';
	$clientes = clientes::singleton();
	
	$usuario = $_SESSION['id_user'];

	$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	$id=implode(",",$sel);//seperating ',' from array

	$result_config = $clientes->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$vendedor = $_GET['vendedor'];
	
	$query = "select C.id_cliente, C.razon_social, C.ruc, C.direccion, C.telefono, C.nr_vendedor, V.id_personal id_vendedor
        from clientes C join personal V on C.nr_vendedor = V.nr";
		
	if(!empty($vendedor))
    {
    	$query = $query . " where C.nr_vendedor = ".$vendedor;	
    }
	
	$query = $query . " order by C.razon_social";
	//echo $query;
	$result = $clientes->get_clientes($query);

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
	$pdf->SetX(75);
	$pdf->Cell(70,6,'Lista de Clientes');

	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('#','Codigo','Razon Social','R.U.C.','Direccion','Telefono','Vend.');
	$w=array(10,30,55,20,50,20,10);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$font_size = 8;
	$pdf->SetFont('Arial','',$font_size);
	$i= 0;
	foreach($result as $row) {
		$i= $i+1;
		$pdf->Cell(10,6,$i,1,0,'L');
		$pdf->Cell(30,6,$row['id_cliente'],1,0,'L');
		$pdf->Cell(55,6,$row['razon_social'],1,0,'L');
		$pdf->Cell(20,6,$row['ruc'],1,0,'R');
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(50,6,$row['direccion'],1,0,'L');
		$pdf->SetFont('Arial','',$font_size);
		$pdf->Cell(20,6,$row['telefono'],1,0,'L');
        $pdf->Cell(10,6,$row['id_vendedor'],1,0,'L');
		//Line break	
		$pdf->Ln();
	}

	$pdf->Output();
?>

