<?php
  require '../login/control_login.php'; /*Check if the user is logged into the system*/
  require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
  require '../css/nombre_empresa.html'; /*Show the Company name*/

  date_default_timezone_set('America/Buenos_Aires');
?>

<!doctype html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../css/estilo_menu_test6.css">
</head>
<body style="padding: 3em 0">
<div>
    <nav class="menu">
      <ul>
        <input type="radio" name="menu" id="archive" checked>
        <li>
          <label for="archive" class="title"><i class="fa fa-folder"></i>Archive</label>
          <a href="../formularios/orden_compra.php">Orden de Compra</a>
          <a href="../formularios/factura_compra.php">Factura Compra</a>
          <a href="../formularios/ingreso_proveedor.php">Ingreso Proveedor</a>
          <a href="../formularios/orden_pago.php">Pagos</a>
        </li>
        <input type="radio" name="menu" id="edit">
        <li>
          <label for="edit" class="title"><i class="fa fa-edit"></i>Edit</label>
          <a href="#">Copy</a>
          <a href="#">Cut</a>
          <a href="#">Paste</a>
          <a href="#">Undo</a>
        </li>
        <input type="radio" name="menu" id="tools">
        <li>
          <label for="tools" class="title"><i class="fa fa-gavel"></i>Tools</label>
          <a href="#">Build</a>
          <a href="#">Macros</a>
          <a href="#">Command</a>
          <a href="#">Snippets</a>
        </li>
        <input type="radio" name="menu" id="preferences">
        <li>
          <label for="preferences" class="title"><i class="fa fa-gears"></i>Preferences</label>
          <a href="#">Browser</a>
          <a href="#">Settings</a>
          <a href="#">Packages</a>
          <a href="#">Theme</a>
        </li>
        <input type="radio" name="menu" id="formularios">
        <li>
          <label for="formularios" class="title"><i class="fa fa-gears"></i>Formularios</label><!-- Formularios menu -->
          <label for="compras" class="title"><i class="fa fa-gears"></i>Compras</label><!-- Formularios menu -->
          <a href="../formularios/orden_compra.php">Orden de Compra</a>
          <a href="../formularios/factura_compra.php">Factura Compra</a>
          <a href="../formularios/ingreso_proveedor.php">Ingreso Proveedor</a>
          <a href="../formularios/orden_pago.php">Pagos</a>       
        </li>

      </ul>
    </nav>
    <main class="main">
      <h1>This is a vertical accordion menu only HTML and CSS</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum itaque quae eos natus est asperiores consectetur sit, deserunt saepe totam tenetur numquam dicta harum distinctio accusamus, sed minima reiciendis expedita in. Sint, numquam recusandae id repellendus delectus porro, ipsa a harum obcaecati blanditiis cum accusantium optio voluptate dicta voluptatum. Ipsa nobis aliquid accusantium illum aliquam impedit! Sunt est aliquid ipsam sequi vel repudiandae minima alias earum facere nisi nostrum voluptatum tenetur, iusto illo harum aliquam reprehenderit porro molestias quas possimus voluptatem, doloribus, maiores ea ullam. Sint accusamus vel adipisci delectus nesciunt inventore, exercitationem saepe nulla, dignissimos cum amet consectetur odit voluptas. Laudantium corrupti modi laboriosam aspernatur sed veniam alias hic distinctio, fuga ullam incidunt blanditiis magnam recusandae numquam neque amet enim ratione! Qui numquam obcaecati quas nobis neque. Magni, autem, optio. Esse iste aut quam corrupti qui laboriosam molestias quisquam totam, non consequuntur mollitia maiores itaque et amet nisi voluptatem voluptatum sed dolorem, expedita nam aliquam. Voluptatum quisquam odio ea ullam esse exercitationem ipsum recusandae. Numquam recusandae ex ullam at nobis soluta voluptatum! Ipsum et, ipsam explicabo! Aliquid beatae commodi sint voluptatibus laborum facilis vitae, quaerat, suscipit dolore deserunt alias totam eaque blanditiis rerum, dolor voluptates itaque perferendis repudiandae similique?</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum itaque quae eos natus est asperiores consectetur sit, deserunt saepe totam tenetur numquam dicta harum distinctio accusamus, sed minima reiciendis expedita in. Sint, numquam recusandae id repellendus delectus porro, ipsa a harum obcaecati blanditiis cum accusantium optio voluptate dicta voluptatum. Ipsa nobis aliquid accusantium illum aliquam impedit! Sunt est aliquid ipsam sequi vel repudiandae minima alias earum facere nisi nostrum voluptatum tenetur, iusto illo harum aliquam reprehenderit porro molestias quas possimus voluptatem, doloribus, maiores ea ullam. Sint accusamus vel adipisci delectus nesciunt inventore, exercitationem saepe nulla, dignissimos cum amet consectetur odit voluptas. Laudantium corrupti modi laboriosam aspernatur sed veniam alias hic distinctio, fuga ullam incidunt blanditiis magnam recusandae numquam neque amet enim ratione! Qui numquam obcaecati quas nobis neque. Magni, autem, optio. Esse iste aut quam corrupti qui laboriosam molestias quisquam totam, non consequuntur mollitia maiores itaque et amet nisi voluptatem voluptatum sed dolorem, expedita nam aliquam. Voluptatum quisquam odio ea ullam esse exercitationem ipsum recusandae. Numquam recusandae ex ullam at nobis soluta voluptatum! Ipsum et, ipsam explicabo! Aliquid beatae commodi sint voluptatibus laborum facilis vitae, quaerat, suscipit dolore deserunt alias totam eaque blanditiis rerum, dolor voluptates itaque perferendis repudiandae similique?</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum itaque quae eos natus est asperiores consectetur sit, deserunt saepe totam tenetur numquam dicta harum distinctio accusamus, sed minima reiciendis expedita in. Sint, numquam recusandae id repellendus delectus porro, ipsa a harum obcaecati blanditiis cum accusantium optio voluptate dicta voluptatum. Ipsa nobis aliquid accusantium illum aliquam impedit! Sunt est aliquid ipsam sequi vel repudiandae minima alias earum facere nisi nostrum voluptatum tenetur, iusto illo harum aliquam reprehenderit porro molestias quas possimus voluptatem, doloribus, maiores ea ullam. Sint accusamus vel adipisci delectus nesciunt inventore, exercitationem saepe nulla, dignissimos cum amet consectetur odit voluptas. Laudantium corrupti modi laboriosam aspernatur sed veniam alias hic distinctio, fuga ullam incidunt blanditiis magnam recusandae numquam neque amet enim ratione! Qui numquam obcaecati quas nobis neque. Magni, autem, optio. Esse iste aut quam corrupti qui laboriosam molestias quisquam totam, non consequuntur mollitia maiores itaque et amet nisi voluptatem voluptatum sed dolorem, expedita nam aliquam. Voluptatum quisquam odio ea ullam esse exercitationem ipsum recusandae. Numquam recusandae ex ullam at nobis soluta voluptatum! Ipsum et, ipsam explicabo! Aliquid beatae commodi sint voluptatibus laborum facilis vitae, quaerat, suscipit dolore deserunt alias totam eaque blanditiis rerum, dolor voluptates itaque perferendis repudiandae similique?</p>
    </main>
  </div>
</body>
</html>