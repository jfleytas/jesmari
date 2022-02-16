<?php
class Depositos_stock
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

    public function get_depositos_stock($query)
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

    public function delete_depositos_stock($nr)
    {
        try {
            $query = $this->db->prepare('delete from depositos_stock where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Deposito de Stock Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='depositos_stock.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Deposito de Stock, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Deposito de Stock, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='depositos_stock.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_depositos_stock($id_deposito,$descripcion,$nr_sucursal)
    {
        try {
            $query = $this->db->prepare('insert into depositos_stock (id_deposito,descripcion,nr_sucursal) values(?,?,?)');
            $query->bindParam(1, $id_deposito);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_sucursal);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Deposito Agregado')</script>";
                echo "<script>location.href='depositos_stock.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='depositos_stock.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Deposito, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='depositos_stock.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Deposito no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_depositos_stock($nr,$id_deposito,$descripcion,$nr_sucursal)
    {
        try {
            $query= $this->db->prepare('update depositos_stock SET id_deposito = ?, descripcion = ?, nr_sucursal = ? WHERE nr = ?');
            $query->bindParam(1, $id_deposito);
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