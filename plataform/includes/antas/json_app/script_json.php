<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Documento sin título</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() { 

   $.post("json_tiendas.php",{latitude:"4.694421",longitude:"-74.049730"},function(data){
	
	
	alert(data.latitude_original);
	
	
	
	},'json');

})


</script>
</head>

<body>
</body>
</html>