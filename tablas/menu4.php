<?php
  require '../login/control_login.php'; /*Check if the user is logged into the system*/
  require '../css/cabecera_empresa.html'; /*Show the Company name in the header*/
  require '../css/nombre_empresa.html'; /*Show the Company name*/

  date_default_timezone_set('America/Buenos_Aires');
?>

<!doctype html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>document.getElementsByTagName("html")[0].className += " js";</script>
  <link rel="stylesheet" href="../css/style_menu.css">
</head>
<body style="padding: 3em 0">
<section>

  <ul class="cd-accordion cd-accordion--animated margin-top-lg margin-bottom-lg">
    <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-1" id="group-1">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-1"><span>Tablas</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-1" id="sub-group-1">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-1"><span>Compras</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/proveedor.php"><span>Proveedores</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/precio_producto_proveedor.php"><span>Precio Producto/Proveedor</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2" id="sub-group-2">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2"><span>Finanzas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/banco.php"><span>Bancos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/cajas.php"><span>Cajas</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/condicion_compra_venta.php"><span>Condicion de Compra y Venta</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/cotizacion.php"><span>Cotizacion</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/cuentas_bancarias.php"><span>Cuentas Bancarias</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/tipo_impuesto.php"><span>Impuestos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/medio_pago.php"><span>Medio de Pago</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/moneda.php"><span>Monedas</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3" id="sub-group-3">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3"><span>RRHH</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/personal.php"><span>Personal</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/tipo_personal.php"><span>Tipo Personal</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-4" id="sub-group-4">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-4"><span>Stock</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/clasificacion.php"><span>Clasificacion</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/depositos_stock.php"><span>Depositos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/marca.php"><span>Marca</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/productos.php"><span>Productos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/unidad_medida.php"><span>Unidad de Medida</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-5" id="sub-group-5">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-5"><span>Ventas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/clientes.php"><span>Clientes</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/descuentos.php"><span>Descuentos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/grupo_clientes.php"><span>Grupo de Clientes</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/lista_precios.php"><span>Lista de Precios</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/limite_credito.php"><span>Limite de Credito</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/detalle_lista_precios.php"><span>Precios</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-6" id="sub-group-6">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-6"><span>Varios</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/configuracion.php"><span>Configuracion</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/pais.php"><span>Pais</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/sucursal.php"><span>Sucursal</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../tablas/users.php"><span>Usuarios</span></a></li>
          </ul>
        </li>
      <!-- Example of multilevel  
        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2" id="sub-group-2">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2"><span>Sub Group 2</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item cd-accordion__item--has-children">
              <input class="cd-accordion__input" type="checkbox" name ="sub-group-level-3" id="sub-group-level-3">
              <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-level-3"><span>Sub Group Level 3</span></label>

              <ul class="cd-accordion__sub cd-accordion__sub--l3">
                <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
                <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
              </ul>
            </li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
          </ul>
        </li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>-->
      </ul>
    </li>

    <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-2" id="group-2">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-2"><span>Formularios</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2-1" id="sub-group-2-1">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2-1"><span>Compras</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_compra.php"><span>Orden de Compra</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/factura_compra.php"><span>Factura Compra</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/ingreso_proveedor.php"><span>Ingreso Proveedor</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_pago.php"><span>Pagos</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2-2" id="sub-group-2-2">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2-2"><span>Finanzas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_pago.php"><span>Depositos Bancarios</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_pago.php"><span>Extraccion Bancaria</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_pago.php"><span>Transferencia Bancaria</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2-3" id="sub-group-2-3">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2-3"><span>Stock</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/pedido_sucursal.php"><span>Pedido Sucursal</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/ingreso_stock.php"><span>Ingreso de Stock</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/egreso_stock.php"><span>Egreso de Stock</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/transferencia_stock.php"><span>Transferencia de Stock</span></a></li> 
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-2-4" id="sub-group-2-4">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2-4"><span>Ventas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/orden_venta.php"><span>Orden de Venta</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/factura_venta.php"><span>Factura Venta</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/nota_credito_venta.php"><span>Nota de Credito</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../formularios/cobranza.php"><span>Recibos</span></a></li> 
          </ul>
        </li>
      </ul>
    </li>

  <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-3" id="group-3">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-3"><span>Reportes</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3-1" id="sub-group-3-1">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3-1"><span>Compras</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/lista_productos2.php"><span>Lista de Productos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_factura_compra_p.php"><span>Lista de Facturas Compras</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3-2" id="sub-group-3-2">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3-2"><span>Finanzas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/cuenta_cobrar_p.php"><span>Cuentas a Cobrar</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/cuenta_pagar_p.php"><span>Cuentas a Pagar</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/estado_cliente_factura_p.php"><span>Estado de Cliente por Factura</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_cobranzas_p.php"><span>Listado de Cobranzas</span></a></li>
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3-3" id="sub-group-3-3">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3-3"><span>Stock</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/historico_producto_p.php"><span>Historico por Producto</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/lista_productos.php"><span>Lista de Productos</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_stock_deposito_sucursal_p.php"><span>Lista de Stock</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_stock_valorizado.php"><span>Lista de Stock Valorizado</span></a></li> 
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/rotacion_stock_p.php"><span>Rotacion de Stock</span></a></li> 
          </ul>
        </li>

        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3-4" id="sub-group-3-4">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3-4"><span>Ventas</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/lista_clientes_p.php"><span>Lista de Clientes</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_factura_venta_p.php"><span>Lista de Facturas Ventas</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/listado_venta_producto_p.php"><span>Lista de Venta por Producto</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/oventa_pendiente_factura_p.php"><span>Orden de Venta pendiente de Facturacion</span></a></li> 
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/venta_mensual_sucursal.php"><span>Ventas por Mes por Sucursal</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/ranking_ventas_cliente_p.php"><span>Ranking de Ventas por Cliente</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/ranking_ventas_producto_p.php"><span>Ranking de Ventas por Producto</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/ranking_ventas_sucursal_p.php"><span>Ranking de Ventas por Sucursal</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/ranking_ventas_vendedor_p.php"><span>Ranking de Ventas por Vendedor</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="../informes/utilidad_por_venta.php"><span>Utilidad por Factura Venta</span></a></li>
          </ul>
        </li>
      </ul>
    </li>

  <!-- Example of other levels
    <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-2" id="group-2">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-2"><span>Group 2</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
      </ul>
    </li>

    <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-3" id="group-3">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-3"><span>Group 3</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
      </ul>
    </li>

    <li class="cd-accordion__item cd-accordion__item--has-children">
      <input class="cd-accordion__input" type="checkbox" name ="group-4" id="group-4">
      <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-4"><span>Group 4</span></label>

      <ul class="cd-accordion__sub cd-accordion__sub--l1">
        <li class="cd-accordion__item cd-accordion__item--has-children">
          <input class="cd-accordion__input" type="checkbox" name ="sub-group-3" id="sub-group-3">
          <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-3"><span>Sub Group 3</span></label>

          <ul class="cd-accordion__sub cd-accordion__sub--l2">
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
            <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
          </ul>
        </li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
        <li class="cd-accordion__item"><a class="cd-accordion__label cd-accordion__label--icon-img" href="#0"><span>Image</span></a></li>
      </ul>
    </li>-->
  </ul>
</section>
<script src="../js/util.js"></script> <!-- util functions included in the CodyHouse framework -->
<script src="../js/main.js"></script>
</body>
</html>