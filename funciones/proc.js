<?php
include 'conexion.php';
$q=$_POST['q'];
$con=conexion();
$res=mysql_query("select * from pais where cod_cont=".$q."",$con);
?>
<select>
<?php while($fila=mysql_fetch_array($res)){ ?>
<option><?php echo $fila[nombre]; ?></option>
<?php } ?>
</select>