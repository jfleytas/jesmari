<?php
  require '../login/control_login.php'; /*Check if the user is logged into the system*/
  require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
  require '../css/nombre_empresa.html'; /*Show the Company name*/

  date_default_timezone_set('America/Buenos_Aires');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
   <link rel="stylesheet" type="text/css" href="../css/estilo_menu_vertical.css" />
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>

<div id="wrapper">
    <ul class="menu">
      <li class="item1"><a href="#">Tablas <span>1</span></a>
         <ul class="submenu">
           <li class="item1-1"><a href="#">Compras <span>1.1</span></a>
               <ul>
                  <li><a href="../tablas/proveedor.php">Proveedores</a></li>    
                  <li><a href="../tablas/precio_producto_proveedor.php">Precio Producto/Proveedor</a></li> 
               </ul>
           </li>
           <li class="item1-2"><a href="#">Finanzas <span>1.2</span></a>
               <ul>
                  <li><a href="../tablas/banco.php">Bancos</a></li>
                  <li><a href="../tablas/cajas.php">Cajas</a></li>
                  <li><a href="../tablas/condicion_compra_venta.php">Condicion de Compra y Venta</a></li>
                  <li><a href="../tablas/cotizacion.php">Cotizacion</a></li>
                  <li><a href="../tablas/cuentas_bancarias.php">Cuentas Bancarias</a></li>
                  <li><a href="../tablas/tipo_impuesto.php">Impuestos</a></li>
                  <li><a href="../tablas/medio_pago.php">Medio de Pago</a></li>
                  <li><a href="../tablas/moneda.php">Monedas</a></li>
               </ul>
           </li>
           <li class="item1-3"><a href="#">RRHH <span>1.3</span></a>
               <ul>
                  <li><a href="../tablas/personal.php">Personal</a></li>
                  <li><a href="../tablas/tipo_personal.php">Tipo Personal</a></li>
               </ul>
           </li>
           <li class="item1-4"><a href="#">Stock <span>1.4</span></a>
               <ul>
                  <li><a href="../tablas/clasificacion.php">Clasificacion</a></li>
                  <li><a href="../tablas/depositos_stock.php">Depositos</a></li>
                  <li><a href="../tablas/marca.php">Marca</a></li>
                  <li><a href="../tablas/productos.php">Productos</a></li>
                  <li><a href="../tablas/unidad_medida.php">Unidad de Medida</a></li>
               </ul>
           </li>
           <li class="item1-5"><a href="#">Ventas <span>1.5</span></a>
               <ul>
                  <li><a href="../tablas/clientes.php">Clientes</a></li>
                  <li><a href="../tablas/descuentos.php">Descuentos</a></li>
                  <li><a href="../tablas/grupo_clientes.php">Grupo de Clientes</a></li>
                  <li><a href="../tablas/lista_precios.php">Lista de Precios</a></li>
                  <li><a href="../tablas/limite_credito.php">Limite de Credito</a></li>
                  <li><a href="../tablas/detalle_lista_precios.php">Precios</a></li>
               </ul>
           </li>
           <li class="item1-6"><a href="#">Varios <span>1.6</span></a>
               <ul>
                  <li><a href="../tablas/configuracion.php">Configuracion</a></li>
                  <li><a href="../tablas/pais.php">Pais</a></li>
                  <li><a href="../tablas/sucursal.php">Sucursal</a></li>
                  <li><a href="../tablas/users.php">Usuarios</a></li>
               </ul>
           </li>
         </ul>
      </li>

      <li class="item2"><a href="#">Formularios <span>2</span></a>
         <ul class="submenu">
           <li class="item1-1"><a href="#">Compras <span>2.1</span></a>
               <ul>
                  <li><a href="../formularios/orden_compra.php">Orden de Compra</a></li>
                  <li><a href="../formularios/factura_compra.php">Factura Compra</a></li>
                  <li><a href="../formularios/ingreso_proveedor.php">Ingreso Proveedor</a></li>
                  <li><a href="../formularios/orden_pago.php">Pagos</a></li>
               </ul>
           </li>
           <li class="item1-2"><a href="#">Finanzas <span>2.2</span></a>
               <ul>
                  <li><a href="../formularios/orden_pago.php">Depositos Bancarios</a></li>
                  <li><a href="../formularios/orden_pago.php">Extraccion Bancaria</a></li>
                  <li><a href="../formularios/orden_pago.php">Transferencia Bancaria</a></li>
                  <!--<li><a href="../formularios/ingreso_stock.php">Ingreso de Stock</a></li>
                  <li><a href="../formularios/egreso_stock.php">Egreso de Stock</a></li>
                  <li><a href="../formularios/transferencia_stock.php">Transferencia de Stock</a></li>-->
               </ul>
           </li>
           <li class="item1-4"><a href="#">Stock <span>2.3</span></a>
               <ul>
                  <li><a href="../formularios/pedido_sucursal.php">Pedido Sucursal</a></li>
                  <li><a href="../formularios/ingreso_stock.php">Ingreso de Stock</a></li>
                  <li><a href="../formularios/egreso_stock.php">Egreso de Stock</a></li>
                  <li><a href="../formularios/transferencia_stock.php">Transferencia de Stock</a></li>
               </ul>
           </li>
           <li class="item1-5"><a href="#">Ventas <span>2.4</span></a>
               <ul>
                  <li><a href="../formularios/orden_venta.php">Orden de Venta</a></li>
                  <li><a href="../formularios/factura_venta.php">Factura Venta</a></li>
                  <!--<li><a href="../formularios/nota_credito.php">Nota de Credito</a></li>-->
                  <li><a href="../formularios/cobranza.php">Recibos</a></li>
               </ul>
           </li>
         </ul>
      </li>

      <li class="item3"><a href="#">Reportes <span>3</span></a>
         <ul class="submenu">
           <li class="item1-1"><a href="#">Compras <span>3.1</span></a>
               <ul>
                  <li><a href="../informes/lista_productos2.php">Lista de Productos</a></li>
               </ul>
           </li>
           <li class="item1-2"><a href="#">Finanzas <span>3.2</span></a>
               <ul>
                  <li><a href="../informes/cuenta_cobrar.php">Cuentas a Cobrar</a></li>
                  <li><a href="../informes/cuenta_pagar.php">Cuentas a Pagar</a></li>
               </ul>
           </li>
           <li class="item1-4"><a href="#">Stock <span>3.3</span></a>
               <ul>
                  <li><a href="../informes/lista_productos2.php">Lista de Productos</a></li>
                  <li><a href="../informes/listado_stock_deposito_sucursal.php">Lista de Stock</a></li>
                  <li><a href="../informes/listado_stock_valorizado.php">Lista de Stock Valorizado</a></li>
               </ul>
           </li>
           <li class="item1-5"><a href="#">Ventas <span>3.4</span></a>
               <ul>
                  <li><a href="../informes/listado_factura_venta_p.php">Lista de Facturas Ventas</a></li>
                  <li><a href="../informes/oventa_pendiente_factura.php">Orden de Venta pendiente de Facturacion</a></li>
                  <li><a href="../informes/venta_mensual_sucursal.php">Ventas por Mes por Sucursal</a></li>
                  <li><a href="../informes/ranking_ventas_cliente.php">Ranking de Ventas por Cliente</a></li>
                  <li><a href="../informes/ranking_ventas_sucursal.php">Ranking de Ventas por Sucursal</a></li>
                  <li><a href="../informes/venta_vendedor.php">Ranking de Ventas por Vendedor</a></li>
                  <li><a href="../informes/utilidad_por_venta.php">Utilidad por Factura Venta</a></li>
               </ul>
           </li>
         </ul>
      </li>
	  
	 <li class="item4"><a href="#">Cuenta <span>4</span></a>
         <ul class="submenu">
           <li class="item1-1"><a href="../login/logout.php">Salir</a>
           </li>
         </ul>
     </li>

    </ul>
