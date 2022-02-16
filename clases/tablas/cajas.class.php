<?php
class Cajas
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

    public function get_cajas($query)
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
    
    public function delete_cajas($nr)
    {
        try {
            $query = $this->db->prepare('delete from cajas where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Caja Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='cajas.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Caja, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Caja, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cajas.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cajas($id_caja,$descripcion,$nr_sucursal)
    {
        try {
            $query = $this->db->prepare('insert into cajas (id_caja,descripcion,nr_sucursal) values(?,?,?)');
            $query->bindParam(1, $id_caja);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_sucursal);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Caja Agregada')</script>";
                echo "<script>location.href='cajas.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='cajas.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Caja, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cajas.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Caja no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cajas($nr,$id_caja,$descripcion,$nr_sucursal)
    {
        try {
            $query= $this->db->prepare('update cajas SET id_caja = ?, descripcion = ?, nr_sucursal = ? WHERE nr = ?');
            $query->bindParam(1, $id_caja);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_sucursal);
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