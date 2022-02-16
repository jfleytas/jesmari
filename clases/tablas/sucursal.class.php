<?php
class Sucursal
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

    public function get_sucursal($query)
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
    
    public function delete_sucursal($nr)
    {
        try {
            $query = $this->db->prepare('delete from sucursal where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Sucursal Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='sucursal.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Sucursal, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Sucursal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='sucursal.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_sucursal($id_sucursal,$descripcion,$direccion,$telefono)
    {
        try {
            $query = $this->db->prepare('insert into sucursal (id_sucursal,descripcion,direccion,telefono) values(?,?,?,?)');
            $query->bindParam(1, $id_sucursal);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $direccion);
            $query->bindParam(4, $telefono);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Sucursal Agregada')</script>";
                echo "<script>location.href='sucursal.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='sucursal.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Sucursal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='sucursal.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Sucursal no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_sucursal($nr,$id_sucursal,$descripcion,$direccion,$telefono)
    {
        try {
            $query= $this->db->prepare('update sucursal SET id_sucursal = ?, descripcion = ?, direccion = ?, telefono = ? WHERE nr = ?');
            $query->bindParam(1, $id_sucursal);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $direccion);
            $query->bindParam(4, $telefono);
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