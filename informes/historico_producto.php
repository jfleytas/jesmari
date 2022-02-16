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
	$fecha_inicio_saldo = date_format(date_create('2015-01-01'),"Y-m-d");
	$fecha_fin_saldo = date('Y-m-d', strtotime('-1 day', strtotime($fecha_inicio)));
	//echo $fecha_inicio_saldo.' - '.$fecha_fin_saldo;

	$producto_data = $productos->query("select * from productos where nr = ".$producto);

	foreach($producto_data as $producto_data) {
		@$id_producto = $producto_data['id_producto'];
		@$descripcion_producto = $producto_data['descripcion'];
	}

	//Check the saldo
	$sql_saldo = "select producto, sum(total_general) total
		from
		(
		(select P.nr producto, sum(-DFV.cantidad) as total_general
		from productos P join detalle_factura_venta DFV on P.nr = DFV.nr_producto join cabecera_factura_venta FV on DFV.nr = FV.nr 
		where FV.fecha_factura >= '".$fecha_inicio_saldo."' and FV.fecha_factura <= '".$fecha_fin_saldo."' and DFV.nr_producto = '".$producto."' group by P.nr)
		union all 
		(select P.nr producto, sum(DIP.cantidad) as total_general 
		from productos P join detalle_ingreso_proveedor DIP on DIP.nr_producto = P.nr join cabecera_ingreso_proveedor CIP on CIP.nr = DIP.nr 
		where CIP.fecha_ingreso >= '".$fecha_inicio_saldo."' and CIP.fecha_ingreso <= '".$fecha_fin_saldo."' and DIP.nr_producto = '".$producto."' group by P.nr)
		union all 
		(select P.nr producto, sum(DIS.cantidad) as total_general 
		from productos P join detalle_ingreso_stock DIS on DIS.nr_producto = P.nr join cabecera_ingreso_stock CIS on CIS.nr = DIS.nr 
		where CIS.fecha_ingreso >= '".$fecha_inicio_saldo."' and CIS.fecha_ingreso <= '".$fecha_fin_saldo."' and DIS.nr_producto = '".$producto."' group by P.nr)
		union all 
		(select P.nr producto, sum (-(DES.cantidad)) as total_general 
		from productos P join detalle_egreso_stock DES on DES.nr_producto = P.nr join cabecera_egreso_stock CES on CES.nr = DES.nr 
		where CES.fecha_egreso >= '".$fecha_inicio_saldo."' and CES.fecha_egreso <= '".$fecha_fin_saldo."' and DES.nr_producto = '".$producto."' group by P.nr)
		) total_movimiento_stock
		group by producto";

	//echo $sql_saldo;

	$saldo_producto = $productos->query($sql_saldo);
	$saldo_anterior=0;
	foreach($saldo_producto as $saldo_producto) {
		$saldo_anterior = $saldo_producto['total'];
	}
	
	//Query for Factura Venta
	$sql = "(select P.nr nr_producto, P.id_producto, FV.nr comprobante_nr, FV.fecha_factura fecha_comprobante, -(DFV.cantidad)cantidad
		from productos P join detalle_factura_venta DFV on P.nr = DFV.nr_producto
		join cabecera_factura_venta FV on DFV.nr = FV.nr
		where FV.fecha_factura >= '".$fecha_inicio."' and FV.fecha_factura <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DFV.nr_producto = ".$producto;	
    }
	
	$sql = $sql . " order by P.nr, FV.fecha_factura)";

	$sql = $sql . " union ";

	//Query for Factura Compra
	$sql = $sql . "(select P.nr nr_producto, P.id_producto, DIP.nr comprobante_nr, CIP.fecha_ingreso fecha_comprobante, DIP.cantidad cantidad_compra
		from productos P join detalle_ingreso_proveedor DIP on DIP.nr_producto = P.nr
		join cabecera_ingreso_proveedor CIP on CIP.nr = DIP.nr
		where CIP.fecha_ingreso >= '".$fecha_inicio."' and CIP.fecha_ingreso <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DIP.nr_producto = ".$producto;	
    }
	
	$sql = $sql . " order by P.nr, CIP.fecha_ingreso)";

	$sql = $sql . " union ";

	//Query for Ingreso Stock
	$sql = $sql . "(select P.nr nr_producto, P.id_producto, DIS.nr comprobante_nr, CIS.fecha_ingreso fecha_comprobante, DIS.cantidad cantidad_ingreso
		from productos P join detalle_ingreso_stock DIS on DIS.nr_producto = P.nr
		join cabecera_ingreso_stock CIS on CIS.nr = DIS.nr
		where CIS.fecha_ingreso >= '".$fecha_inicio."' and CIS.fecha_ingreso <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DIS.nr_producto = ".$producto;	
    }
	
	$sql = $sql . " order by P.nr, CIS.fecha_ingreso)";

	$sql = $sql . " union ";

	//Query for Egreso Stock
	$sql = $sql . "(select P.nr nr_producto, P.id_producto, DES.nr comprobante_nr, CES.fecha_egreso fecha_comprobante, -(DES.cantidad)cantidad_egreso
		from productos P join detalle_egreso_stock DES on DES.nr_producto = P.nr
		join cabecera_egreso_stock CES on CES.nr = DES.nr
		where CES.fecha_egreso >= '".$fecha_inicio."' and CES.fecha_egreso <= '".$fecha_fin."'";

	if(!empty($producto))
    {
    	$sql = $sql . " and DES.nr_producto = ".$producto;	
    }
	
	$sql = $sql . " order by P.nr, CES.fecha_egreso)";

	$sql = $sql . "order by fecha_comprobante";

	//echo $sql;

	$result = $productos->get_productos($sql);

	$decimals = 2;
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
	$pdf->Cell(70,6,'Historico por Producto');

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

	//Line break	
	$pdf->Ln();
	$pdf->SetY(52);
	$pdf->SetX(10);
	$pdf->Cell(70,6,'Producto: '.$id_producto.' - '.$descripcion_producto);

	//Testing purpose
	/*$pdf->Ln();
	$pdf->SetY(48);
	$pdf->SetX(10);
	$pdf->Cell(60,10,$fecha_inicio.' hasta: '.$fecha_fin,0,1);*/

	//Set the Font
	$font_size = 10;
	$pdf->SetFont('Arial','B',$font_size);
	//Line break	
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('Fecha Comprobante','Comprobante Nr.','Cantidad','Saldo');
	$w=array(35,30,26,26);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();

	@$saldo_stock = $saldo_anterior;
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(117,6,'Saldo anterior: '.$saldo_anterior,1,0,'R');
	$pdf->SetFont('Arial','',9);
	$pdf->Ln();

	foreach($result as $row) {
		$font_size = 9;
		$pdf->SetFont('Arial','',$font_size);
		$cantidad_comprobante=$row['cantidad'];
		$saldo_stock = $saldo_stock + ($cantidad_comprobante);
		$fecha = date_format(date_create($row['fecha_comprobante']),"d/m/Y");
		$pdf->Cell(35,6,$fecha,1,0,'C');
		if ($cantidad_comprobante >0)
		{
			$factura_str='F.C. '.$row['comprobante_nr'];
			//$link="imprimir_factura_venta_A.php?factura_venta=".$row['comprobante_nr'];
		}else{
			$factura_str='F.V. '.$row['comprobante_nr'];
			//$link="<a href='imprimir_factura_venta_A.php?factura_venta=".$row['comprobante_nr'].">";
			//$link="../informes/imprimir_factura_venta_A.php?factura_venta=".$row['comprobante_nr'];
		}
		//$link= "href="."../informes/imprimir_factura_venta_A.php?factura_venta=".$row['comprobante_nr'];
		//$pdf->Cell(30,6,$link,1,0,'R');
		//echo $link;
		//$pdf->Cell(30,6,$factura_str,1,0,'R',false, $link);
		$pdf->Cell(30,6,$row['comprobante_nr'],1,0,'R');
		$pdf->Cell(26,6,number_format(floatval($cantidad_comprobante),$decimals,",","."),1,0,'R');
		$pdf->SetFont('Arial','B',$font_size);
		$pdf->Cell(26,6,number_format(floatval($saldo_stock),$decimals,",","."),1,0,'R');
		//Line break	
		$pdf->Ln();
	}

	$pdf->Output();
?>