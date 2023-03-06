<?php

//require( "../admin/config.inc.php" );
include("/var/www/vhosts/miclubapp.com/httpdocs/admin/config.inc.php");


// realizo copia de las reservas que se van a borrar
$sql_duplicados = "select COUNT(*) as Numero,Nombre, Telefono FROM Directorio WHERE IDClub = 10  GROUP BY Nombre ORDER BY Nombre";
$qry_duplicados = $dbo->query($sql_duplicados);
while( $r = $dbo->fetchArray( $qry_duplicados ) ) 
{
	if( $r["Numero"] > 1 )
	{
		echo $sql_delete = "DELETE FROM Directorio WHERE IDClub = 10 AND Nombre = '" . $r["Nombre"] . "' LIMIT 1 ";
		echo "<br>";
		$qry_delete = $dbo->query( $sql_delete );
	}

}



echo "Terminado";
?>