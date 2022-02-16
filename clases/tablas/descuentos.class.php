<?php
class Descuentos
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

    public function get_descuentos($query)
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

    public function delete_descuentos($nr)
    {
        try {
            $query = $this->db->prepare('delete from descuentos where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Descuento Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='descuentos.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Descuento, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Descuento, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='descuentos.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_descuentos($id_descuento,$descripcion,$valor,$vigencia_desde,$vigencia_hasta)
    {
        try {
            $query = $this->db->prepare('insert into descuentos (id_descuento,descripcion,valor,vigencia_desde,vigencia_hasta) values(?,?,?,?,?)');
            $query->bindParam(1, $id_descuento);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $valor);
            $query->bindParam(4, $vigencia_desde);
            $query->bindParam(5, $vigencia_hasta);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Descuento Agregado')</script>";
                echo "<script>location.href='descuentos.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='depositos_stock.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Descuento, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='descuentos.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Descuento no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_descuentos($nr,$id_descuento,$descripcion,$valor,$vigencia_desde,$vigencia_hasta)
    {
        try {
            $query= $this->db->prepare('update descuentos SET id_descuento = ?, descripcion = ?, valor = ?, vigencia_desde = ?, vigencia_hasta = ? WHERE nr = ?');
            $query->bindParam(1, $id_descuento);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $valor);
            $query->bindParam(4, $vigencia_desde);
            $query->bindParam(5, $vigencia_hasta);
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