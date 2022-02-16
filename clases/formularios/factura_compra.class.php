<?php
class Factura_compra
{
    private static $instancia;
    private $db;

    private function __construct()
    {
        include ("../conexion/Conexion.php");
        $this->db = Conexion::conexion();
        //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function singleton()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase;
        }
        return self::$instancia;
    }

    //Count the ammount of register returned for a query
    public function rowCount($query)
    {
        $query = $this->db->prepare($query);
        $query->execute();
        $cantidad_registros = $query->rowCount();
        return $cantidad_registros;
        echo $cantidad_registros;
        $this->$db = null; 
    }

    public function query($query)
    {
        try {
            $query = $this->db->prepare($query);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            //echo $query;
            /*if($resultado <> 00000)
            {
                echo "<script>alert ('El codigo no existe')</script>";
            }*/
            return $query; 
            $this->db = null; 
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_cabecera_factura_compra($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_factura_compra where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Factura de Compra Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='factura_compra.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Factura de Compra')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Factura de Compra, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='factura_compra.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_factura_compra($nr,$nr_factura,$nr_proveedor,$fecha_factura,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_vto,$total_exentas,$total_gravadas,$total_iva,$total_factura,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_compra,$obs,$estado,$saldo_factura)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_factura_compra (nr,nr_factura,nr_proveedor,fecha_factura,nr_condicion,nr_sucursal,nr_deposito,nr_moneda,fecha_vto,total_exentas,total_gravadas,total_iva,total_factura,nr_user,cotizacion_compra,cotizacion_venta,orden_compra,obs,estado,saldo_factura) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_factura);
            $query->bindParam(3, $nr_proveedor);
            $query->bindParam(4, $fecha_factura);
            $query->bindParam(5, $nr_condicion);
            $query->bindParam(6, $nr_sucursal);
            $query->bindParam(7, $nr_deposito);
            $query->bindParam(8, $nr_moneda);
            $query->bindParam(9, $fecha_vto);
            $query->bindParam(10, $total_exentas);
            $query->bindParam(11, $total_gravadas);
            $query->bindParam(12, $total_iva);
            $query->bindParam(13, $total_factura);
            $query->bindParam(14, $nr_user);
            $query->bindParam(15, $cotizacion_compra);
            $query->bindParam(16, $cotizacion_venta);
            $query->bindParam(17, $orden_compra);
            $query->bindParam(18, $obs);
            $query->bindParam(19, $estado);
            $query->bindParam(20, $saldo_factura);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Factura Compra Agregada')</script>";
                echo "<script>location.href='factura_compra.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. de Factura ya existe o la Orden ya fue facturada')</script>";
                echo "<script>location.href='factura_compra.php'</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Factura de Compra')</script>";
                echo "<script>location.href='factura_compra.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Factura de Compra no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_factura_compra($nr,$nr_factura,$nr_proveedor,$fecha_factura,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_vto,$total_exentas,$total_gravadas,$total_iva,$total_factura,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_compra,$saldo_factura,$obs,$estado)
    {
        try {
            $query= $this->db->prepare('update cabecera_factura_compra SET nr_factura = ?, nr_proveedor = ?, fecha_factura = ?, nr_condicion = ?, nr_sucursal = ?, nr_deposito = ?, nr_moneda = ?, fecha_vto = ?, total_exentas = ?, total_gravadas = ?, total_iva = ?, total_factura = ?, nr_user = ?, cotizacion_compra = ?, cotizacion_venta = ?, orden_compra = ?, saldo_factura = ?, obs = ?, estado = ? WHERE nr = ?');
            $query->bindParam(1, $nr_factura);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $fecha_factura);
            $query->bindParam(4, $nr_condicion);
            $query->bindParam(5, $nr_sucursal);
            $query->bindParam(6, $nr_deposito);
            $query->bindParam(7, $nr_moneda);
            $query->bindParam(8, $fecha_vto);
            $query->bindParam(9, $total_exentas);
            $query->bindParam(10, $total_gravadas);
            $query->bindParam(11, $total_iva);
            $query->bindParam(12, $total_factura);
            $query->bindParam(13, $nr_user);
            $query->bindParam(14, $cotizacion_compra);
            $query->bindParam(15, $cotizacion_venta);
            $query->bindParam(16, $orden_compra);
            $query->bindParam(17, $saldo_factura);
            $query->bindParam(18, $obs);
            $query->bindParam(19, $estado);
            $query->bindParam(20, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function delete_factura_compra($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_factura_compra where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_factura_compra where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Factura de Compra Eliminada')".$resultado."</script>";
                        echo "<script>window.location.href='factura_compra.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar la Factura de Compra, ya esta facturada')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar la Factura de Compra, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='factura_compra.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Factura de Compra, ya esta facturada')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Factura de Compra, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='factura_compra.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_detalle_factura_compra($nr,$nr_producto,$cantidad,$precio_lista,$descuento,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad)                        
    {
        try {
            $query = $this->db->prepare('insert into detalle_factura_compra (nr,nr_producto,cantidad,precio_lista,descuento,precio_final,total_gravadas_linea,total_exentas_linea,total_iva_linea,total_linea,impuesto,nr_unidad) values(?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $cantidad);
            $query->bindParam(4, $precio_lista);
            $query->bindParam(5, $descuento);
            $query->bindParam(6, $precio_final);
            $query->bindParam(7, $total_gravadas_linea);
            $query->bindParam(8, $total_exentas_linea);
            $query->bindParam(9, $total_iva_linea);
            $query->bindParam(10, $total_linea);
            $query->bindParam(11, $impuesto);
            $query->bindParam(12, $nr_unidad);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='factura_compra.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la factura de Compra')</script>";
                echo "<script>location.href='factura_compra.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La factura de Compra no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_detalle_factura_compra($nr,$nr_producto,$cantidad,$nr_descuento,$precio,$total_gravadas,$total_exentas,$total_linea,$impuesto,$nr_unidad)
    {
        try {
            $query= $this->db->prepare('update detalle_factura_compra SET cantidad = ?, precio_lista = ?, descuento = ?, precio_final = ?, total_gravadas_linea = ?, total_exentas_linea = ?, total_iva_linea = ?, total_linea = ?, impuesto = ?, nr_unidad = ? WHERE nr = ? AND nr_producto = ?');
            $query->bindParam(1, $cantidad);
            $query->bindParam(2, $precio_lista);
            $query->bindParam(3, $descuento);
            $query->bindParam(4, $precio_final);
            $query->bindParam(5, $total_gravadas_linea);
            $query->bindParam(6, $total_exentas_linea);
            $query->bindParam(7, $total_iva_linea);
            $query->bindParam(8, $total_linea);
            $query->bindParam(9, $impuesto);
            $query->bindParam(10, $nr_unidad);
            $query->bindParam(11, $nr);
            $query->bindParam(12, $nr_producto);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function __clone()
    {
        trigger_error('La clonación no es permitida!.', E_USER_ERROR);
    }
}
?>