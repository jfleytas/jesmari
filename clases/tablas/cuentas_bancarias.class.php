<?php
class Cuentas_Bancarias
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

    public function get_bancos()
    {
        try {
            $query = $this->db->prepare('select * from banco order by descripcion');
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

    public function get_cuentas_bancarias($query)
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
    
    public function delete_cuentas_bancarias($nr)
    {
        try {
            $query = $this->db->prepare('delete from cuentas_bancarias where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Cuenta Bancaria Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='cuentas_bancarias.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Cuenta Bancaria, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Cuenta Bancaria, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cuentas_bancarias.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cuentas_bancarias($nro_cuenta,$nr_banco,$nr_moneda,$descripcion)
    {
        try {
            $query = $this->db->prepare('insert into cuentas_bancarias (nro_cuenta,nr_banco,nr_moneda,descripcion) values(?,?,?,?)');
            $query->bindParam(1, $nro_cuenta);
            $query->bindParam(2, $nr_banco);
            $query->bindParam(3, $nr_moneda);
            $query->bindParam(4, $descripcion);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Cuenta Bancaria Agregada')</script>";
                echo "<script>location.href='cuentas_bancarias.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='cuentas_bancarias.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Cuenta Bancaria, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cuentas_bancarias.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Cuenta Bancaria no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cuentas_bancarias($nr,$nro_cuenta,$nr_banco,$nr_moneda,$descripcion)
    {
        try {
            $query= $this->db->prepare('update cuentas_bancarias SET nro_cuenta = ?, nr_banco = ?, nr_moneda = ?, descripcion = ? WHERE nr = ?');
            $query->bindParam(1, $nro_cuenta);
            $query->bindParam(2, $nr_banco);
            $query->bindParam(3, $nr_moneda);
            $query->bindParam(4, $descripcion);
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