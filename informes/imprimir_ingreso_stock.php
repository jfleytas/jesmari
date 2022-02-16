<?php
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/ingreso_stock.class.php';
	
	$ingreso_stock = ingreso_stock::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$ingreso_stock = $_GET['ingreso_stock'];

	$result_cabecera = $productos->query("select CIS.nr, to_char(CIS.fecha_ingreso, 'DD-MM-YYYY') fecha_ingreso, CIS.nr_sucursal, S.descripcion descripcion_sucursal, CIS.nr_deposito, DS.descripcion descripcion_deposito, 
        CIS.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, CIS.total_ingreso, CIS.nr_user, U.id_user
        from cabecera_ingreso_stock CIS join sucursal S on CIS.nr_sucursal = S.nr
        join depositos_stock DS on CIS.nr_deposito = DS.nr
        join moneda M on CIS.nr_moneda = M.nr
        join users U on CIS.nr_user = U.nr
		where CIS.nr = '$ingreso_stock'");

	$result_detalle = $productos->query("select DIS.nr, DIS.nr_producto, P.id_producto, P.descripcion descripcion_producto, DIS.cantidad, DIS.costo, DIS.nr_unidad, UM.descripcion descripcion_unidad, DIS.total_linea
        from detalle_ingreso_stock DIS join productos P on DIS.nr_producto = P.nr
        join unidad_medida UM on DIS.nr_unidad = UM.nr where DIS.nr = '$ingreso_stock'");

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
	$pdf->Cell(70,6,'Ingreso de Stock Nro.:');
	$pdf->SetY(25);
	$pdf->SetX(115);
	$pdf->Cell(130,6,$ingreso_stock);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha Ingreso:');
	$pdf->SetY(35);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Sucursal:');
	$pdf->SetY(40);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Deposito:');
	
	//Now show the columns
	//Line break	
	//$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_cabecera as $cabecera) {
		$total_general = $cabecera['total_ingreso'];
		if ($cabecera['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		$pdf->SetY(35);
		$pdf->SetX(40);
		$pdf->Cell(15,6,$cabecera['fecha_ingreso'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(35);
		$pdf->SetX(140);
		$pdf->Cell(118,6,$cabecera['descripcion_sucursal'],0,0,'L');
		$pdf->Ln();	
		$pdf->SetY(40);
		$pdf->SetX(140);
		$pdf->Cell(133,6,$cabecera['descripcion_deposito'],0,0,'L');
		//Line break	
		$pdf->Ln();
	}

	//Line break	
	$pdf->Ln();
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Codigo','Descripcion','Cantidad','Unid. Med.','Costo Gs.','Total');
	$w=array(30,70,20,20,25,30);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_detalle as $detalle) {
		$pdf->Cell(30,6,$detalle['id_producto'],1,0,'L');
		$pdf->Cell(70,6,$detalle['descripcion_producto'],1,0,'L');
		$pdf->Cell(20,6,number_format(floatval($detalle['cantidad']),$decimals,",","."),1,0,'R');
		$pdf->Cell(20,6,$detalle['descripcion_unidad'],1,0,'R');	
		$pdf->Cell(25,6,number_format(floatval($detalle['costo']),$decimals,",","."),1,0,'R');
		$pdf->Cell(30,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(165,6,'Total Ingreso Gs.',1,0,'L');
	$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');

	$pdf->Output();
?>