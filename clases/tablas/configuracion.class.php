<?php
class Configuracion
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

    public function get_configuracion($query)
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

    public function update_configuracion($nr,$cantidad_f_venta,$cantidad_f_compra,$cantidad_recibo,$deposito_stock_defecto,$moneda_defecto,$nombre_empresa)
    {
        try {
            $query= $this->db->prepare('update configuracion SET cantidad_f_venta = ?, cantidad_f_compra = ?,cantidad_recibo = ?, deposito_stock_defecto = ?, moneda_defecto = ?, nombre_empresa = ? where nr = ?');
            $query->bindParam(1, $cantidad_f_venta);
            $query->bindParam(2, $cantidad_f_compra);
            $query->bindParam(3, $cantidad_recibo);
            $query->bindParam(4, $deposito_stock_defecto);
            $query->bindParam(5, $moneda_defecto);
            $query->bindParam(6, $nombre_empresa);
            $query->bindParam(7, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            
            if($resultado <> 00000)
            {
                echo '<script>alert ("Ocurrio un error al modificar la configuracion, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='configuracion.php'</script>";
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