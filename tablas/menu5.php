<?php
  //require '../login/control_login.php'; /*Check if the user is logged into the system*/
  require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
  //require '../css/nombre_empresa.html'; /*Show the Company name*/

  date_default_timezone_set('America/Buenos_Aires');
?>

<!doctype html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../css/estilo_menu_test4.css">
</head>
<body>
<ul class="navigation">
  <li class="nav-item"><a href="#">Home</a></li>
  <li class="nav-item"><a href="#">Portfolio</a></li>
  <li class="nav-item"><a href="#">About</a></li>
  <li class="nav-item"><a href="#">Blog</a></li>
  <li class="nav-item"><a href="#">Contact</a></li>
</ul>

<input type="checkbox" id="nav-trigger" class="nav-trigger" />
<label for="nav-trigger"></label>

<div class="site-wrap">
</div>
</body>
</html>