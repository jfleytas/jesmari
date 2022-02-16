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

    $sql = "select ranked.*
		from (
		select sum(CFV.total_factura) total_general_factura, sum (CFV.total_factura-CFV.total_iva) as total_general_sin_iva, sum(CFV.total_costo) total_general_costo, count(CFV.nr) cantidad_factura, CFV.nr_cliente, C.id_cliente, C.razon_social, CFV.nr_vendedor, V.id_personal 
        ,rank() over (order by sum(CFV.total_factura) desc) as rank
		from cabecera_factura_venta CFV join clientes C on CFV.nr_cliente = C.nr
		join personal V on CFV.nr_vendedor = V.nr
		where CFV.fecha_factura >= '".$fecha_inicio."' and CFV.fecha_factura <= '".$fecha_fin."'";

	if(!empty($vendedor))
    {
    	$sql = $sql . " and CFV.nr_vendedor = ".$vendedor;	
    }

    $sql = $sql . " group by CFV.nr_cliente, C.id_cliente, C.razon_social, CFV.nr_vendedor, V.id_personal
		) as ranked;";

   //echo $sql;	
	@@$total_cliente = $productos -> query($sql);

	/*@$total_cliente = $productos -> query("select ranked.*
		from (
		select sum(CFV.total_factura) total_general_factura, sum (CFV.total_factura-CFV.total_iva) as total_general_sin_iva, sum(CFV.total_costo) total_general_costo, count(CFV.nr) cantidad_factura, CFV.nr_cliente, C.id_cliente, C.razon_social 
        ,rank() over (order by sum(CFV.total_factura) desc) as rank
		from cabecera_factura_venta CFV join clientes C on CFV.nr_cliente = C.nr
		where CFV.fecha_factura >= '".$fecha_inicio."' and CFV.fecha_factura <= '".$fecha_fin."'
		group by CFV.nr_cliente, C.id_cliente, C.razon_social
		) as ranked;");*/

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
	$pdf->Cell(70,6,'Ranking de ventas por Cliente');
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
	$pdf->SetFont('Arial','B',10);
	//Detalle Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$detalle_header=array('Rank.','Cod. Cliente','Cliente','Vend.','Total Ventas','Total Ventas s/IVA','Total Costo s/IVA','% Util.','Cant.Fact.');
	$w=array(15,30,80,15,35,35,35,20,20);
	//Line break	
	$pdf->Ln();

 	$total_general_factura = 0;
 	$total_general_sin_iva = 0;
	$total_general_costo = 0;

	for($i=0;$i<count($detalle_header);$i++)
		$pdf->Cell($w[$i],6,$detalle_header[$i],1,0,'C',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	//Check detail
    foreach($total_cliente as $detalle) {
		$id_cliente = $detalle['id_cliente'];
		$razon_social = $detalle['razon_social'];
		$vendedor = $detalle['id_personal'];
		$total_factura = $detalle['total_general_factura'];
		$costo_general = $detalle['total_general_costo'];
		$total_factura_sin_iva = $detalle['total_general_sin_iva'];
		$cantidad_factura = $detalle['cantidad_factura'];

		$total_general_factura = $total_general_factura + $total_factura;
		$total_general_sin_iva = $total_general_sin_iva + $total_factura_sin_iva;
		$total_general_costo = $total_general_costo + $costo_general;

		$pdf->Cell(15,6,$detalle['rank'],1,0,'C');
		$pdf->Cell(30,6,$id_cliente,1,0,'L');
		$pdf->Cell(80,6,$razon_social,1,0,'L');
		$pdf->Cell(15,6,$vendedor,1,0,'L');
		$pdf->Cell(35,6,number_format(floatval($total_factura),$decimals,",","."),1,0,'R');
		$pdf->Cell(35,6,number_format(floatval($total_factura_sin_iva),$decimals,",","."),1,0,'R');
		$pdf->Cell(35,6,number_format(floatval($costo_general),$decimals,",","."),1,0,'R');
		$font_size = 10;
		$pdf->SetFont('Arial','B',$font_size);
		$utilidad = (($total_factura_sin_iva/$costo_general)-1)*100;
		$pdf->Cell(20,6,number_format(floatval($utilidad),2,",","."),1,0,'R');
		$pdf->SetFont('Arial','',$font_size);
		$pdf->Cell(20,6,number_format(floatval($cantidad_factura),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();	
	}

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Cell(140,6,'Total General Gs.',1,0,'C');
	$pdf->Cell(35,6,number_format(floatval($total_general_factura),$decimals,",","."),1,0,'R');
	$pdf->Cell(35,6,number_format(floatval($total_general_sin_iva),$decimals,",","."),1,0,'R');
	$pdf->Cell(35,6,number_format(floatval($total_general_costo),$decimals,",","."),1,0,'R');
	$utilidad=0;
	if(($total_general_sin_iva!=0)||($total_general_costo!=0))
	{
		$utilidad=(($total_general_sin_iva/$total_general_costo)-1)*100;
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