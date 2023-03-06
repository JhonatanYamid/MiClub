<?php
// El servidor con el formato: <computer>\<instance name> o 
// <server>,<port> cuando se use un número de puerto diferente del de defecto



//$conexión = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password);

/*
//$server = '190.0.53.38\COMANDA';
$server = '190.0.53.38';

// Connect to MSSQL CASMPRESTRE MEDELLIN
$link = mssql_connect($server, 'miclub', '#@miClub*#');
if (!$link) {
    die('Algo fue mal mientras se conectaba a MSSQL.');
}

mssql_select_db('COMANDA', $link);

//$sql = mssql_execute($link,"sp_consulta_pass '$pass_','$user_'");
$sql = mssql_query("SELECT TOP 100 * FROM [vapp_socios_activos]");


while ($row = mssql_fetch_array($sql)){
	print_r($row);	
	echo "<br><br>";
}

echo "FIN";
mssql_close($link);
exit;

*/

echo $server = '190.146.236.98:2210\sisclub384';

// Connect to MSSQL
$link = mssql_connect($server, 'nano', 'nano.2014');
if (!$link) {
    die('Algo fue mal mientras se conectaba a MSSQL.');
}

echo "Cabeza factura<br><br>";
//echo "SELECT TOP 30 * FROM v_ventas Where  nmro_accion = 116";
$sql = mssql_query("SELECT TOP 30 * FROM v_ventas Where    Order By fcha_rgstro Desc");
//$sql = mssql_query("SELECT * FROM v_ventas Where  nmro_fctra = '141860' and nmro_rgstro = '65224'");
//$sql = mssql_query("SELECT TOP 30 * FROM v_ventas Where  nmro_accion = 116 nmro_fctra > 0 and nmro_rgstro = '59552'");
//$sql = mssql_query("SELECT TOP 30 * FROM v_ventas");
	while ($row = mssql_fetch_array($sql)){
	print_r($row);	
	echo "<br><br>";
}

echo "<br><br>Detalle Venta<br><br>";

$sql2 = mssql_query("SELECT TOP 1 * FROM v_dtlle_venta");
while ($row2 = mssql_fetch_array($sql2)){
	echo "si";	
	print_r($row2);	
}

echo "<br><br>Forma Pago<br><br>";

$sql3 = mssql_query("SELECT TOP 1 * FROM  v_frm_pgo");
while ($row3 = mssql_fetch_array($sql3)){
	print_r($row3);	
}

echo "<br><br>Detalle Factura<br><br>";

$sql3 = mssql_query("SELECT * FROM v_ventas Where  nmro_fctra = '0' and nmro_rgstro = '8026' ");
while ($row3 = mssql_fetch_array($sql3)){
	print_r($row3);	
}


mssql_free_result($sql);
mssql_close($link);


?>