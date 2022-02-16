<?php
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/ingreso_proveedor.class.php';
	
	$ingreso_proveedor = orden_compra::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array
	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$ingreso_proveedor = $_GET['ingreso_proveedor'];

	$result_cabecera = $productos->query("select CIP.nr, to_char(CIP.fecha_ingreso, 'DD-MM-YYYY') fecha_ingreso, CIP.nr_proveedor, P.id_proveedor, P.descripcion descripcion_proveedor, 
		S.descripcion descripcion_sucursal, CIP.nr_deposito, DS.descripcion descripcion_deposito, CIP.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, 
		CIP.total_exentas, CIP.total_gravadas, CIP.total_iva, CIP.total_ingreso, CIP.nr_user, U.id_user, CIP.orden_compra
        from cabecera_ingreso_proveedor CIP join proveedor P on CIP.nr_proveedor = P.nr
        join sucursal S on CIP.nr_sucursal = S.nr
        join depositos_stock DS on CIP.nr_deposito = DS.nr
        join moneda M on CIP.nr_moneda = M.nr
        join users U on CIP.nr_user = U.nr
		where CIP.nr = '$ingreso_proveedor'");

	$result_detalle = $productos->query("select DIP.nr, DIP.nr_producto, P.id_producto, P.descripcion descripcion_producto, DIP.cantidad, DIP.precio, DIP.precio_final, DIP.impuesto, DIP.nr_unidad, UM.id_unidad_medida id_unidad,
		DIP.total_exentas_linea, DIP.total_gravadas_linea, DIP.total_iva_linea, DIP.total_linea
        from detalle_ingreso_proveedor DIP join productos P on DIP.nr_producto = P.nr
        join unidad_medida UM on DIP.nr_unidad = UM.nr where DIP.nr = '$ingreso_proveedor'");

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
	$pdf->Cell(70,6,'Ingreso Proveedor Nro.:');
	$pdf->SetY(25);
	$pdf->SetX(120);
	$pdf->Cell(130,6,$ingreso_proveedor);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha Ingreso:');
	$pdf->SetY(40);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Proveedor: ');
	$pdf->SetY(45);
	$pdf->SetX(9);
	$pdf->Cell(130,6,'Sucursal:');
	$pdf->SetY(35);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Deposito:');
	$pdf->SetY(40);
	$pdf->SetX(120);
	$pdf->Cell(130,6,'Orden Compra:');

	
	//Now show the columns
	//Line break	
	//$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_cabecera as $cabecera) {
		$total_general = $cabecera['total_ingreso'];
		$id_moneda = $cabecera['id_moneda'];
		if ($cabecera['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		$datos_proveedor = $cabecera['descripcion_proveedor'].'  ('.$cabecera['id_proveedor'].')';
		$pdf->SetY(35);
		$pdf->SetX(40);
		$pdf->Cell(15,6,$cabecera['fecha_ingreso'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(40);
		$pdf->Cell(45,6,$datos_proveedor,0,0,'L');
		$pdf->Ln();
		$pdf->SetY(45);
		$pdf->SetX(40);
		$pdf->Cell(118,6,$cabecera['descripcion_sucursal'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(35);
		$pdf->SetX(140);
		$pdf->Cell(133,6,$cabecera['descripcion_deposito'],0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(150);
		$pdf->Cell(133,6,$cabecera['orden_compra'],0,0,'L');
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
		$pdf->Cell(20,6,number_format(floatval($detalle['cantidad']),$decimals,",","."),1,0,'R');
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