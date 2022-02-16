<!--<html>	
<head>
<script src="../js/jquery.js"></script>
<script type="text/javascript">
	
	$('.inputs').keydown(function (e) {
     	if (e.which === 13) {
     		//$(this).next('.inputs').focus();
            $(this).closest('td').nextAll().eq(1).find('.inputs').focus()
     	}
     });
</script> 
</head>


</html>-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 

 
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head runat="server">
 
<script src="../js/jquery.js"></script>
 
<title></title>
 
<script type="text/javascript">
 
$('.inputs').keydown(function (e) {
     	if (e.which === 13) {
     		//$(this).next('.inputs').focus();
            $(this).closest('td').nextAll().eq(1).find('.inputs').focus()
     	}
     });
 
</script>
 
</head>
 
<body>
 
<form id="form1" runat="server">
 
<div>
 
<table class="half">
    <tr>
        <td class="tdocra c_white">Nome :</td>
        <td>
            <input class="inputs" name="ragsoc" type="text" class="text-sx" id="ragsoc" value=""/>
        </td>
        <td class="tdocra c_white b">Mnemo:</td>
        <td>
            <input class="inputs" name="mnemo" type="text" class="text-sx" id="mnemo" value=""/>
        </td>
        <td class="tdocra c_white">Partita IVA :</td>
        <td>
            <input class="inputs" name="piva" type="text" class="text-cx" id="piva" value=""/>
        </td>
    </tr>
</table>
<script>
function formatNumber (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

console.info(formatNumber(2665));      // 2,665
console.info(formatNumber(102665));    // 102,665
console.info(formatNumber(111102665)); // 111,102,665

function currencyFormatDE (num) {
    return num
       .toFixed(2) // always two decimal digits
       .replace(".", ",") // replace decimal point character with ,
       .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + " €" // use . as a separator
}

console.info(currencyFormatDE(1234567.89)); // output 1.234.567,89 €

</script>
 
</div>
 
</form>
 
</body>
 
</html>