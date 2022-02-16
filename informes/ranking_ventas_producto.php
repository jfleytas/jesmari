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
	$vendedor = $_GET['vendedor'];
	$cliente = $_GET['cliente'];

	$vendedor_data = $productos->query("select * from personal where nr = ".$vendedor);

	foreach($vendedor_data as $vendedor_data) {
		@$id_vendedor = $vendedor_data['id_personal'];
		@$nombre_apellido = $vendedor_data['nombre_apellido'];
	}

	$cliente_data = $productos->query("select * from clientes where nr = ".$cliente);

	foreach($cliente_data as $cliente_data) {
		@$id_cliente = $cliente_data['id_cliente'];
		@$razon_social = $cliente_data['razon_social'];
	}

    $sql = "select ranked.*
		from (
		select sum(DFV.total_linea) total_general_producto, sum (DFV.total_linea-DFV.total_iva_linea) as total_producto_sin_iva, sum(DFV.cantidad*DFV.costo) total_producto_costo, count(CFV.nr) cantidad_factura, DFV.nr_producto, P.id_producto, P.descripcion 
        ,rank() over (order by sum(DFV.total_linea) desc) as rank
		from cabecera_factura_venta CFV join detalle_factura_venta DFV on CFV.nr = DFV.nr
		join productos P on DFV.nr_producto = P.nr
		where CFV.fecha_factura >= '".$fecha_inicio."' and CFV.fecha_factura <= '".$fecha_fin."'";

    if(!empty($vendedor))
    {
    	$sql = $sql . " and CFV.nr_vendedor = ".$vendedor;	
    }

    if(!empty($cliente))
    {
    	$sql = $sql . " and CFV.nr_cliente = ".$cliente;	
    }

    $sql = $sql . " group by DFV.nr_producto, P.id_producto, P.descripcion
		) as ranked";

    @$total_producto = $productos -> query($sql);

	$decimals = 0;
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
	$pdf->SetX(100);
	$pdf->Cell(70,6,'Ranking de ventas por Producto');
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

	if(!empty($vendedor))
    {
	    //Line break	
		$pdf->Ln();
		$pdf->SetY(52);
		$pdf->SetX(10);
		$pdf->Cell(70,6,'Vendedor: '.$id_vendedor.' - '.$nombre_apellido);
	}

	if(!empty($cliente))
    {
		//Line break	
		$pdf->Ln();
		$pdf->SetY(58);
		$pdf->SetX(10);
		$pdf->Cell(70,6,'Cliente: '.$id_cliente.' - '.$razon_social);
	}

	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','B',10);
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Ranking','Cod. Producto','Descripcion','Total Ventas','Total Ventas s/IVA','Total Costo s/IVA','% Util.','Cant.Fact.');
	$w=array(17,30,80,35,35,35,20,20);
	//Line break	
	$pdf->Ln();

 	$total_general_producto = 0;
 	$total_producto_sin_iva = 0;
	$total_producto_costo = 0;

	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	//Check detail
    foreach($total_producto as $detalle) {
		$id_producto = $detalle['id_producto'];
		$descripcion = $detalle['descripcion'];
		$total_factura = $detalle['total_general_producto'];
		$costo_general = $detalle['total_producto_costo'];
		$total_factura_sin_iva = $detalle['total_producto_sin_iva'];
		$cantidad_factura = $detalle['cantidad_factura'];

		$total_general_producto = $total_general_producto + $total_factura;
		$total_producto_sin_iva = $total_producto_sin_iva + $total_factura_sin_iva;
		$total_producto_costo = $total_producto_costo + $costo_general;

		$pdf->Cell(17,6,$detalle['rank'],1,0,'C');
		$pdf->Cell(30,6,$id_producto,1,0,'L');
		$pdf->Cell(80,6,$descripcion,1,0,'L');
		$pdf->Cell(35,6,number_format(floatval($total_factura),$decimals,",","."),1,0,'R');
		$pdf->Cell(35,6,number_format(floatval($total_factura_sin_iva),$decimals,",","."),1,0,'R');
		$pdf->Cell(35,6,number_format(floatval($costo_general),$decimals,",","."),1,0,'R');
		$font_size = 10;
		$pdf->SetFont('Arial','B',$font_size);
		$utilidad = (($total_factura_sin_iva/$costo_general)-1)*100;
		$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(20,6,number_format(floatval($cantidad_factura),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();	
	}

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(127,6,'Total General Gs.',1,0,'C');
	$pdf->Cell(35,6,number_format(floatval($total_general_producto),$decimals,",","."),1,0,'R');
	$pdf->Cell(35,6,number_format(floatval($total_producto_sin_iva),$decimals,",","."),1,0,'R');
	$pdf->Cell(35,6,number_format(floatval($total_producto_costo),$decimals,",","."),1,0,'R');
	$utilidad=0;
	if(($total_producto_sin_iva!=0)||($total_producto_costo!=0))
	{
		$utilidad=(($total_producto_sin_iva/$total_producto_costo)-1)*100;
	}
	$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');

	//Line break	
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(200,6,'==Todos los valores son expresados en Gs.==',0,1,'C');

	$pdf->Output();
?>