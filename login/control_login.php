<?php
  session_start(); /* Start the session */
  if(empty($_SESSION['id_user'])) /* If there's no session created, go to the Login page*/
  { 
    echo '<script>alert("No has iniciado sesion")</script> ';
    echo "<script>location.href='../login/inicio.php'</script>";
  }
  else
  {
    echo "<b>Bienvenido/a: " .$_SESSION['id_user'];
    echo"<br><a href= ../login/logout.php>Salir</a></p>";
  } 
?>