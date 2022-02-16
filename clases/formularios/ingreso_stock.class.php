<?php
class Ingreso_stock
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

    public function get_detalle($nr_cabecera)
    {
        try {
            $query = $this->db->prepare('select * from detalle_ingreso_stock where nr= ?');
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
            $query = $this->db->prepare('select * from productos order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_cabecera_ingreso_stock($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_ingreso_stock where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Ingreso Stock Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='ingreso_stock.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Ingreso Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='ingreso_stock.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_ingreso_stock($nr,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_ingreso_stock (nr,fecha_ingreso,nr_sucursal,nr_deposito,nr_moneda,total_ingreso,nr_user,cotizacion_compra,cotizacion_venta,obs) values(?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $fecha_ingreso);
            $query->bindParam(3, $nr_sucursal);
            $query->bindParam(4, $nr_deposito);
            $query->bindParam(5, $nr_moneda);
            $query->bindParam(6, $total_ingreso);
            $query->bindParam(7, $nr_user);
            $query->bindParam(8, $cotizacion_compra);
            $query->bindParam(9, $cotizacion_venta);
            $query->bindParam(10, $obs);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='ingreso_stock.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Ingreso Stock')</script>";
                echo "<script>location.href='ingreso_stock_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Ingreso Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_ingreso_stock($nr,$fecha_ingreso,$nr_sucursal,$nr_deposito,$nr_moneda,$total_ingreso,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query= $this->db->prepare('update cabecera_ingreso_stock SET fecha_ingreso = ?, nr_sucursal = ?, nr_deposito = ?, nr_moneda = ?, total_ingreso = ?, nr_user = ?, cotizacion_compra = ?, cotizacion_venta = ?, obs = ? WHERE nr = ?');
            $query->bindParam(1, $fecha_ingreso);
            $query->bindParam(2, $nr_sucursal);
            $query->bindParam(3, $nr_deposito);
            $query->bindParam(4, $nr_moneda);
            $query->bindParam(5, $total_ingreso);
            $query->bindParam(6, $nr_user);
            $query->bindParam(7, $cotizacion_compra);
            $query->bindParam(8, $cotizacion_venta);
            $query->bindParam(9, $obs);
            $query->bindParam(10, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_ingreso_stock($nr,$total_exentas,$total_gravadas,$total_iva,$total_ingreso)
    {
        try {
            $query= $this->db->prepare('update cabecera_ingreso_stock SET total_ingreso = ? WHERE nr = ?');
            $query->bindParam(1, $total_ingreso);
            $query->bindParam(2, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Ingreso Stock Agregado')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Ingreso Stock')</script>";
                echo "<script>location.href='ingreso_stock_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Ingreso Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function delete_ingreso_stock($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_ingreso_stock where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_ingreso_stock where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Ingreso Stock EliminadO')".$resultado."</script>";
                        echo "<script>window.location.href='ingreso_stock.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar el Ingreso Stock')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Stock, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='ingreso_stock.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Ingreso Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='ingreso_stock.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_ingreso_stock($nr,$nr_producto)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_ingreso_stock where nr = ? and nr_producto = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Ingreso Stock Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='ingreso_stock.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar el Ingreso Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Ingreso Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='ingreso_stock.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_ingreso_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query = $this->db->prepare('insert into detalle_ingreso_stock (nr,nr_producto,cantidad,costo,total_linea,nr_unidad) values(?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $cantidad);
            $query->bindParam(4, $costo);
            $query->bindParam(5, $total_linea);
            $query->bindParam(6, $nr_unidad);
            $query->execute();
            $resultado = $query->errorCode(); 
            return $resultado; 
            echo $resultado;
            //echo '<script>alert ("Codigo de error: '.$resultado.'")</script>';
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='ingreso_stock.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Ingreso Stock')</script>";
                echo "<script>location.href='ingreso_stock_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Ingreso Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_ingreso_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query= $this->db->prepare('update detalle_ingreso_stock SET cantidad = ?, costo = ?, total_linea = ?, nr_unidad = ? WHERE nr = ? AND nr_producto = ?');
            $query->bindParam(1, $cantidad);
            $query->bindParam(2, $costo);
            $query->bindParam(3, $total_linea);
            $query->bindParam(4, $nr_unidad);
            $query->bindParam(5, $nr);
            $query->bindParam(6, $nr_producto);
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