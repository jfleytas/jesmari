<?php
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/orden_venta.class.php';
	
	$orden_venta = orden_venta::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array
	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$orden_venta = $_GET['orden_venta'];

	$result_cabecera = $productos->query("select COV.nr, to_char(COV.fecha_orden, 'DD-MM-YYYY') fecha_orden, COV.nr_cliente, C.id_cliente, C.razon_social, COV.nr_condicion, CCV.descripcion descripcion_condicion, COV.nr_sucursal, 
		S.descripcion descripcion_sucursal, COV.nr_deposito, DS.descripcion descripcion_deposito, COV.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, COV.total_exentas, COV.total_gravadas, COV.total_iva, COV.total_orden, COV.nr_user, 
		U.id_user
        from cabecera_orden_venta COV join clientes C on COV.nr_cliente = C.nr
        join condicion_compra_venta CCV on COV.nr_condicion = CCV.nr 
        join sucursal S on COV.nr_sucursal = S.nr
        join depositos_stock DS on COV.nr_deposito = DS.nr
        join moneda M on COV.nr_moneda = M.nr
        join users U on COV.nr_user = U.nr
		where COV.nr = '$orden_venta'");

	$result_detalle = $productos->query("select DOV.nr, DOV.nr_producto, P.id_producto, P.descripcion descripcion_producto, DOV.cantidad, DOV.descuento, DOV.precio_lista, DOV.precio_final, DOV.impuesto, DOV.nr_unidad, UM.id_unidad_medida id_unidad,
		DOV.total_exentas_linea, DOV.total_gravadas_linea, DOV.total_iva_linea, DOV.total_linea
        from detalle_orden_venta DOV join productos P on DOV.nr_producto = P.nr
        join unidad_medida UM on DOV.nr_unidad = UM.nr where DOV.nr = '$orden_venta'");

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
	$pdf->Cell(70,6,'Orden de Venta Nro.:');
	$pdf->SetY(25);
	$pdf->SetX(112);
	$pdf->Cell(130,6,$orden_venta);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha Orden:');
	$pdf->SetY(40);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Cliente: ');
	$pdf->SetY(45);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Condicion de Compra:');
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
		$total_general = $cabecera['total_orden'];
		$id_moneda = $cabecera['id_moneda'];
		if ($cabecera['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		//$datos_cliente = $cabecera['razon_social'].'  ('.$cabecera['id_cliente'].')';
		$datos_cliente = $cabecera['razon_social'];
		$pdf->SetY(35);
		$pdf->SetX(40);
		$pdf->Cell(15,6,$cabecera['fecha_orden'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(40);
		$pdf->Cell(45,6,$datos_cliente,0,0,'L');
		$pdf->Ln();
		$pdf->SetY(45);
		$pdf->SetX(52);
		$pdf->Cell(70,6,$cabecera['descripcion_condicion'],0,0,'L');
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
	$detalle_header=array('Codigo','Descripcion','Cantidad','Precio Unitario','IVA','Total');
	$w=array(30,70,20,25,15,30);
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
		$cantidad_unidad = number_format(floatval($detalle['cantidad']),$decimals,",",".").' '.$detalle['id_unidad'];
		$pdf->Cell(20,6,$cantidad_unidad,1,0,'R');
		$pdf->Cell(25,6,number_format(floatval($detalle['precio_final']),$decimals,",","."),1,0,'R');
		$pdf->Cell(15,6,$detalle['impuesto'],1,0,'R');	
		$pdf->Cell(30,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$titulo = 'Total General '. $id_moneda;
	$pdf->Cell(160,6,$titulo,1,0,'L');
	$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');

	$pdf->Output();
?>