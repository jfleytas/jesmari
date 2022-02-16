<?php
class Lista_Precios
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

    public function get_lista_precios($query)
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
    
    public function delete_lista_precios($nr)
    {
        try {
            $query = $this->db->prepare('delete from lista_de_precios where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Lista de Precios Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='lista_precios.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Lista de Precios, es utilizada en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Lista de Precios, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='lista_precios.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_lista_precios($id_lista,$descripcion,$vigencia_desde,$vigencia_hasta,$nr_moneda)
    {
        try {
            $query = $this->db->prepare('insert into lista_de_precios (id_lista,descripcion,vigencia_desde,vigencia_hasta,nr_moneda) values(?,?,?,?,?)');
            $query->bindParam(1, $id_lista);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $vigencia_desde);
            $query->bindParam(4, $vigencia_hasta);
            $query->bindParam(5, $nr_moneda);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Lista de Precios Agregada')</script>";
                echo "<script>location.href='lista_precios.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='lista_precios.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar la Lista de Precios, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='lista_precios.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Lista de Precios no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_lista_precios($nr,$id_lista,$descripcion,$vigencia_desde,$vigencia_hasta,$nr_moneda)
    {
        try {
            $query= $this->db->prepare('update lista_de_precios SET id_lista = ?, descripcion = ?, vigencia_desde = ?, vigencia_hasta = ?, nr_moneda = ? WHERE nr = ?');
            $query->bindParam(1, $id_lista);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $vigencia_desde);
            $query->bindParam(4, $vigencia_hasta);
            $query->bindParam(5, $nr_moneda);
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