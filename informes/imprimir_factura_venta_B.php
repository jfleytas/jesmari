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

	$factura_venta = $_GET['factura_venta'];;

	$result_cabecera = $productos->query("select CFV.nr, to_char(CFV.fecha_factura, 'DD Month YYYY') fecha_factura, CFV.nr_factura, CFV.nr_cliente, C.id_cliente, C.razon_social, CFV.nr_condicion, 
		CCV.descripcion descripcion_condicion, CCV.cant_dias, CFV.nr_sucursal, S.descripcion descripcion_sucursal, CFV.nr_deposito, DS.descripcion descripcion_deposito, CFV.nr_moneda, 
		M.id_moneda, M.descripcion descripcion_moneda, CFV.total_exentas, CFV.total_gravadas, CFV.total_iva, CFV.total_factura, CFV.nr_user, U.id_user, CFV.nr_vendedor, P.nombre_apellido vendedor, 
		C.direccion, C.telefono, C.ruc
        from cabecera_factura_venta CFV join clientes C on CFV.nr_cliente = C.nr
        join condicion_compra_venta CCV on CFV.nr_condicion = CCV.nr 
        join sucursal S on CFV.nr_sucursal = S.nr
        join depositos_stock DS on CFV.nr_deposito = DS.nr
        join moneda M on CFV.nr_moneda = M.nr
        join personal P on CFV.nr_vendedor = P.nr
        join users U on CFV.nr_user = U.nr
		where CFV.nr = '$factura_venta'");

	$result_detalle = $productos->query("select DFV.nr, DFV.nr_producto, P.id_producto, P.descripcion descripcion_producto, DFV.cantidad, DFV.descuento, DFV.precio_lista, DFV.precio_final, DFV.impuesto, DFV.nr_unidad, UM.id_unidad_medida id_unidad,
		DFV.total_exentas_linea, DFV.total_gravadas_linea, DFV.total_iva_linea, DFV.total_linea
        from detalle_factura_venta DFV join productos P on DFV.nr_producto = P.nr
        join unidad_medida UM on DFV.nr_unidad = UM.nr where DFV.nr = '$factura_venta'");

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
	$pdf->SetX(9);
	$pdf->Cell(20,6,'Nota:');
	$pdf->SetY(25);
	$pdf->SetX(30);
	$pdf->Cell(130,6,$factura_venta);

	//Cabecera Header
	$font_size = 12;
	$pdf->SetFont('Arial','',$font_size);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(35);
	$pdf->SetX(9);
	$pdf->Cell(70,6,'Fecha:');
	$pdf->SetY(40);
	$pdf->SetX(9);
	$pdf->Cell(110,6,'Cliente: ');
		
	//Now show the columns
	//Line break	
	//$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_cabecera as $cabecera) {
		$total_general = $cabecera['total_factura'];
		$id_moneda = $cabecera['id_moneda'];
		$descripcion_moneda=strtoupper($cabecera['descripcion_moneda']);
		if ($cabecera['nr_moneda']==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		$datos_cliente = $cabecera['razon_social'];
		$pdf->SetY(35);
		$pdf->SetX(25);
		//Set the Mes description in Spanish
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$time=strtotime($cabecera['fecha_factura']);
		$anho=date("Y",$time);
		$mes=date("m",$time);
		$dia=date("j",$time);
		$fecha_factura= $dia." de ".$meses[$mes-1]. " del ".$anho;
		$pdf->Cell(15,6,$fecha_factura,0,0,'L');
		$pdf->Ln();
		$pdf->SetY(40);
		$pdf->SetX(25);
		$pdf->Cell(45,6,$datos_cliente,0,0,'L');
		//Line break	
		$pdf->Ln();
	}

	//Line break	
	//$pdf->Ln();
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Cantidad','Descripcion','Precio Unitario','Valor de Venta');
	$w=array(25,90,30,35);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	foreach($result_detalle as $detalle) {
		//$pdf->Cell(30,6,$detalle['id_producto'],1,0,'L');
		$cantidad_unidad = number_format(floatval($detalle['cantidad']),$decimals,",",".").' '.$detalle['id_unidad'];
		$pdf->Cell(25,6,$cantidad_unidad,1,0,'R');
		$pdf->Cell(90,6,$detalle['descripcion_producto'],1,0,'L');
		$pdf->Cell(30,6,number_format(floatval($detalle['precio_final']),$decimals,",","."),1,0,'R');
		//$pdf->Cell(15,6,$detalle['impuesto'],1,0,'R');	
		$pdf->Cell(35,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$titulo = 'Total General '. $descripcion_moneda;
	$pdf->Cell(145,6,$titulo,1,0,'L');
	$pdf->Cell(35,6,number_format(floatval($total_general,$decimals),",","."),1,0,'R');

	$pdf->Output();
?>