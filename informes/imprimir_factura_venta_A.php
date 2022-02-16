<?php
	include '../clases/formularios/numero_a_letras2.php';
	require '../clases/php/fpdf18/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/factura_venta.class.php';
	
	$factura_venta = factura_venta::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	$factura_venta = $_GET['factura_venta'];

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

	$result_detalle = $productos->query("select DFV.nr, DFV.nr_producto, P.id_producto, P.codigo_barra, P.descripcion descripcion_producto, DFV.cantidad, DFV.descuento, DFV.precio_lista, DFV.precio_final, DFV.impuesto, DFV.nr_unidad, UM.id_unidad_medida id_unidad,
		DFV.total_exentas_linea, DFV.total_gravadas_linea, DFV.total_iva_linea, DFV.total_linea
        from detalle_factura_venta DFV join productos P on DFV.nr_producto = P.nr
        join unidad_medida UM on DFV.nr_unidad = UM.nr where DFV.nr = '$factura_venta'");

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A4');
	$pdf->SetTitle('Multiventas');
	$pdf->AddPage();
	//Cabecera Header
	//Now show the columns
	//Line break	
	//$pdf->Ln(5);
	$pdf->SetFont('Times','',10);
	foreach($result_cabecera as $cabecera) {
		$nr_moneda=$cabecera['nr_moneda'];
		$id_moneda=$cabecera['id_moneda'];
		$descripcion_moneda=strtoupper($cabecera['descripcion_moneda']);
		if ($nr_moneda==1)
		{
			$decimals= 0;
		}else{
			$decimals= 2;
		}	
		//$pdf->SetY(22);
		//$pdf->SetX(155);
		$pdf->SetXY(155,22);
		//$pdf->Cell(70,6,$cabecera['nr_factura'],0,0,'L');

		if ($cabecera['cant_dias']==0)
		{
			$condicion= 'X';
			$pdf->Ln();
			$pdf->SetXY(167,38);
			$pdf->Cell(70,6,$condicion,0,0,'L');
		}else{
			$condicion= 'X  '.$cabecera['cant_dias'].' d.';
			$pdf->Ln();
			$pdf->SetXY(189,38);
			$pdf->Cell(70,6,$condicion,0,0,'L');
		}
		//$datos_cliente = $cabecera['razon_social'].'  ('.$cabecera['id_cliente'].')';
		$datos_cliente = $cabecera['razon_social'];
		$pdf->Ln();
		$pdf->Ln();
		//Set the Mes description in Spanish
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$time=strtotime($cabecera['fecha_factura']);
		$anho=date("Y",$time);
		$mes=date("m",$time);
		$dia=date("j",$time);
		$pdf->SetXY(23,38);
		$fecha_factura= $dia." de ".$meses[$mes-1]. " del ".$anho;
		$pdf->Cell(15,6,$fecha_factura,0,0,'L');
		$pdf->Ln();
		$pdf->SetXY(28,43);
		$pdf->Cell(45,6,$datos_cliente,0,0,'L');
		$pdf->Ln();
		$pdf->SetXY(11,48);
		$pdf->Cell(118,6,$cabecera['direccion'],0,0,'L');
		$pdf->Ln();	
		$pdf->SetXY(131,48);
		$pdf->Cell(133,6,$cabecera['telefono'],0,0,'L');
		$pdf->Ln();
		$pdf->SetXY(149,43);
		$pdf->Cell(70,6,$cabecera['ruc'],0,0,'L');
		$pdf->Ln();
		$pdf->SetXY(170,45);
		$vendedor = "Vend.: ".$cabecera['vendedor'];
		//$pdf->Cell(70,6,$vendedor,0,0,'L');
		//Totals
		$total_exentas=$cabecera['total_exentas'];
		$total_gravadas=$cabecera['total_gravadas'];
		//$total_factura=number_format($cabecera['total_factura'],$decimals,",","");
		$total_factura=$cabecera['total_factura'];
		$total_iva=$cabecera['total_iva'];
	}

	//Line break	
	$pdf->Ln(16);
	//$pdf->Ln();
	//$pdf->Ln();
	//$pdf->Ln();
    $xstr=0;
    $ystr=62;
	$pdf->SetXY($xstr,$ystr);
	//$pdf->cMargin = 0;
	//Detalle Header
	$pdf->SetFont('Times','',10);
	foreach($result_detalle as $detalle) {
		$pdf->SetXY($xstr,$ystr);
		$pdf->SetFont('Times','',8);
		$pdf->Cell(22,6,$detalle['codigo_barra'],0,0,'R');
		$pdf->SetFont('Times','',10);
		$pdf->Cell(13,6,number_format(floatval($detalle['cantidad']),$decimals,",","."),0,0,'R');
		$pdf->Cell(4,6,' ',0,0,'R');
		//$pdf->Cell(4,6,$detalle['id_unidad'],0,0,'R');
		$pdf->Cell(75,6,$detalle['descripcion_producto'],0,0,'L');
		$pdf->Cell(27,6,number_format(floatval($detalle['precio_final']),$decimals,",","."),0,0,'R');
		if ($detalle['impuesto']==0)
		{
			$pdf->Cell(55,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),0,0,'R');
		}else if ($detalle['impuesto']==5){
			$pdf->Cell(55,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),0,0,'R');
		}else{
			$pdf->Cell(55,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),0,0,'R');	
		}
		
		//Line break	
		//$pdf->Ln(5);
        $ystr=$ystr+5;
	}

	//$total_factura_letras = $id_moneda.'  '.convertir_a_letras($total_factura);
	//$total_factura_letras = $descripcion_moneda.', '.numtoletras($total_factura).'--';
	$total_factura_letras = $id_moneda.', '.numtoletras($total_factura).'--';
	//$pdf->Ln();
	$pdf->SetXY(80,128);
	$pdf->Cell(70,6,number_format(floatval($total_exentas),$decimals,",","."),0,0,'R');
	//$pdf->Ln();
	$pdf->SetXY(127,128);
	$pdf->Cell(70,6,number_format(floatval($total_gravadas),$decimals,",","."),0,0,'R');
	//$pdf->Ln();
	$pdf->SetXY(127,135);
	$pdf->Cell(70,6,number_format(floatval($total_factura),$decimals,",","."),0,0,'R');
	//$pdf->Ln();
	//This is 10% cell
	$pdf->SetXY(103,147);
	$pdf->Cell(70,6,number_format(floatval($total_iva),$decimals,",","."),0,0,'L');
	//$pdf->Ln();
	$pdf->SetXY(165,147);
	$pdf->Cell(70,6,number_format(floatval($total_iva),$decimals,",","."),0,0,'L');
	//$pdf->Ln();
	$pdf->SetFont('Times','',9);
	$pdf->SetXY(20,135);
	$pdf->Cell(70,6,$total_factura_letras,0,0,'L');

	$pdf->Output();
?>