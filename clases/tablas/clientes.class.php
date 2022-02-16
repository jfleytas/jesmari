<?php
class Clientes
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
            $resultado = $query->errorCode();  
            //echo $resultado;
            //echo $query;
            return $query; 
            $this->db = null; 
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }   

    public function get_pais()
    {
        try {
            $query = $this->db->prepare('select * from pais order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_vendedor()
    {
        try {
            $query = $this->db->prepare('select * from personal order by nombre_apellido');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_condicion()
    {
        try {
            $query = $this->db->prepare('select * from condicion_compra_venta order by descripcion');
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
            $query = $this->db->prepare('select * from lista_de_precios order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_grupo()
    {
        try {
            $query = $this->db->prepare('select * from grupo_clientes order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_clientes($query)
    {
        $query = $this->db->prepare($query);
        $query->execute();
        return $query->fetchAll(); 
        $this->$db = null;  
    }
    
    public function delete_clientes($nr)
    {
        try {
            $query = $this->db->prepare('delete from clientes where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Cliente Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='clientes.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Cliente, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Cliente, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='clientes.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_clientes($id_cliente,$razon_social,$ruc,$nr_condicion,$direccion,$telefono,$contacto,$ciudad,$nr_pais,$nr_vendedor,$otros_datos,$nr_grupo,$email,$pagina_web)
    {
        try {
            $query = $this->db->prepare('insert into clientes (id_cliente,razon_social,ruc,nr_condicion,direccion,telefono,contacto,ciudad,nr_pais,nr_vendedor,otros_datos,nr_grupo,email,pagina_web) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $id_cliente);
            $query->bindParam(2, $razon_social);
            $query->bindParam(3, $ruc);
            $query->bindParam(4, $nr_condicion);
            $query->bindParam(5, $direccion);
            $query->bindParam(6, $telefono);
            $query->bindParam(7, $contacto);
            $query->bindParam(8, $ciudad);
            $query->bindParam(9, $nr_pais);
            $query->bindParam(10, $nr_vendedor);
            $query->bindParam(11, $otros_datos);
            $query->bindParam(12, $nr_grupo);
            $query->bindParam(13, $email);
            $query->bindParam(14, $pagina_web);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Cliente Agregado')</script>";
                echo "<script>location.href='clientes.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='clientes.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Cliente, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='clientes.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Cliente no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_clientes($nr,$id_cliente,$razon_social,$ruc,$nr_condicion,$direccion,$telefono,$contacto,$ciudad,$nr_pais,$nr_vendedor,$otros_datos,$nr_grupo,$email,$pagina_web)
    {
        try {
            $query= $this->db->prepare('update clientes SET id_cliente = ?, razon_social = ?, ruc = ?, nr_condicion = ?, direccion = ?, telefono = ?, contacto = ?, ciudad = ?, nr_pais = ?, nr_vendedor = ?, otros_datos = ?, nr_grupo = ?, email = ?, pagina_web = ? WHERE nr = ?');
            $query->bindParam(1, $id_cliente);
            $query->bindParam(2, $razon_social);
            $query->bindParam(3, $ruc);
            $query->bindParam(4, $nr_condicion);
            $query->bindParam(5, $direccion);
            $query->bindParam(6, $telefono);
            $query->bindParam(7, $contacto);
            $query->bindParam(8, $ciudad);
            $query->bindParam(9, $nr_pais);
            $query->bindParam(10, $nr_vendedor);
            $query->bindParam(11, $otros_datos);
            $query->bindParam(12, $nr_grupo);
            $query->bindParam(13, $email);
            $query->bindParam(14, $pagina_web);
            $query->bindParam(15, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado <> 00000)
            {
                echo '<script>alert ("Ocurrio un error al modificar el Cliente, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='clientes.php'</script>";
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