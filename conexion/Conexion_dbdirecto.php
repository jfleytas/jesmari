<?php
class Conexion
{
    private static $dbName = 'jesmari' ;
    //private static $dbName = 'multiventas' ;
    private static $dbHost = 'localhost' ;
    private static $dbUsername = 'postgres';
    private static $dbPassword = '123456789';
     
    private static $cont  = null;
     
    public function __construct() {
        die('Init function is not allowed');
    }
     
    public static function conexion()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {     
        try
        {
          self::$cont =  new PDO( "pgsql:host=".self::$dbHost.";"."dbname=".self::$dbName.";"."user=".self::$dbUsername.";"."password=".self::$dbPassword); 
        }
        catch(PDOException $e)
        {
          die($e->getMessage()); 
        }
       }
       return self::$cont;
    }
     
    public static function close()
    {
        self::$cont = null;
    }
}
?>