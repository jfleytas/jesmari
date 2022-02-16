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

	$fecha_inicio = $_GET['fecha_inicio'];
	$fecha_fin = $_GET['fecha_fin'];
	$producto = $_GET['producto'];
	$cliente = $_GET['cliente'];

	$producto_data = $productos->query("select * from productos where nr = ".$producto);

	foreach($producto_data as $producto_data) {
		$id_producto = $producto_data['id_producto'];
		$descripcion_producto = $producto_data['descripcion'];
	}
	
	//$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	//$id=implode(",",$sel);//seperating ',' from array

	$sql ="select CFV.nr, CFV.fecha_factura, CFV.nr_factura, CFV.nr_cliente, CFV.tipo_factura, CFV.nr_moneda, 
		CFV.total_factura, C.razon_social, M.id_moneda, M.descripcion descripcion_moneda, CFV.cotizacion_compra, DFV.cantidad,DFV.total_linea
		from cabecera_factura_venta CFV join moneda M on CFV.nr_moneda = M.nr
		join detalle_factura_venta DFV on DFV.nr = CFV.nr
		join clientes C on CFV.nr_cliente = C.nr
		where CFV.fecha_factura >= '".$fecha_inicio."' and CFV.fecha_factura <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DFV.nr_producto = ".$producto;	
    }

    if(!empty($cliente))
    {
    	$sql = $sql . " and C.nr = ".$cliente;	
    }
	
	$sql = $sql . " order by CFV.fecha_factura";

	//echo $sql;

	$result = $productos->get_productos($sql);

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
	$pdf->Cell(70,6,'Lista de Venta por Producto');

	//Line break	
	$pdf->Ln();
	$font_size = 10;
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

	$pdf->SetY(52);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Producto: '.$id_producto.' - '.$descripcion_producto);

	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('#','Fecha','Nro. Factura','Tipo','Cliente','Cant.','Monto Factura','Mon.');
	$w=array(10,19,28,10,75,15,25,10);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	//Declarations
	$i= 0;
	@$total_general = 0;
	@$total_venta = 0;
	@$total_cantidad = 0;
	@$decimals = 0;
	foreach($result as $row) {
		$fecha = date_format(date_create($row['fecha_factura']),"d/m/Y");
		$i= $i+1;
		$pdf->Cell(10,6,$i,1,0,'C');
		$pdf->Cell(19,6,$fecha,1,0,'L');
		$pdf->Cell(28,6,$row['nr_factura'],1,0,'L');
		$pdf->Cell(10,6,$row['tipo_factura'],1,0,'C');
		$pdf->Cell(75,6,$row['razon_social'],1,0,'L');
		$pdf->Cell(15,6,number_format(floatval($row['cantidad']),$decimals,",","."),1,0,'R');
		$monto_factura = number_format(floatval($row['total_factura']),$decimals,",",".");
		$monto_venta = number_format(floatval($row['total_linea']),$decimals,",",".");
		//$pdf->Cell(30,6,$monto_factura,1,0,'R');
		$pdf->Cell(25,6,$monto_venta,1,0,'R');
		$pdf->Cell(10,6,$row['id_moneda'],1,0,'C');
		//Total General
		$total_general = $total_general + ($row['total_factura']*$row['cotizacion_compra']);
		$total_venta = $total_venta + ($row['total_linea']*$row['cotizacion_compra']);
		$total_cantidad = $total_cantidad + ($row['cantidad']);
		//Line break	
		$pdf->Ln();
	}
	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(142,6,'Total General',1,0,'C');
	$pdf->Cell(15,6,number_format(floatval($total_cantidad),$decimals,",","."),1,0,'C');
	$pdf->Cell(35,6,number_format(floatval($total_venta),$decimals,",","."),1,0,'C');

	$pdf->Output();
?>