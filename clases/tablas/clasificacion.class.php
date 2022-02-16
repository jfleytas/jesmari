<?php
class Clasificacion
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

    public function get_clasificacion($query)
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

    public function get_tipo_producto()
    {
        try {
            $query = $this->db->prepare('select * from tipo_producto order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }
    
    public function delete_clasificacion($nr)
    {
        try {
            $query = $this->db->prepare('delete from clasificacion where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Clasificacion Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='clasificacion.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Clasificacion, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Clasificacion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='clasificacion.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_clasificacion($id_clasificacion,$descripcion,$aplicado_a)
    {
        try {
            $query = $this->db->prepare('insert into clasificacion (id_clasificacion,descripcion,aplicado_a) values(?,?,?)');
            $query->bindParam(1, $id_clasificacion);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $aplicado_a);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Clasificacion Agregada')</script>";
                echo "<script>location.href='clasificacion.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='clasificacion.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Clasificacion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='clasificacion.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Clasificacion no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_clasificacion($nr,$id_clasificacion,$descripcion,$aplicado_a)
    {
        try {
            $query= $this->db->prepare('update clasificacion SET id_clasificacion = ?, descripcion = ?, aplicado_a = ? WHERE nr = ?');
            $query->bindParam(1, $id_clasificacion);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $aplicado_a);
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