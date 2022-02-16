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

	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];

	$total_vendedor = $productos->get_productos("select ranked.*
		from (
		select sum(FV.total_factura*FV.cotizacion_compra) as total_general_vendedor, sum (FV.total_factura-FV.total_iva) as total_sin_iva, sum(FV.total_costo) as total_costo_vendedor, FV.nr_moneda, M.id_moneda,M.descripcion descripcion_moneda,
		FV.nr_vendedor, P.id_personal, P.nombre_apellido
		,rank() over (order by sum(FV.total_factura) desc) as rank
		from personal P join cabecera_factura_venta FV on P.nr = FV.nr_vendedor
		join moneda M on M.nr = FV.nr_moneda
		where FV.fecha_factura >= '".$fecha_inicio."' and FV.fecha_factura <= '".$fecha_fin."'
		group by FV.nr_moneda, M.id_moneda, M.descripcion, FV.nr_vendedor, P.id_personal, P.nombre_apellido
		) as ranked;");

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
	$pdf->Cell(70,6,'Ranking de ventas por Vendedor');

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
	$header=array('Ranking','Vendedor','Nombre y Apellido','Total Venta','Total Costo','% Util.','Moneda');
	$w=array(17,20,50,30,30,20,20);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$font_size = 10;
	$pdf->SetFont('Arial','',$font_size);
	$total_general_factura_gs = 0;
	$total_general_sin_iva_gs = 0;
	$total_general_costo_gs = 0;
	foreach($total_vendedor as $row) {
		if ($row['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}
		$id_personal = $row['id_personal'];
		$total_general_factura_gs = $total_general_factura_gs + $row['total_general_vendedor'];
		$total_general_sin_iva = $row['total_sin_iva'];
		$total_general_sin_iva_gs = $total_general_sin_iva_gs + $total_general_sin_iva;
		$costo_general = $row['total_costo_vendedor'];
		$total_general_costo_gs = $total_general_costo_gs + $costo_general;
		$utilidad = (($total_general_sin_iva/$costo_general)-1)*100;
		//$fecha = date_format(date_create($row['fecha_factura']),"d/m/Y");
		$pdf->Cell(17,6,$row['rank'],1,0,'C');
		$pdf->Cell(20,6,$id_personal,1,0,'L');
		$pdf->Cell(50,6,$row['nombre_apellido'],1,0,'L');
		$pdf->Cell(30,6,number_format(floatval($row['total_general_vendedor']),$decimals,",","."),1,0,'R');
		$pdf->Cell(30,6,number_format(floatval($row['total_costo_vendedor']),$decimals,",","."),1,0,'R');
		$pdf->SetFont('Arial','B',$font_size);
		$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');
		$pdf->SetFont('Arial','',$font_size);
		$pdf->Cell(20,6,$row['id_moneda'],1,0,'C');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(87,6,'Total General de Ventas Gs.',1,0,'C');
	$pdf->Cell(30,6,number_format(floatval($total_general_factura_gs),$decimals,",","."),1,0,'R');
	$pdf->Cell(30,6,number_format(floatval($total_general_costo_gs),$decimals,",","."),1,0,'R');
	$utilidad=0;
	if(($total_general_sin_iva_gs!=0)||($total_general_costo_gs!=0))
	{
		$utilidad=(($total_general_sin_iva_gs/$total_general_costo_gs)-1)*100;
	}
	$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');

	//Line break	
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(200,6,'==Todos los valores son expresados en Gs.==',0,1,'C');

	$pdf->Output();
?>