</div>
<div class="page-content">Holi holi holi</div>

<script type="text/javascript">
   $(function() {

      var menu_ul = $('.menu > li > ul'), menu_a  = $('.menu > li > a'), 
      submenu_ul = $('.submenu > li > ul'), submenu_a  = $('.submenu > li > a');
       
      menu_ul.hide();
      submenu_ul.hide();

      menu_a.click(function(e) {
         e.preventDefault();
         if(!$(this).hasClass('active')) {
            menu_a.removeClass('active');
            menu_ul.filter(':visible').slideUp('normal');
            $(this).addClass('active').next().stop(true,true).slideDown('normal');
         } else {
            $(this).removeClass('active');
            $(this).next().stop(true,true).slideUp('normal');
         }
      });

      submenu_a.click(function(e) {
         e.preventDefault();
         if(!$(this).hasClass('active')) {
            submenu_a.removeClass('active');
            submenu_ul.filter(':visible').slideUp('normal');
            $(this).addClass('active').next().stop(true,true).slideDown('normal');
         } else {
            $(this).removeClass('active');
            $(this).next().stop(true,true).slideUp('normal');
         }
      });

/* Commented because it does not allow the link to work
$(".menu li ul li ul li a").click(function(e){
        e.preventDefault(); //To prevent the default anchor tag behaviour
        var url = this.href;
        $(".page-content").load(url);
    });*/


   });
</script>
  
</body>
</html>