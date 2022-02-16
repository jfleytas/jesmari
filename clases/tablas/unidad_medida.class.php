<?php
class Unidad_Medida
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

    public function get_unidad_medida($query)
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

    public function delete_unidad_medida($nr)
    {
        try {
            $query = $this->db->prepare('delete from unidad_medida where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Unida de Medida Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='unidad_medida.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Unidad de Medida, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Unidad de Medida, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='unidad_medida.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_unidad_medida($id_unidad_medida,$descripcion)
    {
        try {
            $query = $this->db->prepare('insert into unidad_medida (id_unidad_medida,descripcion) values(?,?)');
            $query->bindParam(1, $id_unidad_medida);
            $query->bindParam(2, $descripcion);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Unidad de Medida Agregada')</script>";
                echo "<script>location.href='unidad_medida.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='unidad_medida.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Unidad de Medida, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='unidad_medida.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Unidad de Medida no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_unidad_medida($nr,$id_unidad_medida,$descripcion)
    {
        try {
            $query= $this->db->prepare('update unidad_medida SET id_unidad_medida = ?, descripcion = ? WHERE nr = ?');
            $query->bindParam(1, $id_unidad_medida);
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