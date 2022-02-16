<?php
//Sources
//http://lineadecodigo.com/javascript/validar-password-con-javascript/
//http://www.sitepoint.com/password-hashing-in-php/

    require '../tablas/menu.php';
    
    require '../clases/tablas/users.class.php';
    $users = users::singleton();

    //Declaring variables
    $action=isset($_POST['action']) ? $_POST['action'] : '';
    $rol=isset($_POST['rol']) ? $_POST['rol'] : '';
    $id_user=isset($_POST['id_user']) ? $_POST['id_user'] : '';
    $password_user=isset($_POST['password_user']) ? $_POST['password_user'] : '';
    $nombre_apellido=isset($_POST['nombre_apellido']) ? $_POST['nombre_apellido'] : '';
    $confirm_password=isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    //$query = "select * from users";
    $query = "select * from pg_shadow where usename <> 'postgres' and usename <> 'admin'";
    $total_result = $users->get_users($query);//We get all the results from the table

    if (isset($_GET['modify'])) // Check if the Modify variable has a value and show the required result from the table
    {
        echo '<script>location.href="#modal"</script>';
        $parameter = $_GET['modify'];
        $query = "select * from pg_auth_members as upa, pg_roles as upr, pg_shadow as upu
            WHERE upa.roleid = upr.oid
            AND upu.usesysid = upa.member AND usename = '".$parameter."'";
        $result = $users->get_users($query);//We get the desired result from the table
        foreach($result as $row):
            $id_user = $row['usename'];
            $nombre_apellido = $row['usename'];
            $rol = $row['rolname'];
            $password_user = $row['passwd'];
            $confirm_password = $row['passwd'];
        endforeach;
    }
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
            echo '<td>'. $total_row['usename'] . '</td>';
            echo '<td>'. $total_row['usename'] . '</td>';
            echo '<td><a href="users.php?modify='.$total_row['usename'].'" title="Editar" class = "edit"></a></td>';
            echo '<td><a onclick="return confirmarBorrado()" href="users.php?delete='.$total_row['usename'].'"  title="Borrar" class = "delete"></a></td>';
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
                    <b><label class="control-label">Codigo</label></b>
                    <input type="text" id="id_user" name="id_user" value="<?php echo $id_user;?>" class="boxes" autofocus accesskey="b" style="text-transform:lowercase;" onkeyup="javascript:this.value=this.value.toLowerCase();" required><p>
                    <b><label class="control-label">Nombre y Apellido</label></b>
                    <input type="text" id="nombre_apellido" name="nombre_apellido" value="<?php echo $nombre_apellido;?>" class="boxes" required><p>
                    <b><label class="control-label">Rol</label></b>
                    <select name="rol" required class="boxes">
                        <option value="" selected>Seleccione:</option>
                        <?php 
                            $rol_result = $users->get_users("select * from pg_group");//We get all the results from the table rol
                            foreach ($rol_result as $row) {
                            $rol_nr = $row['groname'];
                            echo '<option value="'.$row['groname'].'"';
                            if ($rol==$rol_nr) echo 'selected="selected"';
                            echo '>'.$row['groname'].'</option>';
                        }?>
                    </select><p>
                    <b><label class="control-label">Contrase&ntilde;a</label></b>
                    <input type="password" id="password_user" name="password_user" value="<?php echo $password_user;?>" class="boxes" required><p>
                    <b><label class="control-label">Confirmar Contrase&ntilde;a</label></b>
                    <input type="password" id="confirm_password" name="confirm_password" value="<?php echo $confirm_password;?>" class="boxes" required><p>
                    <div class="form-actions">
                        <?php if (!empty($usename)){ ?>
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
    if (isset ($_POST['action'])){
    switch($_POST['action'])
    {
        case "Crear": 
            $id_user = $_POST['id_user'];
            $rol = $_POST['rol'];
            $password_user = $_POST['password_user'];
            if ($password_user == $confirm_password)
            {
                $users->insert_users($id_user,$rol,$password_user); 
            }else{
                echo "<script>alert ('Las claves no coinciden')</script>";
            }

            $users->insert_users($id_user,$rol,$password_user);
        break;

        case "Modificar": 
            $id_user = $_POST['id_user'];
            $nombre_apellido = $_POST['nombre_apellido'];
            $rol = $_POST['rol'];
            $password_user = generateHash($_POST['password_user']); 

            $users->update_users($id_user,$nombre_apellido,$rol,$password_user);
            echo "<script>window.location.href = 'users.php'</script>";
        break;
    }
    }
    
    if (isset($_GET['delete'])) //Check if the Delete variable has a value and delete a row
    {
        $id_user = $_GET['delete'];
        $users->delete_users($id_user);
    }
?>