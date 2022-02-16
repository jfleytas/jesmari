<?php
require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
require '../css/nombre_empresa.html'; /*Show the Company name*/

session_start(); /* Start the session */
 
//include_once $_SERVER['DOCUMENT_ROOT']."/jesmari/conexion/Conexion.php";
include_once ("../conexion/Conexion.php");
$db = Conexion::conexion();

if(isset($_POST['submit'])){
  //$sql= "SELECT * FROM users WHERE id_user = :id_user";
  //$sql= "SELECT * FROM pg_shadow WHERE usename = :id_user";
  $sql= "select * from pg_shadow WHERE usename = :id_user and passwd = 'md5' || md5(:password || :id_user)";
  $result = $db->prepare($sql);
  $result->bindParam(":id_user" ,$_POST['id_user']);
  $result->bindParam(":password" ,$_POST['password_user']);
  //$password = $_POST['password_user'];
  $result->execute();

  $user_result=$result->fetch();
  if($user_result > 0)
  {
    /*$passwd = $user_result['passwd'];

  	//if(hash('sha256', ($password)) == $user_result['password_user'])
  	//Verifying the password
      function verify($password, $hashedPassword) 
      {
        return crypt($password, $hashedPassword) == $hashedPassword;
      }


  	if (verify($password, $user_result['passwd']))
    //if (verify($password, $user_result['password_user']))
  	{*/
  		$_SESSION['id_user'] = $_POST['id_user']; 
  		
      $sql= "select * from pg_auth_members as upa, pg_roles as upr, pg_shadow as upu
            WHERE upa.roleid = upr.oid
            AND upu.usesysid = upa.member AND usename = '".$_POST['id_user']."'";
            //echo $sql;
      $result = $db->prepare($sql);
      $result->execute();

      //switch($_POST['id_user'])
      if ($_POST['id_user'] == 'jfleytas')
      {
        $rol = 'ventas'; 
      }else if ($_POST['id_user'] == 'admin'){
        $rol = 'gerencia';
      }else if ($_POST['id_user'] == 'sgomez'){
        $rol = 'administracion';
      }else{
        $rol = 'ventas';
      }

            
      //return $result->fetchAll(); 
      /*$user_result=$result->fetch();
      
        //$result = $users->get_users($query);//We get the desired result from the table
        foreach($user_result as $row):
            $id_user = $row['usename'];
            $nombre_apellido = $row['usename'];
            $rol = $row['rolname'];
            $password_user = $row['passwd'];
            $confirm_password = $row['passwd'];
        endforeach;*/
        $_SESSION['rol'] = $rol; 

        echo "<script>location.href='../tablas/menu.php'</script>";
  	/*}else{
  		echo '<script>alert("USUARIO O CLAVE INCORRECTA")</script> ';
  		echo "<script>location.href='inicio.php'</script>";
  	}*/
  }else{
  	echo '<script>alert("USUARIO O CLAVE INCORRECTA")</script> ';
  	echo "<script>location.href='inicio.php'</script>";	
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../css/estilo_login.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
  <div class="heading">
    <form action="" method="post">
    	<div class="input-group input-group-lg">
        	<span class="input-group-addon"><i class="fa fa-user"></i></span>
        	<input type="text" id= "id_user" name="id_user" class="form-control" placeholder="Usuario">
        	<script type='text/javascript'>document.getElementById('id_user').focus ();</script>
       	</div>
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="fa fa-lock"></i></span>
          <input type="password" name="password_user" id="password_user" class="form-control" placeholder="Contrase&ntilde;a">
        </div><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p><p>
        <button type="submit" name='submit' class="float">Ingresar</button>
       </form>
 		</div>
 </div>
</body>
</html>