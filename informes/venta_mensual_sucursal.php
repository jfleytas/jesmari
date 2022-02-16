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

	$total_mensual = $productos->get_productos('select nr_sucursal, sucursal, anho,
	  sum(case when mes = 1 then total else 0 end) as Enero,
	  sum(case when mes = 2 then total else 0 end) as Febrero,
	  sum(case when mes = 3 then total else 0 end) as Marzo,
	  sum(case when mes = 4 then total else 0 end) as Abril,
	  sum(case when mes = 5 then total else 0 end) as Mayo,
	  sum(case when mes = 6 then total else 0 end) as Junio,
	  sum(case when mes = 7 then total else 0 end) as Julio,
	  sum(case when mes = 8 then total else 0 end) as Agosto,
	  sum(case when mes = 9 then total else 0 end) as Septiembre,
	  sum(case when mes = 10 then total else 0 end) as Octubre,
	  sum(case when mes = 11 then total else 0 end) as Noviembre,
	  sum(case when mes = 12 then total else 0 end) as Diciembre,
	  sum(coalesce(total,0)) as total_general
		from (
		    select  
		      nr_sucursal, S.descripcion as sucursal,
		      extract(month from fecha_factura) as mes,
		      extract(year from fecha_factura) as anho,
		      sum(total_factura * cotizacion_venta) as total
		    from cabecera_factura_venta CFV
		    right join sucursal S
		    on CFV.nr_sucursal = S.nr
		    group by nr_sucursal,sucursal, mes, anho
	    ) venta_mensual
	    group by nr_sucursal, sucursal, anho
	    order by sucursal;');

	$decimals = 0;
	$logo = "../img/logo.jpg";

	//Create a new PDF file
	$pdf=new FPDF('L','mm','A4');
	$pdf->AddPage();
	$font_size = 9;
	$pdf->SetFont('Arial','',$font_size);
	//Set the logo
	$pdf->Cell(10,10, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 20.78), 0, 0, 'L', false);
	//Title
	$pdf->SetY(0);
	$pdf->SetX(220);
    $pdf->Cell(60,20,"Reporte generado por ".$usuario." el: ".date('d-m-Y'),0,0,'C');
    //Line break	
	$pdf->Ln();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->SetY(15);
	$pdf->SetX(120);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(110);
	$pdf->Cell(70,6,'Ventas por Mes por Sucursal');

	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Sucursal','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Total Gral.');
	$w=array(22,20,20,20,20,20,20,20,20,20,20,20,20,23);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],15,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',8);

	foreach($total_mensual as $row) {
		$sucplusyear = 'Suc. '.$row['nr_sucursal'].' / '.$row['anho']; 
		$pdf->Cell(22,6,$sucplusyear,1,0,'C');
		//$pdf->Cell(22,6,$row['sucursal'],1,0,'L');
		$pdf->Cell(20,6,number_format(floatval($row['enero']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['febrero']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['marzo']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['abril']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['mayo']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['junio']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['julio']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['agosto']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['septiembre']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['octubre']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['noviembre']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,number_format(floatval($row['diciembre']),$decimals,",","."),1,0,'R');
		$font_size = 9;
		$pdf->SetFont('Arial','B',$font_size);
		$pdf->SetTextColor(194,8,8);
		$pdf->Cell(23,6,number_format(floatval($row['total_general']),$decimals,",","."),1,0,'R');
		$font_size = 8;
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
	$pdf->Cell(300,6,'==Todos los valores son expresados en Gs.==',0,1,'C');

	$pdf->Output();
?>