<?php
	session_start();
	//include '../clases/formularios/numero_a_letra.php';
	require '../clases/php/fpdf183/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/factura_venta.class.php';
	
	$factura_venta = factura_venta::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	//$factura_venta = $_GET['factura_venta'];
	$result_config = $productos->query("select * from configuracion");

	$usuario = $_SESSION['id_user'];

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];
	$producto = $_GET['producto'];

	$fecha1 = new DateTime($fecha_inicio);
	$fecha2 = new DateTime($fecha_fin);

	$amountdays = $fecha2->diff($fecha1)->format("%a");

	$fecha_30d = date('Y-m-d', strtotime("-30 days"));

	$sql = "select sum(DFV.cantidad) as cantidad_vendida, DFV.nr_producto, P.id_producto, P.descripcion, S.stock_actual
		from cabecera_factura_venta CFV join detalle_factura_venta DFV on CFV.nr = DFV.nr
		join productos P on DFV.nr_producto = P.nr
		join stock_deposito_sucursal S on S.nr_producto = P.nr
		where CFV.fecha_factura >= '".$fecha_inicio."' and CFV.fecha_factura <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DFV.nr_producto = ".$producto;	
    }
	
	$sql = $sql . " group by DFV.nr_producto, P.id_producto, P.descripcion, S.stock_actual
		order by P.id_producto;";

	@$total_producto = $productos->get_productos($sql);

	$decimals = 2;
	$logo = "../img/logo.jpg";

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A3');
	$pdf->AddPage();
    $font_size = 9;
	$pdf->SetFont('Arial','',$font_size);
	//Set the logo
	$pdf->Cell(10,10, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 20.78), 0, 0, 'L', false);
	//Title
	$pdf->SetY(0);
	$pdf->SetX(220);
    $pdf->Cell(60,20,"Reporte generado por ".$usuario." el: ".date('d-m-Y'),0,0,'C');

	$font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->SetY(15);
	$pdf->SetX(120);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(116);
	$pdf->Cell(70,6,'Rotacion de Stock');
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

	//Line break	
	$pdf->Ln();
	$pdf->SetY(52);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Cant. dias: '.$amountdays);

	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','B',10);
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Nro.','Cod. Producto','Descripcion','Cant. Total Vendida','Venta mensual','Stock Actual','Cant. meses stock');
	$w=array(13,30,80,35,35,30,35);
	//Line break	
	$pdf->Ln();

 	$total_general_producto = 0;
 	$total_producto_sin_iva = 0;
	$total_producto_costo = 0;

	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	//Check the detail
	$i= 1;
    foreach($total_producto as $detalle) {
		$pdf->Cell(13,6,$i,1,0,'C');

		$id_producto = $detalle['id_producto'];
		$descripcion = $detalle['descripcion'];
		$cantidad_vendida = $detalle['cantidad_vendida'];
		$stock_actual = $detalle['stock_actual'];

		$venta_mensual = ($cantidad_vendida/$amountdays)*30;

		$pdf->Cell(30,6,$id_producto,1,0,'L');
		$pdf->Cell(80,6,$descripcion,1,0,'L');
		$pdf->Cell(35,6,number_format(floatval($cantidad_vendida),$decimals,",","."),1,0,'R');
		$pdf->Cell(35,6,number_format(floatval($venta_mensual),$decimals,",","."),1,0,'R');
		$pdf->Cell(30,6,number_format(floatval($stock_actual),$decimals,",","."),1,0,'R');

		$cant_meses = 0;
		if ($stock_actual!=0)
		{
			$cant_meses = $stock_actual/$venta_mensual;
		}
		$pdf->Cell(35,6,number_format(floatval($cant_meses),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
		$i++;	
	}

	$pdf->Output();

	//Create report in excel
	$objPHPExcel = new PHPExcel();
	$row = 1;
	foreach($total_producto as $detalle) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $id_producto);
	    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $descripcion);
	    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $cantidad_vendida);
	    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $stock_actual);
	    $row++;
	}

	$excelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2013');
	$objWriter->save('myFileName.xlsx');
?>