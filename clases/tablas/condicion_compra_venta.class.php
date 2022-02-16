<?php
class Condicion_Compra_Venta
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

    public function get_descuento()
    {
        try {
            $query = $this->db->prepare('select * from descuentos order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_condicion_compra_venta($query)
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
    
    public function delete_condicion_compra_venta($nr)
    {
        try {
            $query = $this->db->prepare('delete from condicion_compra_venta where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Condicion Compra y Venta Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='condicion_compra_venta.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Condicion, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Condicion Compra y Venta, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='condicion_compra_venta.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_condicion_compra_venta($id_condicion,$descripcion,$cant_dias,$cant_cuotas,$nr_descuento)
    {
        try {
            $query = $this->db->prepare('insert into condicion_compra_venta (id_condicion,descripcion,cant_dias,cant_cuotas,nr_descuento) values(?,?,?,?,?)');
            $query->bindParam(1, $id_condicion);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $cant_dias);
            $query->bindParam(4, $cant_cuotas);
            $query->bindParam(5, $nr_descuento);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Condicion Agregada')</script>";
                echo "<script>location.href='condicion_compra_venta.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='condicion_compra_venta.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Condicion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='condicion_compra_venta.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Condicion no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_condicion_compra_venta($nr,$id_condicion,$descripcion,$cant_dias,$cant_cuotas,$nr_descuento)
    {
        try {
            $query= $this->db->prepare('update condicion_compra_venta SET id_condicion = ?, descripcion = ?, cant_dias = ?, cant_cuotas = ?, nr_descuento = ? WHERE nr = ?');
            $query->bindParam(1, $id_condicion);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $cant_dias);
            $query->bindParam(4, $cant_cuotas);
            $query->bindParam(5, $nr_descuento);
            $query->bindParam(6, $nr);
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