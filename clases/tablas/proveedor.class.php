<?php
class Proveedor
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

    public function get_proveedor($query)
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
    
    public function delete_proveedor($nr)
    {
        try {
            $query = $this->db->prepare('delete from proveedor where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Proveedor Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='proveedor.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Proveedor, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='proveedor.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_proveedor($id_proveedor,$descripcion,$nr_condicion,$direccion,$telefono,$ciudad,$nr_pais,$contacto,$pagina_web,$email,$otros_datos,$nr_moneda)
    {
        try {
            $query = $this->db->prepare('insert into proveedor(id_proveedor,descripcion,nr_condicion,direccion,telefono,ciudad,nr_pais,contacto,pagina_web,email,otros_datos,nr_moneda) values(?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $id_proveedor);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_condicion);
            $query->bindParam(4, $direccion);
            $query->bindParam(5, $telefono);
            $query->bindParam(6, $ciudad);
            $query->bindParam(7, $nr_pais);
            $query->bindParam(8, $contacto);
            $query->bindParam(9, $pagina_web);
            $query->bindParam(10, $email);
            $query->bindParam(11, $otros_datos);
            $query->bindParam(12, $nr_moneda);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Proveedor Agregado')</script>";
                echo "<script>location.href='proveedor.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='proveedor.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='proveedorcript>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Proveedor no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_proveedor($nr,$id_proveedor,$descripcion,$nr_condicion,$direccion,$telefono,$ciudad,$nr_pais,$contacto,$pagina_web,$email,$otros_datos,$nr_moneda)
    {
        try {
            $query= $this->db->prepare('update proveedor set id_proveedor = ?, descripcion = ?, nr_condicion = ?, direccion = ?, telefono = ?, ciudad = ?, nr_pais = ?, contacto = ?, pagina_web = ?, email = ?, otros_datos = ?, nr_moneda = ? WHERE nr = ?');
            $query->bindParam(1, $id_proveedor);
            $query->bindParam(2, $descripcion);
            $query->bindParam(3, $nr_condicion);
            $query->bindParam(4, $direccion);
            $query->bindParam(5, $telefono);
            $query->bindParam(6, $ciudad);
            $query->bindParam(7, $nr_pais);
            $query->bindParam(8, $contacto);
            $query->bindParam(9, $pagina_web);
            $query->bindParam(10, $email);
            $query->bindParam(11, $otros_datos);
            $query->bindParam(12, $nr_moneda);
            $query->bindParam(13, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado <> 00000)
            {
                echo '<script>alert ("Ocurrio un error al actualizar el Proveedor, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='proveedorcript>";
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