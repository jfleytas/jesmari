<?php
//Comprobamos que sea una petición ajax
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
 
    //Llamamos a la clase que contiene la paginacion
    require("clientes2.php");
    //Creamos una instancia
    $clientes = new clientes();
    //Comprobamos si han llegado las variables get para setearlas
    $offset = !isset($_GET["offset"]) || $_GET["offset"] == "undefined" ? 0 : $_GET["offset"];
    $limit = !isset($_GET["limit"]) || $_GET["limit"] == "undefined" ? 5 : $_GET["limit"];
    //Obtenemos los posts
    $pag = $clientes->get_clients($offset,$limit);
    //Obtenemos los enlaces para estos posts
    $links = $clientes->crea_links();
    //Los devolvemos en formato json
    echo json_encode(array("clientes" => $pag,"links" => $links));
 
}else{
    //Si no es una petición ajax decimos que no existe 
    echo "Esta página no existe";
 
}
?>