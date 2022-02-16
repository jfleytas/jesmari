<?php
/*require('../clases/php/fpdf18/fpdf.php');
$pdf = new FPDF('P','mm','A4');//with page settings
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World!');
$pdf->Cell(60,10,'Powered by FPDF.',0,1,'C');
$pdf->Output();*/
?>

<?php
	require('../clases/php/fpdf18/fpdf.php');
	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();


	$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	$id=implode(",",$sel);//seperating ',' from array

	$result = $productos->get_productos('select * from productos');
	//Initialize the 3 columns and the total
	$c_code = "";
	$c_name = "";
	$c_price = "";
	$total = 0;

	//For each row, add the field to the corresponding column
	foreach ($result as $row) {
	   $code =$row['id_producto'];
	   $name = ($row['descripcion']);
	   $real_price = $row['nr_marca'];
	   $show =$row['empaque'];

	 $c_code = $c_code.$code."\n";
	 $c_name = $c_name.$name."\n";
	 $c_price = $c_price.$show."\n";

	//Sum all the Prices (TOTAL)
	//    $total = $total+$real_price;
	}

	$total = $total;

	//Create a new PDF file
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(10,10,'Hello World!');

	//Now show the 3 columns
	$pdf->SetY(26);
	$pdf->SetX(15);
	$pdf->MultiCell(40,6,$c_code,1);
	$pdf->SetY(26);
	$pdf->SetX(55);
	$pdf->MultiCell(90,6,$c_name,1);
	$pdf->SetY(26);
	$pdf->SetX(145);
	$pdf->MultiCell(30,6,$c_price,1,'R');
	$pdf->SetX(145);
	$pdf->MultiCell(30,6,'$ '.$total,1,'R');

	$pdf->Output();
?>