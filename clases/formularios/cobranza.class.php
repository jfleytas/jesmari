<?php
class Cobranza
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

    public function get_cobrador()
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

    public function get_cliente($nr_cliente)
    {
        try {
            if (!empty($nr_cliente))//If the parameter isn't empty return the desired row
            {
                $query = $this->db->prepare('select * from clientes order by razon_social where nr = ?');
                $query->bindParam(1, $nr_cliente);
                $query->execute();
                return $query->fetchAll();
                $this->$db = null; 
            }else{
                $query = $this->db->prepare('select * from clientes order by descripcion');
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
            $query = $this->db->prepare('select * from detalle_cobranza where nr= ?');
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

    public function delete_cabecera_cobranza($nr)
    {
        try {
            $query = $this->db->prepare('delete from cabecera_cobranza where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                echo "<script>alert ('Recibo Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Recibo')</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Recibo, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cobranza.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function insert_cabecera_cobranza($nr,$nr_cobranza,$fecha_cobranza,$nr_cliente,$nr_sucursal,$nr_caja,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$nr_cobrador,$obs,$estado)
    {
        try {
            $query = $this->db->prepare('insert into cabecera_cobranza (nr,nr_cobranza,fecha_cobranza,nr_cliente,nr_sucursal,nr_caja,nr_moneda,cotizacion_compra,cotizacion_venta,total_pago,nr_user,nr_cobrador,obs,estado) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_cobranza);
            $query->bindParam(3, $fecha_cobranza);
            $query->bindParam(4, $nr_cliente);
            $query->bindParam(5, $nr_sucursal);
            $query->bindParam(6, $nr_caja);
            $query->bindParam(7, $nr_moneda);
            $query->bindParam(8, $cotizacion_compra);
            $query->bindParam(9, $cotizacion_venta);
            $query->bindParam(10, $total_pago);
            $query->bindParam(11, $nr_user);
            $query->bindParam(12, $nr_cobrador);
            $query->bindParam(13, $obs);
            $query->bindParam(14, $estado);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            if($resultado == 00000)
            {
                //echo "<script>alert ('Condicion Agregada')</script>";
                //echo "<script>location.href='cobranza.php'</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Recibo')</script>";
                echo "<script>location.href='cobranza_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Recibo no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_cabecera_cobranza($nr,$nr_cobranza,$fecha_cobranza,$nr_cliente,$nr_sucursal,$nr_moneda,$cotizacion_compra,$cotizacion_venta,$total_pago,$nr_user,$nr_cobrador,$obs,$estado)
    {
        try {
            $query= $this->db->prepare('update cabecera_cobranza SET nr_cobranza = ?, nr_cliente = ?, fecha_cobranza = ?, nr_sucursal = ?, nr_caja = ?, nr_moneda = ?, cotizacion_compra = ?, cotizacion_venta = ?, total_pago = ?, nr_user = ?, nr_cobrador = ?, obs = ?, estado = ? WHERE nr = ?');
            $query->bindParam(1, $nr_cobranza);
            $query->bindParam(2, $fecha_cobranza);
            $query->bindParam(3, $nr_cliente);
            $query->bindParam(4, $nr_sucursal);
            $query->bindParam(5, $nr_caja);
            $query->bindParam(6, $nr_moneda);
            $query->bindParam(7, $cotizacion_compra);
            $query->bindParam(8, $cotizacion_venta);
            $query->bindParam(9, $total_pago);
            $query->bindParam(10, $nr_user);
            $query->bindParam(11, $nr_cobrador);
            $query->bindParam(12, $obs);
            $query->bindParam(13, $estado);
            $query->bindParam(14, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //echo $resultado;
            return $query;
            $this->db = null;
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function update_cobranza($nr,$total_pago)
    {
        try {
            $query= $this->db->prepare('update cabecera_cobranza SET total_pago = ? WHERE nr = ?');
            $query->bindParam(1, $total_pago);
            $query->bindParam(2, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            if($resultado == 00000)
            {
                echo "<script>alert ('Recibo Agregado')</script>";
                echo "<script>window.close()</script>";
            }else if ($resultado == 23505){
                echo "<script>alert ('El Nro. ya existe')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Recibo')</script>";
                echo "<script>location.href='cobranza_form.php'</script>";
            }
            return $query; 
            return $resultado;
            echo $resultado;
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Recibo no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function delete_cobranza($nr)
    {
        try {
            $query = $this->db->prepare('delete from detalle_cobranza where nr = ?');
            $query->bindParam(1, $nr);
            $query->execute();
            $resultado = $query->errorCode();  
            //return $resultado;
            //echo $resultado;
            if($resultado == 00000)
            {//If we can delete the Detail we can delete the Aplicaciones
                try 
                {
                    $query = $this->db->prepare('delete from detalle_aplicacion_cobranza where nr = ?');
                    $query->bindParam(1, $nr);
                    $query->execute();
                    $resultado2 = $query->errorCode();  
                    //return $resultado;
                    //echo $resultado;
                    if($resultado2 == 00000)
                    {//If we can delete the Detail we can delete the Cabecera

                        $query = $this->db->prepare('delete from cabecera_cobranza where nr = ?');
                        $query->bindParam(1, $nr);
                        $query->execute();
                        $resultado3 = $query->errorCode(); 
                        //echo $resultado;
                        if($resultado3 == 00000)
                        {
                            echo "<script>alert ('Recibo Eliminado')".$resultado."</script>";
                            echo "<script>window.location.href='cobranza.php'</script>";
                        }else if ($resultado3 == 23503){
                            echo "<script>alert ('No se puede eliminar el Recibo')</script>";
                        }else if ($resultado3 == 23514){
                            echo "<script>alert ('No se puede eliminar el Recibo')</script>";
                        }else{
                            echo '<script>alert ("Ocurrio un error al eliminar el Recibo, codigo de error: '.$resultado.'")</script>';
                            echo "<script>location.href='cobranza.php'</script>";
                        }
                        return $query;
                        $this->$db = null;
                    }
                } catch (PDOException $e) {
                    $e->getMessage();
                    return $e;
                }
            }else if ($resultado == 23503){
                echo "<script>alert ('No se puede eliminar el Recibo')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado == 23514){
                echo "<script>alert ('No se puede eliminar el Recibo')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Recibo, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cobranza.php'</script>";
            }
            return $query;
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }

    public function delete_detalle_cobranza($nr,$nr_medio_pago)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_cobranza where nr = ? and nr_medio_pago = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_medio_pago);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Recibo Eliminado')".$resultado."</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar el Recibo')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado2 == 23514){
                echo "<script>alert ('No se puede eliminar')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar el Recibo, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cobranza.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_cobranza($nr,$nr_medio_pago,$monto_pago,$total_linea,$obs)
    {
        try {
            $query = $this->db->prepare('insert into detalle_cobranza (nr,nr_medio_pago,monto_pago,total_linea,obs) values(?,?,?,?,?)');
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
                //echo "<script>location.href='cobranza.php'</script>";
            }else if ($resultado == 23503){
                echo "<script>alert ('Ya existe el Medio de Pago')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar el Recibo')</script>";
                echo "<script>location.href='cobranza_form.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: El Recibo no pudo ser agregado.<br>".$e->getMessage();
        }
    }

    public function update_detalle_cobranza($nr,$nr_medio_pago,$monto_pago,$total_linea,$obs)
    {
        try {
            $query= $this->db->prepare('update detalle_cobranza SET obs = ?, monto_pago = ?, total_linea = ? WHERE nr = ? AND nr_medio_pago = ?');
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

    public function delete_detalle_aplicacion_cobranza($nr,$nr_factura_venta)
    {
        try 
        {
            $query = $this->db->prepare('delete from detalle_aplicacion_cobranza where nr = ? and nr_factura_venta = ?');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_factura_venta);
            $query->execute();
            $resultado2 = $query->errorCode(); 
            //echo $resultado;
            if($resultado2 == 00000)
            {
                echo "<script>alert ('Aplicacion de Cobranza Eliminada')".$resultado."</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado2 == 23503){
                echo "<script>alert ('No se puede eliminar la Aplicacion de Cobranza')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else if ($resultado2 == 23514){
                echo "<script>alert ('No se puede eliminar')</script>";
                echo "<script>window.location.href='cobranza.php'</script>";
            }else{
                echo '<script>alert ("Ocurrio un error al eliminar la Aplicacion de Cobranza, codigo de error: '.$resultado.'")</script>';
                echo "<script>location.href='cobranza.php'</script>";
            }
            return $query; 
            $this->$db = null;
        } catch (PDOException $e) {
            $e->getMessage();
            return $e;
        }
    }


    public function insert_detalle_aplicacion_cobranza($nr,$nr_factura_venta,$nr_nota_credito_venta,$monto_aplicado,$total_linea)
    {
        try {
            $query = $this->db->prepare('insert into detalle_aplicacion_cobranza (nr,nr_factura_venta,nr_nota_credito_venta,monto_aplicado,total_linea) values(?,?,?,?,?)');
            $query->bindParam(1, $nr);
            $query->bindParam(2, $nr_factura_venta);
            $query->bindParam(3, $nr_nota_credito_venta);
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
                echo "<script>alert ('Ya existe la Factura Venta')</script>";
            }else{
                echo "<script>alert ('Ocurrio un error al insertar la Aplicacion')</script>";
                echo "<script>location.href='cobranza.php'</script>";
            }
            return $query; 
            $this->db = null; 
        } catch (PDOException $e) {
            $e->getMessage();
        }catch (Exception $e) {
            echo "General Error: La Aplicacion de Cobranza no pudo ser agregada.<br>".$e->getMessage();
        }
    }

    public function update_detalle_aplicacion_cobranza($nr,$nr_factura_venta,$nr_nota_credito_venta,$monto_aplicado,$total_linea)
    {
        try {
            $query= $this->db->prepare('update detalle_aplicacion_cobranza SET nr_nota_credito_venta = ?, monto_aplicado = ?, total_linea = ? WHERE nr = ? AND nr_factura_venta = ?');
            $query->bindParam(1, $nr_nota_credito_venta);
            $query->bindParam(2, $monto_aplicado);
            $query->bindParam(3, $total_linea);
            $query->bindParam(4, $nr_factura_venta);
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