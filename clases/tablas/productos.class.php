<?php
class Productos
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

    public function get_productos($query)
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

    public function get_unidad_medida()
    {
        try {
            $query = $this->db->prepare('select * from unidad_medida order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_impuesto()
    {
        try {
            $query = $this->db->prepare('select * from tipo_impuesto order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

      public function get_tipo_producto()
    {
        try {
            $query = $this->db->prepare('select * from tipo_producto order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_marca()
    {
        try {
            $query = $this->db->prepare('select * from marca order by descripcion');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_clasificacion($parametro)
    {
        try {
            //if ((!empty($parametro))//If the parameter isn't empty return the desired row
            if ($parametro >=0)//If the parameter isn't empty return the desired row
            {
                $query = $this->db->prepare('select * from clasificacion where aplicado_a = ?');
                $query->bindParam(1, $parametro);
                $query->execute();
                $resultado = $query->errorCode();  
                echo $resultado;
                return $query->fetchAll();
                $this->$db = null;          
            }else{
                $query = $this->db->prepare('select * from clasificacion order by descripcion');
                $query->execute();
                $resultado = $query->errorCode();  
                //echo $resultado;
                return $query->fetchAll();
                $this->$db = null;
            }
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_productos($nr)
    {
        try {
            $query = $this->db->prepare('delete from productos where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Producto Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='productos.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Producto, es utilizado en otras tablas')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Producto, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='productos.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_productos($id_producto,$codigo_barra,$descripcion,$nr_marca,$nr_unidad_medida,$nr_impuesto,$nr_clasificacion,$empaque,$otros_datos,$nr_tipo,$fecha_alta,$temporada,$inicio_temporada,$fin_temporada)
    {
        try {
            $query = $this->db->prepare('insert into productos (id_producto,codigo_barra,descripcion,nr_marca,nr_unidad_medida,nr_impuesto,nr_clasificacion,empaque,otros_datos,nr_tipo,fecha_alta,temporada,inicio_temporada,fin_temporada) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $id_producto);
            $query->bindParam(2, $codigo_barra);
            $query->bindParam(3, $descripcion);
            $query->bindParam(4, $nr_marca);
            $query->bindParam(5, $nr_unidad_medida);
            $query->bindParam(6, $nr_impuesto);
            $query->bindParam(7, $nr_clasificacion);
            $query->bindParam(8, $empaque);
            $query->bindParam(9, $otros_datos);
            $query->bindParam(10, $nr_tipo);
            $query->bindParam(11, $fecha_alta);
            $query->bindParam(12, $temporada);
            $query->bindParam(13, $inicio_temporada);
            $query->bindParam(14, $fin_temporada);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;

            if($resultado == 00000)
            {
                echo "<script>alert ('Producto Agregado')</script>";
                echo "<script>location.href='productos.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El codigo ya existe')</script>";
                echo "<script>location.href='productos.php#modal'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al insertar el Producto, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='productos.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Producto no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_productos($nr,$id_producto,$codigo_barra,$descripcion,$nr_marca,$nr_unidad_medida,$nr_impuesto,$nr_clasificacion,$empaque,$otros_datos,$nr_tipo,$fecha_alta,$temporada,$inicio_temporada,$fin_temporada)
    {
        try {
            $query= $this->db->prepare('update productos SET id_producto = ?, codigo_barra = ?,descripcion = ?, nr_marca = ?, nr_unidad_medida = ?, nr_impuesto = ?, nr_clasificacion = ?, empaque = ?, otros_datos = ?, nr_tipo = ?, fecha_alta = ?, temporada = ?, inicio_temporada = ?, fin_temporada = ? WHERE nr = ?');
            $query->bindParam(1, $id_producto);
            $query->bindParam(2, $codigo_barra);
            $query->bindParam(3, $descripcion);
            $query->bindParam(4, $nr_marca);
            $query->bindParam(5, $nr_unidad_medida);
            $query->bindParam(6, $nr_impuesto);
            $query->bindParam(7, $nr_clasificacion);
            $query->bindParam(8, $empaque);
            $query->bindParam(9, $otros_datos);
            $query->bindParam(10, $nr_tipo);
            $query->bindParam(11, $fecha_alta);
            $query->bindParam(12, $temporada);
            $query->bindParam(13, $inicio_temporada);
            $query->bindParam(14, $fin_temporada);
            $query->bindParam(15, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            
            if($resultado <> 00000)
            {
                echo '<script>alert ("Ocurrio un error al modificar el Producto, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='productos.php'</script>";
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