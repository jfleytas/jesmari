<?php
class Medio_Pago
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

    public function get_medio_pago($query)
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

    public function get_monedas()
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
    
    public function delete_medio_pago($nr)
    {
        try {
            $query = $this->db->prepare('delete from medio_pago where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Medio de Pago Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='medio_pago.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Medio de Pago, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Medio de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='medio_pago.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_medio_pago($id_medio_pago,$descripcion,$nr_moneda)
    {
        try {
            $query = $this->db->prepare('insert into medio_pago (id_medio_pago,descripcion,nr_moneda) values(?,?,?)');
            $query->bindParam(1, $id_medio_pago);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_moneda);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Medio de Pago Agregado')</script>";
                echo "<script>location.href='medio_pago.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='medio_pago.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Medio de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='medio_pago.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Medio de Pago no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_medio_pago($nr,$id_medio_pago,$descripcion,$nr_moneda)
    {
        try {
            $query= $this->db->prepare('update medio_pago SET id_medio_pago = ?, descripcion = ?, nr_moneda = ? WHERE nr = ?');
            $query->bindParam(1, $id_medio_pago);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_moneda);
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