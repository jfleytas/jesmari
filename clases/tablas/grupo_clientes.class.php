<?php
class Grupo_clientes
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

    public function get_lista_precios()
    {
        try {
            $query = $this->db->prepare('select LP.nr, LP.id_lista, LP.descripcion, LP.nr_moneda, M.descripcion descripcion_moneda from lista_de_precios LP join moneda M on LP.nr_moneda = M.nr order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_moneda($lista_precios_nr)
    {
        try {
            $query = $this->db->prepare('select LP.nr_moneda, M.descripcion from lista_de_precios LP join moneda M on LP.nr_moneda = M.nr where LP.nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_grupo_clientes($query)
    {
        $query = $this->db->prepare($query);
        $query->execute();
        return $query->fetchAll(); 
        $this->$db = null;  
    }
    
    public function delete_grupo_clientes($nr)
    {
        try {
            $query = $this->db->prepare('delete from grupo_clientes where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Grupo de Cliente Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='grupo_clientes.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Grupo de Cliente, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Grupo de Cliente, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='grupo_clientes.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_grupo_clientes($id_grupo,$descripcion,$nr_lista_precios)
    {
        try {
            $query = $this->db->prepare('insert into grupo_clientes (id_grupo,descripcion,nr_lista_precios) values(?,?,?)');
            $query->bindParam(1, $id_grupo);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_lista_precios);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Grupo Agregado')</script>";
                echo "<script>location.href='grupo_clientes.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='grupo_clientes.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el grupo, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='grupo_clientes.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Grupo no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_grupo_clientes($nr,$id_grupo,$descripcion,$nr_lista_precios)
    {
        try {
            $query= $this->db->prepare('update grupo_clientes SET id_grupo = ?, descripcion = ?, nr_lista_precios = ? WHERE nr = ?');
            $query->bindParam(1, $id_grupo);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_lista_precios);
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