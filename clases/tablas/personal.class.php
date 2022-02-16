<?php
class Personal
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

    public function get_personal($query)
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

    public function get_tipo_personal()
    {
        try {
            $query = $this->db->prepare('select * from tipo_personal order by descripcion');
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

    public function get_user()
    {
        try {
            $query = $this->db->prepare('select * from users order by id_user');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }
    
    public function delete_personal($nr)
    {
        try {
            $query = $this->db->prepare('delete from personal where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Personal Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='personal.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Personal, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Personal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='personal.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_personal($id_personal,$nombre_apellido,$documento_nr,$direccion,$telefono,$email,$nr_tipo_personal,$nr_sucursal,$nr_user,$otros_datos)
    {
        try {
            $query = $this->db->prepare('insert into personal(id_personal,nombre_apellido,documento_nr,direccion,telefono,email,nr_tipo_personal,nr_sucursal,nr_user,otros_datos) values(?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $id_personal);
            $query->bindParam(2, $nombre_apellido);
            $query->bindParam(3, $documento_nr);
            $query->bindParam(4, $direccion);
            $query->bindParam(5, $telefono);
            $query->bindParam(6, $email);
            $query->bindParam(7, $nr_tipo_personal);
            $query->bindParam(8, $nr_sucursal );
            $query->bindParam(9, $nr_user);
            $query->bindParam(10, $otros_datos);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Personal Agregado')</script>";
                echo "<script>location.href='personal.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='personal.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Personal, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='personalcript>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Personal no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_personal($nr,$id_personal,$nombre_apellido,$documento_nr,$direccion,$telefono,$email,$nr_tipo_personal,$nr_sucursal,$nr_user,$otros_datos)
    {
        try {
            $query= $this->db->prepare('update personal set id_personal = ?, nombre_apellido = ?, documento_nr = ?, direccion = ?, telefono = ?, email = ?, nr_tipo_personal = ?, nr_sucursal = ?, nr_user = ?, otros_datos = ? WHERE nr = ?');
            $query->bindParam(1, $id_personal);
            $query->bindParam(2, $nombre_apellido);
            $query->bindParam(3, $documento_nr);
            $query->bindParam(4, $direccion);
            $query->bindParam(5, $telefono);
            $query->bindParam(6, $email);
            $query->bindParam(7, $nr_tipo_personal);
            $query->bindParam(8, $nr_sucursal );
            $query->bindParam(9, $nr_user);
            $query->bindParam(10, $otros_datos);
            $query->bindParam(11, $nr);
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