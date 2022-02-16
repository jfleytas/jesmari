<?php
class Marca
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

    public function get_marca($query)
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
    
    public function delete_marca($nr)
    {
        try {
            $query = $this->db->prepare('delete from marca where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Marca Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='marca.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Marca, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Marca, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='marca.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_marca($id_marca,$descripcion)
    {
        try {
            $query = $this->db->prepare('insert into marca (id_marca,descripcion) values(?,?)');
            $query->bindParam(1, $id_marca);
            $query->bindParam(2, $descripcion);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Marca Agregada')</script>";
                echo "<script>location.href='marca.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='marca.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Marca, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='marca.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Marca no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_marca($nr,$id_marca,$descripcion)
    {
        try {
            $query= $this->db->prepare('update marca SET id_marca = ?, descripcion = ? WHERE nr = ?');
            $query->bindParam(1, $id_marca);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr);
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