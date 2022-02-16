<?php
	session_start();
	require '../clases/php/fpdf183/fpdf.php';
	//require '../css/nombre_empresa.html'; /*Show the Company name*/
	/*require '../clases/formularios/orden_venta.class.php';
	
	$orden_venta = orden_venta::singleton();*/

	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkdate(month, day, year)box array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	//$orden_venta = $_GET['orden_venta'];
	$usuario = $_SESSION['id_user'];

	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];

	@$cant_orden = $productos -> query("select COV.nr nr_orden from cabecera_orden_venta COV 
		where COV.nr not in (select orden_venta from cabecera_factura_venta) 
		and COV.fecha_orden >= '".$fecha_inicio."' and COV.fecha_orden <= '".$fecha_fin."'
		order by COV.nr");

	$logo = "../img/logo.jpg";

	//Create a new PDF file
	$pdf=new FPDF('P','mm','A4');
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
	$pdf->Cell(70,6,'Ordenes de Venta Pendientes de Facturacion');
	//Line break
	//Title
	$font_size = 9;
	$pdf->SetFont('Arial','',$font_size);
	$pdf->SetY(0);
	$pdf->SetX(141);
    $pdf->Cell(60,20,"Reporte generado por ".$usuario." el: ".date('d-m-Y'),0,0,'C');

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

    //Print every pending Orden de Venta
    foreach($cant_orden as $detalle_orden_pendiente) {
		$result_cabecera = $productos->query("select COV.nr, to_char(COV.fecha_orden, 'DD-MM-YYYY') fecha_orden, COV.nr_cliente, C.id_cliente, C.razon_social, COV.nr_condicion, CCV.descripcion descripcion_condicion, COV.nr_sucursal, 
			S.descripcion descripcion_sucursal, COV.nr_deposito, DS.descripcion descripcion_deposito, COV.nr_moneda, M.id_moneda, M.descripcion descripcion_moneda, COV.total_exentas, COV.total_gravadas, COV.total_iva, COV.total_orden, COV.nr_user, 
			U.id_user
	        from cabecera_orden_venta COV join clientes C on COV.nr_cliente = C.nr
	        join condicion_compra_venta CCV on COV.nr_condicion = CCV.nr 
	        join sucursal S on COV.nr_sucursal = S.nr
	        join depositos_stock DS on COV.nr_deposito = DS.nr
	        join moneda M on COV.nr_moneda = M.nr
	        join users U on COV.nr_user = U.nr
			where COV.nr = '".$detalle_orden_pendiente['nr_orden']."'");

		$result_detalle = $productos->query("select DOV.nr, DOV.nr_producto, P.id_producto, P.descripcion descripcion_producto, DOV.cantidad, DOV.descuento, DOV.precio_lista, DOV.precio_final, DOV.impuesto, DOV.nr_unidad, UM.id_unidad_medida id_unidad,
			DOV.total_exentas_linea, DOV.total_gravadas_linea, DOV.total_iva_linea, DOV.total_linea
	        from detalle_orden_venta DOV join productos P on DOV.nr_producto = P.nr
	        join unidad_medida UM on DOV.nr_unidad = UM.nr where DOV.nr = '".$detalle_orden_pendiente['nr_orden']."'");

	    $font_size = 10;
		$pdf->SetFont('Arial','',$font_size);	
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(45,6,'Orden de Venta Nro.:');
		$pdf->Cell(90,6,$detalle_orden_pendiente['nr_orden']);

		//Cabecera Header
		//Line break	
		$pdf->Ln();
		//Now show the columns
		//Line break	
		//$pdf->Ln();
		$pdf->SetFont('Arial','',10);
		$total_general = 0;
		foreach($result_cabecera as $cabecera) {
			$total_general = $cabecera['total_orden'];
			if ($cabecera['nr_moneda']==1)
			{
				$decimals= 0;
			}else{
				$decimals= 2;
			}	
			$datos_cliente = $cabecera['razon_social'].'  ('.$cabecera['id_cliente'].')';
			$pdf->Cell(45,6,'Fecha Orden:');
			$pdf->Cell(50,6,$cabecera['fecha_orden'],0,0,'L');
			//$pdf->Ln();
			$pdf->Cell(15,6,'Cliente: ');
			$pdf->Cell(90,6,$datos_cliente,0,0,'L');
		}

		//Line break	
		//$pdf->Ln();
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
		$pdf->Cell(160,6,'Total Orden Gs.',1,0,'L');
		$pdf->Cell(30,6,number_format(floatval($total_general),$decimals,",","."),1,0,'R');
		
		//Line break	
		$pdf->Ln();
	}

	$pdf->Output();
?>