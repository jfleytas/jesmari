
<?

require '../tablas/menu.php';

    require '../clases/clientes.class.php';
    $clientes = clientes::singleton();
$total_result = $clientes->get_clientes()//We get all the results from the table

?>

<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Light Javascript Table Filter</title>
    <link rel='stylesheet prefetch' href='http://s.cdpn.io/3/bootstrap.min.css'>
  </head>

  <body>

    <section class="container">

	<h2>Light Javascript Table Filter</h2>

	<input type="search" class="light-table-filter" data-table="order-table" data-table-columns="0,2" placeholder="Filter">

	<table class="order-table table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Price</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach($total_result as $total_row):
                    echo '<tr>';
                    echo '<td>'. $total_row['id_cliente'] . '</td>';
                    echo '<td>'. $total_row['razon_social'] . '</td>';
                    echo '<td>'. $total_row['ruc'] . '</td>';
                    echo '<td><a href="clientes.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
                    echo '<td><a onclick="return confirmarBorrado()" href="clientes.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
                    echo '</tr>';
                endforeach;
               ?>
		</tbody>
	</table>

</section>
    
        <script src="js/index.js"></script>
  </body>
</html>
