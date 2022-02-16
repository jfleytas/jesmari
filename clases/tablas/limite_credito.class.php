<?php
class Limite_Credito
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

    public function get_moneda()
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

    public function delete_limite_credito($nr)
    {
        try {
            $query = $this->db->prepare('delete from cuenta_cliente where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Limite de Credito Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='limite_credito.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Limite de Credito, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Limite de Credito, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='limite_credito.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_limite_credito($nr_cliente,$nr_moneda,$limite_credito,$credito_disponible)
    {
        try {
            $query = $this->db->prepare('insert into cuenta_cliente (nr_cliente,nr_moneda,limite_credito,credito_disponible) values(?,?,?,?)');
            $query->bindParam(1, $nr_cliente);
            $query->bindParam(2, $nr_moneda);
            $query->bindParam(3, $limite_credito);
            $query->bindParam(4, $credito_disponible);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;

            if($resultado == 00000)
            {
                echo "<script>alert ('Limite de Credito Agregado')</script>";
                echo "<script>location.href='limite_credito.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo de Cliente ya existe')</script>";
                echo "<script>location.href='limite_credito.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Limite de Credito, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='limite_credito.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Limite de Credito no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_limite_credito($nr,$nr_cliente,$nr_moneda,$limite_credito,$credito_disponible)
    {
        try {
            $query= $this->db->prepare('update cuenta_cliente SET nr_cliente = ?, nr_moneda = ?, limite_credito = ?, credito_disponible = ? WHERE nr = ?');
            $query->bindParam(1, $nr_cliente);
            $query->bindParam(2, $nr_moneda);
            $query->bindParam(3, $limite_credito);
            $query->bindParam(4, $credito_disponible);
            $query->bindParam(5, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado <> 00000)
            {
                echo '<script>alert ("Ocurrio un error al modificar el Limite de Credito, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='limite_credito.php'</script>";
            }
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