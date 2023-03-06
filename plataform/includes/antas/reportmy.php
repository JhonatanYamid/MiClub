
<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	define( "DBHOST" , "10.187.88.45" );
	define( "DBNAME" , "tmp1_1" );
	define( "DBUSER" , "tmp1_1" );
	define( "DBPASS" , "GjHwX1*9Ikp4y0XDo" );
	$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	if ($mysqli->connect_errno) {
	    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	$FechaDesde="2020-04-01 00:00:00";
	$FechaHasta="2020-04-30 23:59:59";
	$sql="SELECT IDLogAcceso FROM Backup_LogAcceso  LIMIT 1";
	$sql2="SELECT IDLogAcceso
				FROM Backup_LogAcceso
				WHERE FechaTrCr >= '".$FechaInicio."' and FechaTrCr >= '".$FechaFin."'
				ORDER BY IDLogAcceso ASC
				LIMIT 100";
	if ($resultado = $mysqli->query($sql)) {
    while ($fila = $resultado->fetch_object()) {
			echo "SI";
				$array_invitacion[$fila->IDInvitacion]=$fila->IDInvitacion;
        print_r($fila);
    }
    $resultado->close();
}

/* cerrar la conexión */
$mysqli->close();
exit;




?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento sin título</title>
<!--<meta http-equiv="refresh" content="2" />-->
</head>

<body>

</body>
</html>
