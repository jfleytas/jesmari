<?php
class Tipo_Personal
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

    public function get_tipo_personal($query)
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
 
    public function delete_tipo_personal($nr)
    {
        try {
            $query = $this->db->prepare('delete from tipo_personal where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Tipo Personal Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='tipo_personal.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Tipo de Personal, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Tipo Personal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='tipo_personal.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_tipo_personal($id_tipo_personal,$descripcion)
    {
        try {
            $query = $this->db->prepare('insert into tipo_personal (id_tipo_personal,descripcion) values(?,?)');
            $query->bindParam(1, $id_tipo_personal);
            $query->bindParam(2, $descripcion);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Tipo Personal Agregado')</script>";
                echo "<script>location.href='tipo_personal.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='tipo_personal.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Tipo Personal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='tipo_personal.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Tipo Personal no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_tipo_personal($nr,$id_tipo_personal,$descripcion)
    {
        try {
            $query= $this->db->prepare('update tipo_personal SET id_tipo_personal = ?, descripcion = ? WHERE nr = ?');
            $query->bindParam(1, $id_tipo_personal);
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