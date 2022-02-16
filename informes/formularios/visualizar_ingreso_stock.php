<?php
	require('../clases/php/fpdf18/fpdf.php');
	require '../clases/tablas/ingreso_stock.class.php';
	$ingreso_stock = ingreso_stock::singleton();


	$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	$id=implode(",",$sel);//seperating ',' from array

	$result = $ingreso_stock->get_ingreso_stock('select P.id_producto, P.descripcion, S.stock_actual, S.stock_a_recibir, S.stock_a_facturar, S.nr_sucursal, SU.descripcion sucursal_descripcion, S.nr_deposito, DS.descripcion deposito_descripcion
		from ingreso_stock P join stock_deposito_sucursal S on P.nr = S.nr_producto
		join sucursal SU on S.nr_sucursal = SU.nr
		join depositos_stock DS on S.nr_deposito = DS.nr order by P.descripcion');
	//Initialize the 3 columns and the total
	$c_id_producto = "";
	$c_descripcion = "";
	$c_stock_actual = "";
	$c_stock_a_recibir = "";
	$c_stock_a_facturar = "";
	$c_sucursal_descripcion = "";
	$c_deposito_descripcion = "";

	//For each row, add the field to the corresponding column
	foreach ($result as $row) {
	   $id_producto =$row['id_producto'];
	   $descripcion = ($row['descripcion']);
	   $stock_actual = ($row['stock_actual']);
	   $stock_a_recibir = ($row['stock_a_recibir']);
	   $stock_a_facturar = ($row['stock_a_facturar']);
	   //$sucursal_descripcion = $row['sucursal_descripcion'];
	   $sucursal_descripcion = $row['nr_sucursal'];
	   $deposito_descripcion = $row['nr_deposito'];
	   //$deposito_descripcion = $row['deposito_descripcion'];
 
	 	$c_id_producto = $c_id_producto.$id_producto."\n";
	 	$c_descripcion = $c_descripcion.$descripcion."\n";
	 	$c_stock_actual = $c_stock_actual.$stock_actual."\n";
	 	$c_stock_a_recibir = $c_stock_a_recibir.$stock_a_recibir."\n";
		$c_stock_a_facturar = $c_stock_a_facturar.$stock_a_facturar."\n";
		$c_sucursal_descripcion = $c_sucursal_descripcion.$sucursal_descripcion."\n";
		$c_deposito_descripcion = $c_deposito_descripcion.$deposito_descripcion."\n";
	}


	//Create a new PDF file
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(60,10,'JESMARI S.R.L.',0,1);
	$pdf->Cell(70,6,'Lista de Stock por Deposito y Sucursal');
	//$pdf->Cell(10,20,'Lista de ingreso_stock y Servicios');

	//Now show the columns
	$pdf->SetY(41);
	$pdf->SetX(5);
	$pdf->MultiCell(30,6,'Codigo',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(5);
	$pdf->MultiCell(30,6,$c_id_producto,1,'L');
	$pdf->SetY(41);
	$pdf->SetX(35);
	$pdf->MultiCell(70,6,'Descripcion',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(35);
	$pdf->MultiCell(70,6,$c_descripcion,1,'L');
	$pdf->SetY(41);
	$pdf->SetX(105);
	$pdf->MultiCell(20,6,'Sucursal',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(105);
	$pdf->MultiCell(20,6,$c_sucursal_descripcion,1);
	$pdf->SetY(41);
	$pdf->SetX(125);
	$pdf->MultiCell(16,6,'Depos.',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(125);
	$pdf->MultiCell(16,6,$c_deposito_descripcion,1);
	$pdf->SetY(35);
	$pdf->SetX(141);
	$pdf->MultiCell(63,6,'STOCK',1,'C');
	$pdf->SetY(41);
	$pdf->SetX(141);
	$pdf->MultiCell(20,6,'Actual',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(141);
	$pdf->MultiCell(20,6,$c_stock_actual,1,'R');
	$pdf->SetY(41);
	$pdf->SetX(161);
	$pdf->MultiCell(20,6,'A Recibir',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(161);
	$pdf->MultiCell(20,6,$c_stock_a_recibir,1,'R');
	$pdf->SetY(41);
	$pdf->SetX(181);
	$pdf->MultiCell(23,6,'A Facturar',1,'C');
	$pdf->SetY(47);
	$pdf->SetX(181);
	$pdf->MultiCell(23,6,$c_stock_a_facturar,1,'R');

	$pdf->Output();
?>