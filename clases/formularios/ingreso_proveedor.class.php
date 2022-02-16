<?php
class Ingreso_proveedor
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

    public function delete_cabecera_ingreso_proveedor($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_ingreso_proveedor where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Ingreso Proveedor Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='ingreso_proveedor.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Ingreso Proveedor')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_ingreso_proveedor($nr,$nr_proveedor,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_exentas,$total_gravadas,$total_iva,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_compra,$obs)
    {        
        try {
            $query = $this->db->prepare('insert into cabecera_ingreso_proveedor (nr,nr_proveedor,fecha_ingreso,nr_sucursal,nr_deposito,nr_moneda,total_exentas,total_gravadas,total_iva,total_ingreso,nr_user,cotizacion_compra,cotizacion_venta,orden_compra,obs) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $fecha_ingreso);
            $query->bindParam(4, $nr_sucursal);
            $query->bindParam(5, $nr_deposito);
            $query->bindParam(6, $nr_moneda);
            $query->bindParam(7, $total_exentas);
            $query->bindParam(8, $total_gravadas);
            $query->bindParam(9, $total_iva);
            $query->bindParam(10, $total_ingreso);
            $query->bindParam(11, $nr_user);
            $query->bindParam(12, $cotizacion_compra);
            $query->bindParam(13, $cotizacion_venta);
            $query->bindParam(14, $orden_compra);
            $query->bindParam(15, $obs);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Ingreso Proveedor Agregado')</script>";
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. de Ingreso ya existe o la Orden ya fue ingresada')</script>";
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Ingreso Proveedor')</script>";
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Ingreso Proveedor no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_ingreso_proveedor($nr,$nr_proveedor,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_exentas,$total_gravadas,$total_iva,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$orden_compra,$obs)
    {
        try {
            $query= $this->db->prepare('update cabecera_ingreso_proveedor SET nr_proveedor = ?, fecha_ingreso = ?, nr_sucursal = ?, nr_deposito = ?, nr_moneda = ?, total_exentas = ?, total_gravadas = ?, total_iva = ?, total_ingreso = ?, nr_user = ?, cotizacion_compra = ?, cotizacion_venta = ?, orden_compra = ?, obs = ? WHERE nr = ?');
            $query->bindParam(1, $nr_proveedor);
            $query->bindParam(2, $fecha_ingreso);
            $query->bindParam(3, $nr_sucursal);
            $query->bindParam(4, $nr_deposito);
            $query->bindParam(5, $nr_moneda);
            $query->bindParam(6, $total_exentas);
            $query->bindParam(7, $total_gravadas);
            $query->bindParam(8, $total_iva);
            $query->bindParam(9, $total_ingreso);
            $query->bindParam(10, $nr_user);
            $query->bindParam(11, $cotizacion_compra);
            $query->bindParam(12, $cotizacion_venta);
            $query->bindParam(13, $orden_compra);
            $query->bindParam(14, $obs);
            $query->bindParam(15, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function delete_ingreso_proveedor($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_ingreso_proveedor where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_ingreso_proveedor where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Ingreso Proveedor Eliminado')".$resultado."</script>";
                        echo "<script>window.location.href='ingreso_proveedor.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar el Ingreso Proveedor')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Proveedor, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='ingreso_proveedor.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Ingreso Proveedor')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_detalle_ingreso_proveedor($nr,$nr_producto,$cantidad,$nr_unidad,$impuesto,$precio,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea)                        
    {
        try {
            $query = $this->db->prepare('insert into detalle_ingreso_proveedor (nr,nr_producto,cantidad,nr_unidad,impuesto,precio,precio_final,total_gravadas_linea,total_exentas_linea,total_iva_linea,total_linea) values(?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $cantidad);
            $query->bindParam(4, $nr_unidad);
            $query->bindParam(5, $impuesto);
            $query->bindParam(6, $precio);
            $query->bindParam(7, $precio_final);
            $query->bindParam(8, $total_gravadas_linea);
            $query->bindParam(9, $total_exentas_linea);
            $query->bindParam(10, $total_iva_linea);
            $query->bindParam(11, $total_linea);
            
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='ingreso_proveedor.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Ingreso Proveedor')</script>";
                echo "<script>location.href='ingreso_proveedor.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Ingreso Proveedor no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_ingreso_proveedor($nr,$nr_producto,$cantidad,$nr_unidad,$impuesto,$precio,$precio_final,$total_gravadas_linea,$total_exentas_linea,$total_iva_linea,$total_linea)
    {
        try {
            $query= $this->db->prepare('update detalle_ingreso_proveedor SET cantidad = ?, nr_unidad = ?, impuesto = ?, precio = ?, precio_final = ?, total_gravadas_linea = ?, total_exentas_linea = ?, total_iva_linea = ?, total_linea = ? WHERE nr = ? AND nr_producto = ?');
            $query->bindParam(1, $cantidad);
            $query->bindParam(2, $precio);
            $query->bindParam(3, $nr_unidad);
            $query->bindParam(4, $impuesto);
            $query->bindParam(5, $precio_final);
            $query->bindParam(6, $total_gravadas_linea);
            $query->bindParam(7, $total_exentas_linea);
            $query->bindParam(8, $total_iva_linea);
            $query->bindParam(9, $total_linea);
            $query->bindParam(10, $nr);
            $query->bindParam(11, $nr_producto);
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