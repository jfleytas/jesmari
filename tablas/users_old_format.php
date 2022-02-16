<?php
//Sources
//http://lineadecodigo.com/javascript/validar-password-con-javascript/
//http://www.sitepoint.com/password-hashing-in-php/

    require '../tablas/menu.php';
    
    require '../clases/tablas/users.class.php';
    $users = users::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $nr=isset($_POST['nr']) ? $_POST['nr'] : '';
    $id_user=isset($_POST['id_user']) ? $_POST['id_user'] : '';
    $hash_password=isset($_POST['hash_password']) ? $_POST['hash_password'] : '';
    $nombre_apellido=isset($_POST['nombre_apellido']) ? $_POST['nombre_apellido'] : '';
    $confirm_password=isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    //$query = "select * from users";
    $query = "select * from pg_shadow";
    $total_result = $users->get_users($query);//We get all the results from the table

    /*if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $result = $users->get_users("select * from users where nr = '$parameter'");//We get the desired result from the table
        foreach($result as $row):
            $nr = $row['nr'];
            $id_user = $row['id_user'];
            $nombre_apellido = $row['nombre_apellido'];
            $hash_password = $row['password_user'];
            $confirm_password = $row['password_user'];
        endforeach;
    }*/
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../css/estilos_abm.css" />
</head>
<body>
<h2>Usuarios</h2>
<a href="#modal" class = "add">Nuevo</a>
<table class="list">
	<thead>
        <tr>
          	<th>Codigo</th>
           	<th>Nombre y Apellido</th>
          	<th>Editar</th>
            <th>Eliminar</th>
        </tr>
    </thead>
	<tbody>
		<?php
            foreach($total_result as $total_row):
            echo '<tr>';
            //echo '<td>'. $total_row['id_user'] . '</td>';
            //echo '<td>'. $total_row['nombre_apellido'] . '</td>';
            echo '<td>'. $total_row['usename'] . '</td>';
            //echo '<td><a href="users.php?modify='.$total_row['nr'].'" title="Editar" class = "edit"></a></td>';
            //echo '<td><a onclick="return confirmarBorrado()" href="users.php?delete='.$total_row['nr'].'"  title="Borrar" class = "delete"></a></td>';
            echo '</tr>';
            endforeach;
    	?>
    </tbody>
	</table>
	<div id="modal" class="modalstyle">
		<div class="modalbox movedown">
            <caption align="center">Usuarios</caption><hr><p>
			<a href="users.php" title="Cerrar" class="close">X</a>
			<div class="container">
                <div class="span10 offset1">
                    <form class="form-horizontal" action="users.php" method="POST" name = "users" autocomplete = "off">
                    <p hidden><input type="number" id="nr" name="nr" value="<?php echo $nr;?>"> </p>
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_user" name="id_user" value="<?php echo $id_user;?>" class="boxes" autofocus accesskey="b" style="text-transform:lowercase;" onkeyup="javascript:this.value=this.value.toLowerCase();" required><p>
                    <b><label class="control-label">Nombre y Apellido</label></b>
                    <input type="text" id="nombre_apellido" name="nombre_apellido" value="<?php echo $nombre_apellido;?>" class="boxes" required><p>
                    <b><label class="control-label">Contrase&ntilde;a</label></b>
                    <input type="password" id="hash_password" name="hash_password" value="<?php echo $hash_password;?>" class="boxes" required><p>
                    <b><label class="control-label">Confirmar Contrase&ntilde;a</label></b>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?php echo $confirm_password;?>" class="boxes" required><p>
                    <div class="form-actions">
                        <?php if (!empty($nr)){ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Modificar">
                            <a class="btn" href="users.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }else{ ?>
                            <p><p><input type="submit" class="btn-success" id="action" name="action" value ="Crear">
                            <a class="btn" href="users.php" onclick="alert("Operacion Cancelada")";>Cancelar</a>
                        <?php }?>
                    </div>
                    </form>
                </div>
            </div> 
		</div>
	</div>

    <script language="Javascript">
        function confirmarBorrado()
        {
            eliminar=confirm("¿Deseas eliminar este registro?");
            if (eliminar)
                return true;
            else
                return false;
                window.location.href = "users.php";
        }
    </script>

</form>
</body>
</html>


<?php
    //Hashing the password
    function generateHash($hash_password) 
    {
        if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) 
        {
            $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
            //$salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
            $salt = $salt . substr(md5(uniqid(rand(), true)), 0, 22);
            return crypt($hash_password, $salt);
        }
    }

    if (isset ($_POST['action'])){
    switch($_POST['action'])
    {
        case "Crear": 
            $id_user = $_POST['id_user'];
            $rol = 'gerencia';
            //$nombre_apellido = $_POST['nombre_apellido'];
            $password_user = $_POST['hash_password'];
            if ($hash_password == $confirm_password)
            {
                $hash_password = generateHash($_POST['hash_password']);  
            }else{
                echo "<script>alert ('Las claves no coinciden')</script>";
            }

            //$users->insert_users($id_user,$nombre_apellido,$hash_password);
            $users->insert_users($id_user,$rol,$password_user);
        break;

        case "Modificar": 
            $nr = $_POST['nr'];
            $id_user = $_POST['id_user'];
            $nombre_apellido = $_POST['nombre_apellido'];
            $hash_password = generateHash($_POST['hash_password']); 

            $users->update_users($nr,$id_user,$nombre_apellido,$hash_password);
            echo "<script>window.location.href = 'users.php'</script>";
        break;
    }
    }
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $nr = $_GET['delete'];
        $users->delete_users($nr);
    }
?>