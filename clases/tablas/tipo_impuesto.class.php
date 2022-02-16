<?php
class Tipo_Impuesto
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

    public function get_tipo_impuesto($query)
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
    
    public function delete_tipo_impuesto($nr)
    {
        try {
            $query = $this->db->prepare('delete from tipo_impuesto where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Impuesto Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='tipo_impuesto.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Impuesto, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Impuesto, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='tipo_impuesto.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_tipo_impuesto($id_impuesto,$descripcion,$valor)
    {
        try {
            $query = $this->db->prepare('insert into tipo_impuesto (id_impuesto,descripcion,valor) values(?,?,?)');
            $query->bindParam(1, $id_impuesto);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $valor);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Impuesto Agregado')</script>";
                echo "<script>location.href='tipo_impuesto.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='tipo_impuesto.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Impuesto, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='tipo_impuesto.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Impuesto no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_tipo_impuesto($nr,$id_impuesto,$descripcion,$valor)
    {
        try {
            $query= $this->db->prepare('update tipo_impuesto SET id_impuesto = ?, descripcion = ?, valor = ? WHERE nr = ?');
            $query->bindParam(1, $id_impuesto);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $valor);
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