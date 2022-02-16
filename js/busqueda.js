//Getting value from "orden_venta_ajax.php".
function fill(Value) 
  {
  //Assigning value to "search" div in "search.php" file.
  $('#searchstring').val(Value);
  //Hiding "display" div in "search.php" file.
  $('#display').hide();
  }
$(document).ready(function() 
  {
  //On pressing a key on "Search box" in "orden_venta.php" file. This function will be called.
  $("#searchstring").keyup(function()
  //function onClick(e)
    {
    //Assigning search box value to javascript variable named as "name".
    var name = $('#searchstring').val();
    //Validating, if "name" is empty.
    if (name == "") 
      {
      //Assigning empty value to "display" div in "search.php" file.
      $("#display").html("");
      }
    //If name is not empty.
    else 
      {
      //AJAX is called.
      $.ajax({
        //AJAX type is "Post".
        type: "POST",
        //Data will be sent to "orden_venta_ajax.php".
        url: "../ajax/orden_venta_ajax.php",
		dataType : "json",
        //Data, that will be sent to "orden_venta_ajax.php".
        data: 
          {
          //Assigning value of "name" into "search" variable.
          searchstring: name
          },
        //If result found, this funtion will be called.
        success: function(html) 
        //success: function(result) 
          {
          //Assigning result to "display" div in "search.php" file.
          $("#display").html(html).show();
          //$("#table_data").html(result);
          }
        });
      };
    });
});