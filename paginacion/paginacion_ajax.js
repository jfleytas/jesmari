//evitamos el comportamiento por defecto de los links
$(document).on("click", "a", function(e){
    e.preventDefault();    
})
 
function paginate(offset, limit)
{
    //obtenemos los posts via get con jQuery
    $.get("data.php/?offset=" + offset + "&limit=" + limit, function(data){
        if(data)
        {
            $html = "";
            //parseamos el json
            json = JSON.parse(data);
            //lo recorremos e insertamos en la variable $html
            for(datos in json.clientes)
            {
                $html += "<div class='panel panel-default'>";
                $html += "<div class='panel-heading'>";
                $html += "<p>Codigo: " + json.clientes[datos].id_cliente + "</p>";
                $html += "<p>Razon Social: " + json.clientes[datos].razon_social + "</p>";
                $html += "</div>";
                $html += "</div>";
            }
 
            //cargamos los posts en el div paginacion
            $("#paginacion").html($html);
            //cargamos los links en el div links
            $(".links").html("");
            $(".links").html(json.links);
    
            //hacemos una sencilla animacion
            $(document.body).animate({opacity: 0.3}, 400);
            $("html, body").animate({ scrollTop: 0 }, 400);
            $(document.body).animate({opacity: 1}, 400);        
        }
    })
}
 
//al cargar la página llamamos a la función paginate
$(window).bind("load", function(){
    paginate();
})