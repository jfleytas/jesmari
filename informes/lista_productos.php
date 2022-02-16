<?php
//require '../css/nombre_empresa.html'; /*Show the Company name*/
/*require('../clases/php/fpdf18/fpdf.php');
$pdf = new FPDF('P','mm','A4');//with page settings
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World!');
$pdf->Cell(60,10,'Powered by FPDF.',0,1,'C');
$pdf->Output();*/
?>

<?php
	session_start();
	//require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
	require '../clases/php/fpdf183/fpdf.php';
	require '../clases/tablas/productos.class.php';
	$productos = productos::singleton();
	
	$usuario = $_SESSION['id_user'];

	$sel =array (1,3);// can use $sel =$_GET['sel']; where $_GET['sel'], sel is a checkbox array value received from prev page..

	$id=implode(",",$sel);//seperating ',' from array

	$result_config = $productos->query("select * from configuracion");

	foreach($result_config as $config) {
		$nombre_empresa = $config['nombre_empresa'];
	}

	$result = $productos->get_productos('select P.id_producto, P.descripcion, P.nr_marca, P.nr_tipo, TP.descripcion descripcion_tipo, M.descripcion descripcion_marca, P.nr_impuesto, I.descripcion descripcion_impuesto, P.nr_unidad_medida, UM.descripcion descripcion_unidad
		from productos P left join Marca M on P.nr_marca = M.nr
		join tipo_impuesto I on P.nr_impuesto = I.nr
		join unidad_medida UM on P.nr_unidad_medida = UM.nr
		join tipo_producto TP on TP.nr = P.nr_tipo');
	//Initialize the 3 columns and the total
	$c_id_producto = "";
	$c_descripcion = "";
	$c_descripcion_tipo = "";
	$c_descripcion_marca = "";
	$c_descripcion_impuesto = "";
	$c_descripcion_unidad = "";

	//For each row, add the field to the corresponding column
	foreach ($result as $row) {
	   $id_producto =$row['id_producto'];
	   $descripcion = wordwrap(($row['descripcion']), 20);

	   $descripcion_tipo = ($row['descripcion_tipo']);
	   $descripcion_marca = $row['descripcion_marca'];
	   $descripcion_impuesto = $row['descripcion_impuesto'];
	   $descripcion_unidad = $row['descripcion_unidad']; 
 
	 	$c_id_producto = $c_id_producto.$id_producto."\n";
	 	$c_descripcion = $c_descripcion.$descripcion."\n";
	 	$c_descripcion_tipo = $c_descripcion_tipo.$descripcion_tipo."\n";
	 	$c_descripcion_marca = $c_descripcion_marca.$descripcion_marca."\n";
		$c_descripcion_impuesto = $c_descripcion_impuesto.$descripcion_impuesto."\n";
		$c_descripcion_unidad = $c_descripcion_unidad.$descripcion_unidad."\n";
	}

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
	$pdf->Cell(70,6,'Lista de Productos y Servicios');

	$font_size = 11;
	$pdf->SetFont('Arial','B',$font_size);
	$pdf->Ln();
	//Header
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$header=array('#','Codigo','Descripcion','Marca','I.V.A','Unid.Med.');
	$w=array(10,35,80,35,16,20);
	//Line break	
	$pdf->Ln();
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	//Line break	
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);
	$i= 0;
	foreach($result as $row) {
		$i= $i+1;
		$pdf->Cell(10,6,$i,1,0,'L');
		$pdf->Cell(35,6,$row['id_producto'],1,0,'L');
		$pdf->Cell(80,6,$row['descripcion'],1,0,'L');
		$pdf->Cell(35,6,$row['descripcion_marca'],1,0,'L');
		$pdf->Cell(16,6,$row['descripcion_impuesto'],1,0,'L');
		$pdf->Cell(20,6,$row['descripcion_unidad'],1,0,'L');
		//Line break	
		$pdf->Ln();
	}

	$pdf->Output();
?>

