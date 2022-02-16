<?php
require_once("../conexion/Conexion.php");
session_start();
class clientes
{
 
    public function __construct()
    {
        $this->dbh = new conexion();
    }
       
    //obtenemos el número de clientes totales
    public function get_all_clientes()
    {
         try {
            
            $sql = "SELECT COUNT(*) from clientes";
            $query = $this->dbh->prepare($sql);
            $query->execute();
 
            //si es true
            if($query->rowCount() == 1)
            {
                 
                 return $query->fetchColumn();
    
            }
            
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }      
    }
 
    //creamos los enlaces de nuestra paginación
    public function crea_links()
    {
 
        //html para retornar
        $html = "";
 
        //página actual
        $actual_pag = $_SESSION["actual"];
 
        //limite por página
        $limit = $_SESSION["limit"];
 
        //total de enlaces que existen
        $totalPag = floor($this->get_all_clientes()/$limit);
 
        //links delante y detrás que queremos mostrar
        $pagVisibles = 2;
 
        if($actual_pag <= $pagVisibles)
        {
            $primera_pag = 1;   
        }else{
            $primera_pag = $actual_pag - $pagVisibles; 
        }
 
        if($actual_pag + $pagVisibles <= $totalPag)
        {
            $ultima_pag = $actual_pag + $pagVisibles;
        }else{
            $ultima_pag = $totalPag;
        }
         
        $html .= '<p>';
        $html .= ($actual_pag > 1) ? 
        ' <a href="#" class="btn btn-default" onclick="paginate(0,'.$limit.')">Primera</a>' : 
        ' <a href="#" class="btn btn-default disabled">Primera</a>';
        $html .= ($actual_pag > 1) ? 
        ' <a href="#" class="btn btn-default" onclick="paginate('.(($actual_pag-1)*$limit).','.$limit.')">Anterior</a>' : 
        ' <a href="#" class="btn btn-default disabled">Anterior</a>';
         
        for($i=$primera_pag; $i<=$ultima_pag; $i++) 
        {
            $html .= ($i == $actual_pag) ? 
            ' <a class="btn btn-primary round disabled" href="#">'.$i.'</a>' : 
            ' <a class="btn btn-default" href="#" onclick="paginate('.(($i)*$limit).','.$limit.')">'.$i.'</a>';
        }
         
        $html .= ($actual_pag < $totalPag) ? 
        ' <a href="#" class="btn btn-default" onclick="paginate('.(($actual_pag+1)*$limit).','.$limit.')">Siguiente</a>' : 
        ' <a href="#" class="btn btn-default disabled">Siguiente</a>';
        $html .= ($actual_pag < $totalPag) ? 
        ' <a href="#" class="btn btn-default" onclick="paginate('.(($totalPag)*$limit).','.$limit.')">Última</a>' : 
        ' <a href="#" class="btn btn-default disabled">Última</a>';
        $html .= '</p>';
 
        return $html;
 
    }
 
    public function get_clientes($offset = 0, $limit = 5)
    {
        if($offset == 0){
            $_SESSION["actual"] = 1;
        }else{
            $_SESSION["actual"] = $offset/$limit;
        }
        $_SESSION["limit"] = $limit;
        try {
            
            $sql = "SELECT * FROM clientes OFFSET ? LIMIT ?";
            $query = $this->dbh->prepare($sql);
            $query->bindValue(1, (int) $offset, PDO::PARAM_INT); 
            $query->bindValue(2, (int) $limit, PDO::PARAM_INT); 
            $query->execute();
 
            //si existe el usuario
            if($query->rowCount() > 0)
            {
                 
                 return $query->fetchAll();
    
            }
            
        }catch(PDOException $e){
            
            print "Error!: " . $e->getMessage();
            
        }        
        
    }
}
?>