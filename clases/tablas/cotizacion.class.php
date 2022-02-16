<?php
class Cotizacion
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

    public function get_cotizacion($query)
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

    public function delete_cotizacion($nr)
    {
        try {
            $query = $this->db->prepare('delete from cotizacion where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Cotizacion Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='cotizacion.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Cotizacion, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Cotizacion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cotizacion.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cotizacion($fecha,$cotizacion_compra,$cotizacion_venta,$nr_moneda)
    {
        try {
            $query = $this->db->prepare('insert into cotizacion (fecha,cotizacion_compra,cotizacion_venta,nr_moneda) values(?,?,?,?)');
            $query->bindParam(1, $fecha);
            $query->bindParam(2, $cotizacion_compra);
            $query->bindParam(3, $cotizacion_venta);
            $query->bindParam(4, $nr_moneda);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            //echo $fecha;
            if($resultado == 00000)
            {
                echo '<script>alert ("Cotizacion Agregada")</script>';
                echo "<script>location.href='Cotizacion.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='Cotizacion.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Cotizacion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='Cotizacion.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Cotizacion no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cotizacion($nr,$fecha,$cotizacion_compra,$cotizacion_venta,$nr_moneda)
    {
        try {
            $query= $this->db->prepare('update cotizacion SET fecha = ?, cotizacion_compra = ?, cotizacion_venta = ?, nr_moneda = ? WHERE nr = ?');
            $query->bindParam(1, $fecha);
            $query->bindParam(2, $cotizacion_compra);
            $query->bindParam(3, $cotizacion_venta);
            $query->bindParam(4, $nr_moneda);
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