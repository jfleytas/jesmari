<?php
class Users
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

    public function get_users($query)
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
    
    public function delete_users($nr)
    {
        try {
            $query = $this->db->prepare('delete from users where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Usuario Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='users.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Usuario, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Usuario, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='users.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    //public function insert_users($id_user,$nombre_apellido,$password_user)
    public function insert_users($id_user,$rol,$password_user)
    {
        try {
            /*$query = $this->db->prepare('insert into users (id_user,nombre_apellido,password_user) values(?,?,?)');
            $query->bindParam(1, $id_user);
            $query->bindParam(2, $nombre_apellido);
            $query->bindParam(3, $password_user);*/
            $sentence = "CREATE ROLE ".$id_user ." LOGIN PASSWORD '".$password_user."' SUPERUSER INHERIT CREATEDB CREATEROLE;";
            //echo $sentence;
            $query = $this->db->prepare($sentence);
            $query->execute();
            $resultado = $query->errorCode();  
            //Assign the role
            $sentence = "GRANT ".$rol ." TO ".$id_user.";";
            $query = $this->db->prepare($sentence);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Usuario Agregado')</script>";
                echo "<script>location.href='users.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El nombre de usuario ya existe')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Usuario, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='users.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El usuario no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_users($nr,$id_user,$nombre_apellido,$password_user)
    {
        try {
            $query= $this->db->prepare('update users SET id_user = ?, nombre_apellido = ?, password_user = ? WHERE nr = ?');
            $query->bindParam(1, $id_user);
            $query->bindParam(2, $nombre_apellido);
            $query->bindParam(3, $password_user);
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