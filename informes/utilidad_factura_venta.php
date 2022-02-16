<?php
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

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];

	$sql = "select CFV.nr nr_factura from cabecera_factura_venta CFV 
		where fecha_factura >= '".$fecha_inicio."' and fecha_factura <= '".$fecha_fin."' order by CFV.fecha_factura, CFV.nr_factura";

	//echo $sql;

	@$cant_factura = $productos -> query($sql);

	$decimals = 0;
	$logo = "../img/logo.jpg";

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A3');
	$pdf->AddPage();
    $font_size = 14;
	$pdf->SetFont('Arial','B',$font_size);
	//Set the logo
	$pdf->Cell(10,10, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 20.78), 0, 0, 'L', false);
	$pdf->SetY(15);
	$pdf->SetX(80);
	$pdf->Cell(60,10,$nombre_empresa,0,1);
	//Line break	
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(60);
	$pdf->Cell(70,6,'Utilidad por Factura Venta');
	//Line break	
	$pdf->Ln();

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

	//Line break	
	$pdf->Ln();
	
	//Print every Factura Venta
    foreach($cant_factura as $detalle_facturas) {
    	$result_cabecera = $productos->query("select CFV.nr, CFV.nr_factura, to_char(CFV.fecha_factura, 'DD-MM-YYYY') fecha_factura, CFV.nr_cliente, C.id_cliente, C.razon_social, CFV.nr_condicion, 
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
	        where CFV.nr = '".$detalle_facturas['nr_factura']."'");

		$result_detalle = $productos->query("select DFV.nr, DFV.nr_producto, P.id_producto, P.descripcion descripcion_producto, DFV.cantidad, DFV.descuento, DFV.precio_lista, DFV.precio_final, DFV.impuesto, DFV.nr_unidad, UM.id_unidad_medida id_unidad, DFV.costo,
			DFV.total_exentas_linea, DFV.total_gravadas_linea, DFV.total_iva_linea, DFV.total_linea
	        from detalle_factura_venta DFV join productos P on DFV.nr_producto = P.nr
	        join unidad_medida UM on DFV.nr_unidad = UM.nr
	        where DFV.nr = '".$detalle_facturas['nr_factura']."'
	        order by P.descripcion");

		//Cabecera Header
		$font_size = 10;
		$pdf->SetFont('Arial','',$font_size);
		//Line break	
		$pdf->Ln();
		//Now show the columns
		//Line break	
		//$pdf->Ln();
		$total_general =0;
		$pdf->SetFont('Arial','',10);
		foreach($result_cabecera as $cabecera) {
			$total_general = $total_general  + $cabecera['total_factura'];
			if ($cabecera['nr_moneda']==1)
			{
				$decimals= 0;
			}else{
				$decimals= 2;
			}	
			$pdf->Cell(30,6,'Nro. Factura');
			$pdf->Cell(100,6,$cabecera['nr_factura'],0,0,'L');
			$pdf->Cell(40,6,'Fecha:');
			$pdf->Cell(15,6,$cabecera['fecha_factura'],0,0,'L');
			$pdf->Ln();
			$pdf->Cell(30,6,'Cliente: ');
			$datos_cliente = $cabecera['razon_social'].'  ('.$cabecera['id_cliente'].')';
			$pdf->Cell(45,6,$datos_cliente,0,0,'L');
			$pdf->Ln();
			$pdf->Cell(30,6,'Sucursal:');
			$pdf->Cell(100,6,$cabecera['descripcion_sucursal'],0,0,'L');
			$pdf->Cell(30,6,'Deposito:');
			$pdf->Cell(100,6,$cabecera['descripcion_deposito'],0,0,'L');
		}

		//Line break	
		//$pdf->Ln();
		//Detalle Header
		$pdf->SetFillColor(255,255,255);
		$pdf->SetDrawColor(0,0,0);
		$detalle_header=array('Codigo','Descripcion','Cantidad','Precio IVA Inc.','IVA','Precio sin IVA','Costo s/IVA','Total IVA Inc.','% Util.');
		$w=array(30,70,20,30,10,30,30,30,20);
		//Line break	
		$pdf->Ln();
		for($i=0;$i<count($detalle_header);$i++)
			$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
		//Line break	
		$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$costo_general = 0;
		$total_general_sin_iva = 0;
		foreach($result_detalle as $detalle) {
			$cantidad = $detalle['cantidad'];
			$impuesto = $detalle['impuesto'];
			$precio_final = $detalle['precio_final'];
			$costo = $detalle['costo'];
			$costo_general = $costo_general + ($costo * $cantidad);

			$pdf->Cell(30,6,$detalle['id_producto'],1,0,'L');
			$pdf->Cell(70,6,$detalle['descripcion_producto'],1,0,'L');
			$cantidad_unidad = number_format(floatval($cantidad),$decimals,",",".").' '.$detalle['id_unidad'];
			$pdf->Cell(20,6,$cantidad_unidad,1,0,'R');
			$pdf->Cell(30,6,number_format(floatval($precio_final),$decimals,",","."),1,0,'R');
			$pdf->Cell(10,6,$impuesto,1,0,'R');	

			$precio_final_sin_iva = $precio_final/(1+($impuesto/100));
			$total_general_sin_iva = $total_general_sin_iva + ($precio_final_sin_iva * $cantidad);
			$pdf->Cell(30,6,number_format(floatval($precio_final_sin_iva),$decimals,",","."),1,0,'R');

			$pdf->Cell(30,6,number_format(floatval($costo),$decimals,",","."),1,0,'R');
			$pdf->Cell(30,6,number_format(floatval($detalle['total_linea']),$decimals,",","."),1,0,'R');

			$font_size = 10;
			$pdf->SetFont('Arial','B',$font_size);
			$utilidad = (($precio_final_sin_iva/$costo)-1)*100;
			$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');
			//Line break	
			$pdf->Ln();
			$pdf->SetFont('Arial','',10);
		}
		$font_size = 11;
		$pdf->SetFont('Arial','B',$font_size);
		$pdf->Cell(190,6,'Total General Gs. c/IVA',1,0,'L');
		$pdf->Cell(60,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');
		$pdf->Ln();
		$pdf->Cell(190,6,'Total General Gs. s/IVA',1,0,'L');
		$pdf->Cell(60,6,number_format(floatval($total_general_sin_iva),$decimals,",","."),1,0,'R');
		$pdf->Ln();
		$pdf->Cell(190,6,'Costo General Gs. s/ IVA',1,0,'L');
		$pdf->Cell(60,6,number_format(floatval($costo_general),$decimals,",","."),1,0,'R');
		$pdf->Ln();
		$pdf->Cell(190,6,'% Utilidad sobre Venta Acumulada',1,0,'L');
		$utilidad_general=0;
		if(($total_general_sin_iva!=0)||($costo_general!=0))
		{
			$utilidad_general=(($total_general_sin_iva/$costo_general)-1)*100;
		}
		$pdf->Cell(20,6,number_format(floatval($utilidad_general),2,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
		$pdf->Ln();
	}

	$pdf->Output();
?>