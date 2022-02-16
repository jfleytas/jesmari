<?php
class Orden_pago
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

    //Count the ammount of register returned for a query
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
            /*if($resultado <> 00000)
            {
                echo "<script>alert ('El codigo no existe')</script>";
            }*/
            return $query; 
            $this->db = null; 
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_monedas()
    {
        try {
            $query = $this->db->prepare('select * from moneda order by nr');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_proveedor($nr_proveedor)
    {
        try {
            if (!empty($nr_proveedor))//If the parameter isn't empty return the desired row
            {
                $query = $this->db->prepare('select * from proveedor order by descripcion where nr = ?');
                $query->bindParam(1, $nr_proveedor);
                $query->execute();
                return $query->fetchAll();
                $this->$db = null; 
            }else{
                $query = $this->db->prepare('select * from proveedor order by descripcion');
                $query->execute();
                return $query->fetchAll();
                $this->$db = null;
            }            
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_medio_pago($medio_pago)
    {
        try {
            $query = $this->db->prepare('select * from medio_pago order by descripcion where nr = ?');
            $query->execute();
            return $query->fetchAll();
            $this->$db = null;          
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_caja($sucursal)
    {
        try {
            if (!empty($sucursal))//If the parameter isn't empty return the desired row
            {
                $query = $this->db->prepare('select * from cajas where nr_sucursal = ?');
                $query->bindParam(1, $sucursal);
                $query->execute();
                return $query->fetchAll();
                $this->$db = null; 
            }            
        }catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function get_detalle($nr_cabecera)
    {
        try {
            $query = $this->db->prepare('select * from detalle_orden_pago where nr= ?');
            $query->bindParam(1, $nr_cabecera);
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

    public function delete_cabecera_orden_pago($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_orden_pago where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Orden de Pago Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Orden de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_pago.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_orden_pago($nr,$fecha_pago,$nr_proveedor,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$obs,$estado)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_orden_pago (nr,fecha_pago,nr_proveedor,nr_sucursal,nr_caja,nr_moneda,cotizacion_compra,cotizacion_venta,total_pago,nr_user,obs,estado) values(?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $fecha_pago);
            $query->bindParam(3, $nr_proveedor);
            $query->bindParam(4, $nr_sucursal);
            $query->bindParam(5, $nr_caja);
            $query->bindParam(6, $nr_moneda);
            $query->bindParam(7, $cotizacion_compra);
            $query->bindParam(8, $cotizacion_venta);
            $query->bindParam(9, $total_pago);
            $query->bindParam(10, $nr_user);
            $query->bindParam(11, $obs);
            $query->bindParam(12, $estado);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='orden_pago.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Pago')</script>";
                echo "<script>location.href='orden_pago_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Pago no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_orden_pago($nr,$fecha_pago,$nr_proveedor,$nr_sucursal,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$obs,$estado)
    {
        try {
            $query= $this->db->prepare('update cabecera_orden_pago SET nr_proveedor = ?, fecha_pago = ?, nr_sucursal = ?, nr_caja = ?, nr_moneda = ?, cotizacion_compra = ?, cotizacion_venta = ?, total_pago = ?, nr_user = ?, obs = ?, estado = ? WHERE nr = ?');
            $query->bindParam(1, $fecha_pago);
            $query->bindParam(2, $nr_proveedor);
            $query->bindParam(3, $nr_sucursal);
            $query->bindParam(4, $nr_caja);
            $query->bindParam(5, $nr_moneda);
            $query->bindParam(6, $cotizacion_compra);
            $query->bindParam(7, $cotizacion_venta);
            $query->bindParam(8, $total_pago);
            $query->bindParam(9, $nr_user);
            $query->bindParam(10, $obs);
            $query->bindParam(11, $estado);
            $query->bindParam(12, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_orden_pago($nr,$total_pago)
    {
        try {
            $query= $this->db->prepare('update cabecera_orden_pago SET total_pago = ? WHERE nr = ?');
            $query->bindParam(1, $total_pago);
            $query->bindParam(2, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Orden de Pago Agregada')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Pago')</script>";
                echo "<script>location.href='orden_pago_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Pago no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function delete_orden_pago($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_orden_pago where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Aplicaciones
                try 
                {
                    $query = $this->db->prepare('delete from detalle_aplicacion_pago where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode();  
                    //return $resultado;
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {//If we can delete the Detail we can delete the Cabecera

                        $query = $this->db->prepare('delete from cabecera_orden_pago where nr = ?');
                        $query->bindParam(1, $nr);
                        $query->execute();
                        $resultado3 = $query->errorCode(); 
                        //echo $resultado;
                        if($resultado3 == 00000)
                        {
                            echo "<script>alert ('Orden de Pago Eliminada')".$resultado."</script>";
                            echo "<script>window.location.href='orden_pago.php'</script>";
                        }else if ($resultado3 == 23503){
                            echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
                        }else if ($resultado3 == 23514){
                            echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
                        }else{
                            echo '<script>alert ("Ocurrio un error al eliminar la Orden de Pago, codigo de error: '.$resultado.'")</script>';
                            echo "<script>location.href='orden_pago.php'</script>";
                        }
                        return $query;
                        $this->$db = null;
                    }
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado == 23514){
                echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Orden de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_pago.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_orden_pago($nr,$nr_medio_pago)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_orden_pago where nr = ? and nr_medio_pago = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_medio_pago);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Orden de Pago Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar la Orden de Pago')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado2 == 23514){
                echo "<script>alert ('No se puede eliminar')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Orden de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_pago.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_orden_pago($nr,$nr_medio_pago,$monto_pago,$total_linea,$obs)
    {
        try {
            $query = $this->db->prepare('insert into detalle_orden_pago (nr,nr_medio_pago,monto_pago,total_linea,obs) values(?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_medio_pago);
            $query->bindParam(3, $monto_pago);
            $query->bindParam(4, $total_linea);
            $query->bindParam(5, $obs);
            $query->execute();
            $resultado = $query->errorCode(); 
            return $resultado; 
            echo $resultado;
            //echo '<script>alert ("Codigo de error: '.$resultado.'")</script>';
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='orden_pago.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el Medio de Pago')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Orden de Pago')</script>";
                echo "<script>location.href='orden_pago_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Orden de Pago no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_detalle_orden_pago($nr,$nr_medio_pago,$monto_pago,$total_linea,$obs)
    {
        try {
            $query= $this->db->prepare('update detalle_orden_pago SET obs = ?, monto_pago = ?, total_linea = ? WHERE nr = ? AND nr_medio_pago = ?');
            $query->bindParam(1, $monto_pago);
            $query->bindParam(2, $total_linea);
            $query->bindParam(3, $obs);
            $query->bindParam(4, $nr_medio_pago);
            $query->bindParam(5, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function delete_detalle_aplicacion_pago($nr,$nr_factura_compra)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_aplicacion_pago where nr = ? and nr_factura_compra = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_factura_compra);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Aplicacion de Pago Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar la Aplicacion de Pago')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else if ($resultado2 == 23514){
                echo "<script>alert ('No se puede eliminar')</script>";
                echo "<script>window.location.href='orden_pago.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Aplicacion de Pago, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='orden_pago.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_aplicacion_pago($nr,$nr_factura_compra,$nr_nota_credito_compra,$monto_aplicado,$total_linea)
    {
        try {
            $query = $this->db->prepare('insert into detalle_aplicacion_pago (nr,nr_factura_compra,nr_nota_credito_compra,monto_aplicado,total_linea) values(?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_factura_compra);
            $query->bindParam(3, $nr_nota_credito_compra);
            $query->bindParam(4, $monto_aplicado);
            $query->bindParam(5, $total_linea);
            $query->execute();
            $resultado = $query->errorCode(); 
            return $resultado; 
            echo $resultado;
            //echo '<script>alert ("Codigo de error: '.$resultado.'")</script>';
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='aplicacion_pago.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe la Factura compra')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Aplicacion')</script>";
                echo "<script>location.href='orden_pago.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Aplicacion de Pago no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_detalle_aplicacion_pago($nr,$nr_factura_compra,$nr_nota_credito_compra,$monto_aplicado,$total_linea)
    {
        try {
            $query= $this->db->prepare('update detalle_aplicacion_pago SET nr_nota_credito_compra = ?, monto_aplicado = ?, total_linea = ? WHERE nr = ? AND nr_factura_compra = ?');
            $query->bindParam(1, $nr_nota_credito_compra);
            $query->bindParam(2, $monto_aplicado);
            $query->bindParam(3, $total_linea);
            $query->bindParam(4, $nr_factura_compra);
            $query->bindParam(5, $nr);
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