<?php
class Precio_producto_proveedor
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

    //For pagging
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
            return $query->fetchAll(); 
            $this->$db = null;         
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_productos()
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

    public function get_proveedor()
    {
        try {
            $query = $this->db->prepare('select * from proveedor order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

     public function get_moneda()
    {
        try {
            $query = $this->db->prepare('select * from moneda order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }
    
    public function delete_precio_producto_proveedor($nr)
    {
        try {
            $query = $this->db->prepare('delete from precio_producto_proveedor where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Precio Producto/Proveedor Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='precio_producto_proveedor.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Precio, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Precio Producto/Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='precio_producto_proveedor.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_precio_producto_proveedor($nr_producto,$nr_proveedor,$nr_moneda,$precio)
    {
        try {
            $query = $this->db->prepare('insert into precio_producto_proveedor (nr_producto,nr_proveedor,nr_moneda,precio) values(?,?,?,?)');
            $query->bindParam(1, $nr_producto);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $nr_moneda);
            $query->bindParam(4, $precio);
            $query->execute();
            $resultado = $query->errorCode();
            //echo $resultado;  
            if($resultado == 00000)
            {
                echo "<script>alert ('Precio Agregado')</script>";
                echo "<script>location.href='precio_producto_proveedor.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Precio ya existe')</script>";
                echo "<script>location.href='precio_producto_proveedor.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Precio, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='precio_producto_proveedor.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Precio no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_precio_producto_proveedor($nr,$nr_producto,$nr_proveedor,$nr_moneda,$precio)
    {
        try {
            $query= $this->db->prepare('update precio_producto_proveedor SET nr_producto = ?, nr_proveedor = ?, nr_moneda = ?, precio = ? WHERE nr = ?');
            $query->bindParam(1, $nr_producto);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $nr_moneda);
            $query->bindParam(4, $precio);
            $query->bindParam(5, $nr);
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