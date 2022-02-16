<?php
class Orden_compra
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

    public function get_monedas()
    {
        try {
            $query = $this->db->prepare('select * from moneda order by nr');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_proveedor($nr_proveedor)
    {
        try {
            if (!empty($nr_proveedor))//If the parameter isn't empty return the desired row
            {
                $query = $this->db->prepare('select * from proveedor order by descripcion where nr = ?');
                $query->bindParam(1, $nr_proveedor);
                $query->execute();
                return $query->fetchAll();
                $this->$db = null; 
            }else{
                $query = $this->db->prepare('select * from proveedor order by descripcion');
                $query->execute();
                return $query->fetchAll();
                $this->$db = null;
            }            
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_condicion_compra_venta()
    {
        try {
            $query = $this->db->prepare('select * from condicion_compra_venta order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_detalle($nr_cabecera)
    {
        try {
            $query = $this->db->prepare('select * from detalle_orden_compra where nr= ?');
            $query->bindParam(1, $nr_cabecera);
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_producto()
    {
        try {
            $query = $this->db->prepare('select * from producto order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_sucursal()
    {
        try {
            $query = $this->db->prepare('select * from sucursal order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_deposito_stock($nr_sucursal)
    {
        try {
            $query = $this->db->prepare('select * from depositos_stock where nr_sucursal = ? order by nr');
            $query->bindParam(1, $nr_sucursal);
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_cabecera_orden_compra($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_orden_compra where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Orden de Compra Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='orden_compra.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Orden de Compra, ya esta facturada')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Orden de Compra, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_compra.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_orden_compra($nr,$nr_proveedor,$fecha_orden,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_entrega,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_orden_compra (nr,nr_proveedor,fecha_orden,nr_condicion,nr_sucursal,nr_deposito,nr_moneda,fecha_entrega,total_exentas,total_gravadas,total_iva,total_orden,nr_user,cotizacion_compra,cotizacion_venta,obs) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $fecha_orden);
            $query->bindParam(4, $nr_condicion);
            $query->bindParam(5, $nr_sucursal);
            $query->bindParam(6, $nr_deposito);
            $query->bindParam(7, $nr_moneda);
            $query->bindParam(8, $fecha_entrega);
            $query->bindParam(9, $total_exentas);
            $query->bindParam(10, $total_gravadas);
            $query->bindParam(11, $total_iva);
            $query->bindParam(12, $total_orden);
            $query->bindParam(13, $nr_user);
            $query->bindParam(14, $cotizacion_compra);
            $query->bindParam(15, $cotizacion_venta);
            $query->bindParam(16, $obs);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='orden_compra.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Compra')</script>";
                echo "<script>location.href='orden_compra_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Compra no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_orden_compra($nr,$nr_proveedor,$fecha_orden,$nr_condicion,$nr_sucursal,$nr_deposito,$nr_moneda,$fecha_entrega,$total_exentas,$total_gravadas,$total_iva,$total_orden,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query= $this->db->prepare('update cabecera_orden_compra SET nr_proveedor = ?, fecha_orden = ?, nr_condicion = ?, nr_sucursal = ?, nr_deposito = ?, nr_moneda = ?, fecha_entrega = ?, total_exentas = ?, total_gravadas = ?, total_iva = ?, total_orden = ?, nr_user = ?, cotizacion_compra = ?, cotizacion_venta = ?, obs = ? WHERE nr = ?');
            $query->bindParam(1, $nr_proveedor);
            $query->bindParam(2, $fecha_orden);
            $query->bindParam(3, $nr_condicion);
            $query->bindParam(4, $nr_sucursal);
            $query->bindParam(5, $nr_deposito);
            $query->bindParam(6, $nr_moneda);
            $query->bindParam(7, $fecha_entrega);
            $query->bindParam(8, $total_exentas);
            $query->bindParam(9, $total_gravadas);
            $query->bindParam(10, $total_iva);
            $query->bindParam(11, $total_orden);
            $query->bindParam(12, $nr_user);
            $query->bindParam(13, $cotizacion_compra);
            $query->bindParam(14, $cotizacion_venta);
            $query->bindParam(15, $obs);
            $query->bindParam(16, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_orden_compra($nr,$total_gravadas,$total_exentas,$total_iva,$total_orden)
    {
        try {
            $query= $this->db->prepare('update cabecera_orden_compra SET total_gravadas = ?, total_exentas = ?, total_iva = ?, total_orden = ? WHERE nr = ?');
            $query->bindParam(1, $total_gravadas);
            $query->bindParam(2, $total_exentas);
            $query->bindParam(3, $total_iva);
            $query->bindParam(4, $total_orden);
            $query->bindParam(5, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Orden de Compra Agregada')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Compra')</script>";
                echo "<script>location.href='orden_compra_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Compra no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function delete_orden_compra($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_orden_compra where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_orden_compra where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Orden de Compra Eliminada')".$resultado."</script>";
                        echo "<script>window.location.href='orden_compra.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar la Orden de Compra, ya esta facturada')</script>";
                    }else if ($resultado2 == 23514){
                        echo "<script>alert ('No se puede eliminar la Orden de Compra, algun producto queria con Cantidad a Recibir negativa')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar la Orden de Compra, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='orden_compra.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Orden de Compra, ya esta facturada')</script>";
                echo "<script>window.location.href='orden_compra.php'</script>";
            }else if ($resultado == 23514){
                echo "<script>alert ('No se puede eliminar la Orden de Compra, algun producto queria con Cantidad a Recibir negativa')</script>";
                echo "<script>window.location.href='orden_compra.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Orden de Compra, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_compra.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_orden_compra($nr,$nr_producto)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_orden_compra where nr = ? and nr_producto = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->execute();
            $resultado = $query->errorCode(); 
            //echo $resultado;
            if($resultado == 00000)
            {
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el producto, codigo de error: '.$resultado.'")</script>';
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_orden_compra($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad)
    {
        try {
            $query = $this->db->prepare('insert into detalle_orden_compra (nr,nr_producto,cantidad,descuento,precio_lista,precio_final,total_gravadas_linea,total_exentas_linea,total_iva_linea,total_linea,impuesto,nr_unidad) values(?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $cantidad);
            $query->bindParam(4, $descuento);
            $query->bindParam(5, $precio_lista);
            $query->bindParam(6, $precio_final);
            $query->bindParam(7, $total_gravadas_linea);
            $query->bindParam(8, $total_exentas_linea);
            $query->bindParam(9, $total_iva_linea);
            $query->bindParam(10, $total_linea);
            $query->bindParam(11, $impuesto);
            $query->bindParam(12, $nr_unidad);
            $query->execute();
            $resultado = $query->errorCode(); 
            return $resultado; 
            echo $resultado;
            //echo '<script>alert ("Codigo de error: '.$resultado.'")</script>';
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='orden_compra.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Compra')</script>";
                echo "<script>location.href='orden_compra_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Compra no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_detalle_orden_compra($nr,$nr_producto,$cantidad,$descuento,$precio_lista,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea,$impuesto,$nr_unidad)
    {
        try {
            $query= $this->db->prepare('update detalle_orden_compra SET cantidad = ?, descuento = ?, precio_lista = ?, precio_final = ?, total_gravadas_linea = ?, total_exentas_linea = ?, total_iva_linea = ?, total_linea = ?, impuesto = ?, nr_unidad = ? WHERE nr = ? AND nr_producto = ?');
            $query->bindParam(1, $cantidad);
            $query->bindParam(2, $nr_descuento);
            $query->bindParam(3, $precio_lista);
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