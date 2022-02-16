<?php
class Transferencia_stock
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

    public function get_detalle($nr_cabecera)
    {
        try {
            $query = $this->db->prepare('select * from detalle_transferencia_stock where nr= ?');
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

    public function delete_cabecera_transferencia_stock($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_transferencia_stock where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Transferencia Stock Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='transferencia_stock.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Transferencia Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Transferencia Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='transferencia_stock.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_transferencia_stock($nr,$fecha_transferencia,$nr_sucursal,$nr_deposito_origen,$nr_deposito_destino,$total_transferencia,$nr_user,$obs)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_transferencia_stock (nr,fecha_transferencia,nr_sucursal,nr_deposito_origen,nr_deposito_destino,total_transferencia,nr_user,obs) values(?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $fecha_transferencia);
            $query->bindParam(3, $nr_sucursal);
            $query->bindParam(4, $nr_deposito_origen);
            $query->bindParam(5, $nr_deposito_destino);
            $query->bindParam(6, $total_transferencia);
            $query->bindParam(7, $nr_user);
            $query->bindParam(8, $obs);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='transferencia_stock.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Transferencia Stock')</script>";
                echo "<script>location.href='transferencia_stock_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Transferencia Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_transferencia_stock($nr,$fecha_transferencia,$nr_sucursal,$nr_deposito_origen,$nr_deposito_destino,$total_transferencia,$nr_user,$obs)
    {
        try {
            $query= $this->db->prepare('update cabecera_transferencia_stock SET fecha_transferencia = ?, nr_sucursal = ?, nr_deposito_origen = ?, nr_deposito_destino = ?, total_transferencia = ?, nr_user = ?, obs = ? WHERE nr = ?');
            $query->bindParam(1, $fecha_transferencia);
            $query->bindParam(2, $nr_sucursal);
            $query->bindParam(3, $nr_deposito_origen);
            $query->bindParam(4, $nr_deposito_destino);
            $query->bindParam(5, $total_transferencia);
            $query->bindParam(6, $nr_user);
            $query->bindParam(7, $obs);
            $query->bindParam(8, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_transferencia_stock($nr,$total_transferencia)
    {
        try {
            $query= $this->db->prepare('update cabecera_transferencia_stock SET total_transferencia = ? WHERE nr = ?');
            $query->bindParam(1, $total_transferencia);
            $query->bindParam(2, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Transferencia Stock Agregada')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Transferencia Stock')</script>";
                echo "<script>location.href='transferencia_stock_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Transferencia Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function delete_transferencia_stock($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_transferencia_stock where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Cabecera
                try 
                {
                    $query = $this->db->prepare('delete from cabecera_transferencia_stock where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode(); 
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {
                        echo "<script>alert ('Transferencia Stock Eliminada')".$resultado."</script>";
                        echo "<script>window.location.href='transferencia_stock.php'</script>";
                    }else if ($resultado2 == 23503){
                        echo "<script>alert ('No se puede eliminar la Transferencia Stock')</script>";
                    }else{
                        echo '<script>alert ("Ocurrio un error al eliminar la Transferencia Stock, codigo de error: '.$resultado.'")</script>';
                        echo "<script>location.href='transferencia_stock.php'</script>";
                    }
                    return $query;
                    $this->$db = null;
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Transferencia Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Transferencia Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='transferencia_stock.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_transferencia_stock($nr,$nr_producto)
    {
        try 
        {
            $query = $this->db->prepare('delete from cabecera_transferencia_stock where nr = ? and nr_producto = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_producto);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Transferencia Stock Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='transferencia_stock.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar la Transferencia Stock')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Transferencia Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='transferencia_stock.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_transferencia_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query = $this->db->prepare('insert into detalle_transferencia_stock (nr,nr_producto,cantidad,costo,total_linea,nr_unidad) values(?,?,?,?,?,?)');
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
                //echo "<script>location.href='transferencia_stock.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el producto')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Transferencia Stock')</script>";
                echo "<script>location.href='transferencia_stock_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Transferencia Stock no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_transferencia_stock($nr,$nr_producto,$cantidad,$costo,$total_linea,$nr_unidad)
    {
        try {
            $query= $this->db->prepare('update detalle_transferencia_stock SET cantidad = ?, costo = ?, total_linea = ?, nr_unidad = ? WHERE nr = ? AND nr_producto = ?');
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