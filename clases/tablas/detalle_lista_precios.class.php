<?php
class Detalle_lista_precios
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

    public function get_detalle_lista_precios($query)
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

    public function get_lista_precios()
    {
        try {
            $query = $this->db->prepare('select * from lista_de_precios order by descripcion');
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
    
    public function delete_detalle_lista_precios($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_lista_precios where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Precio Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='detalle_lista_precios.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Precio, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Precio, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='detalle_lista_precios.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_detalle_lista_precios($nr_lista_precios,$nr_producto,$precio)
    {
        try {
            $query = $this->db->prepare('insert into detalle_lista_precios (nr_lista_precios,nr_producto,precio) values(?,?,?)');
            $query->bindParam(1, $nr_lista_precios);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $precio);
            $query->execute();
            $resultado = $query->errorCode();
            //echo $resultado;  
            if($resultado == 00000)
            {
                echo "<script>alert ('Precio Agregado')</script>";
                echo "<script>location.href='detalle_lista_precios.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='detalle_lista_precios.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Precio, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='detalle_lista_precios.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Precio no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_lista_precios($nr,$nr_lista_precios,$nr_producto,$precio)
    {
        try {
            $query= $this->db->prepare('update detalle_lista_precios SET nr_lista_precios = ?, nr_producto = ?, precio = ? WHERE nr = ?');
            $query->bindParam(1, $nr_lista_precios);
            $query->bindParam(2, $nr_producto);
            $query->bindParam(3, $precio);
            $query->bindParam(4, $nr);
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