<?php
class Pedido_sucursal
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
            $query = $this->db->prepare('select * from detalle_pedido_sucursal where nr= ?');
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

    public function delete_cabecera_pedido_sucursal($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_pedido_sucursal where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Pedido Sucursal Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='pedido_sucursal.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Pedido Sucursal')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Pedido Sucursal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='pedido_sucursal.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_pedido_sucursal($nr,$fecha_pedido,$nr_sucursal_origen,$nr_deposito_origen,$nr_sucursal_destino,$nr_deposito_destino,$nr_moneda,$total_pedido,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_pedido_sucursal (nr,fecha_pedido,nr_sucursal_origen,nr_deposito_origen,nr_sucursal_destino,nr_deposito_destino,nr_moneda,total_pedido,nr_user,cotizacion_compra,cotizacion_venta,obs) values(?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $fecha_pedido);
            $query->bindParam(3, $nr_sucursal_origen);
            $query->bindParam(4, $nr_deposito_origen);
            $query->bindParam(5, $nr_sucursal_destino);
            $query->bindParam(6, $nr_deposito_destino);
            $query->bindParam(7, $nr_moneda);
            $query->bindParam(8, $total_pedido);
            $query->bindParam(9, $nr_user);
            $query->bindParam(10, $cotizacion_compra);
            $query->bindParam(11, $cotizacion_venta);
            $query->bindParam(12, $obs);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='pedido_sucursal.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Pedido Sucursal')</script>";
                echo "<script>location.href='pedido_sucursal_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Pedido Sucursal no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_pedido_sucursal($nr,$fecha_pedido,$nr_sucursal_origen,$nr_deposito_origen,$nr_sucursal_destino,$nr_deposito_destino,$nr_moneda,$total_pedido,$nr_user,$cotizacion_compra,$cotizacion_venta,$obs)
    {
        try {
            $query= $this->db->prepare('update cabecera_pedido_sucursal SET fecha_pedido = ?, nr_sucursal_origen = ?, nr_deposito_origen = ?, nr_sucursal_destino = ?, nr_deposito_destino = ?, nr_moneda = ?, total_pedido = ?, nr_user = ?, cotizacion_compra = ?, cotizacion_venta = ?, obs = ? WHERE nr = ?');
            $query->bindParam(1, $fecha_pedido);
            $query->bindParam(2, $nr_sucursal_origen);
            $query->bindParam(3, $nr_deposito_origen);
            $query->bindParam(4, $nr_sucursal_destino);
            $query->bindParam(5, $nr_deposito_destino);
            $query->bindParam(6, $nr_moneda);
            $query->bindParam(7, $total_pedido);
            $query->bindParam(8, $nr_user);
            $query->bindParam(9, $cotizacion_compra);
            $query->bindParam(10, $cotizacion_venta);
            $query->bindParam(11, $obs);
            $query->bindParam(12, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_pedido_sucursal($nr,$total_pedido)
    {
        try {
            $query= $this->db->prepare('update cabecera_pedido_sucursal SET total_pedido = ? WHERE nr = ?');
            $query->bindParam(1, $total_pedido);
            $query->bindParam(2, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Pedido Sucursal Agregado')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Pedido Sucursal')</script>";
                echo "<script>location.href='pedido_sucursal_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Pedido Sucursal no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function delete_pedido_sucursal($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_pedido_sucursal where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_pedido_sucursal where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Pedido Sucursal Eliminado')".$resultado."</script>";
                        echo "<script>window.location.href='pedido_sucursal.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar el Pedido Sucursal')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar el Pedido Sucursal, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='pedido_sucursal.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Pedido Sucursal, ya esta facturada')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Pedido Sucursal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='pedido_sucursal.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_pedido_sucursal($nr,$nr_producto)
    {
        try 
        {
            $query = $this->db->prepare('delete from cabecera_pedido_sucursal where nr = ? and nr_producto = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Pedido Sucursal Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='pedido_sucursal.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar el Pedido Sucursal')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Pedido Sucursal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='pedido_sucursal.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_pedido_sucursal($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query = $this->db->prepare('insert into detalle_pedido_sucursal (nr,nr_producto,cantidad,costo,total_linea,nr_unidad) values(?,?,?,?,?,?)');
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
                //echo "<script>location.href='pedido_sucursal.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Pedido Sucursal')</script>";
                echo "<script>location.href='pedido_sucursal_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Pedido Sucursal no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_pedido_sucursal($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query= $this->db->prepare('update detalle_pedido_sucursal SET cantidad = ?, costo = ?, total_linea = ?, nr_unidad = ? WHERE nr = ? AND nr_producto = ?');
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