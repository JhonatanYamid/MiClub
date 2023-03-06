#!/usr/bin/php -q
<?php
	include("/home/http/miclubapp/httpdocs/admin/config.inc.php");
	
	$IDClub = 20;
	
	$server = '190.0.53.38';
	// Connect to MSSQL CASMPRESTRE MEDELLIN
	$link = mssql_connect($server, 'miclub', '#@miClub*#');
	if (!$link) {
		//die('Algo fue mal mientras se conectaba a MSSQL.');
	 		//mssql_get_last_message();
			die('MSSQL error: ' . mssql_get_last_message());
			var_dump($link);
	}
	
	echo "ok";
	exit;

	mssql_select_db('COMANDA', $link);
	$sql = mssql_query("SELECT * FROM [vapp_socios_activos]");	
	while ($row = mssql_fetch_array($sql)){
		//Consulto si el socio ya existe
		$sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '".$row["ident_cliente"]."' and IDClub = '".$IDClub."'";
		$result_socio = $dbo->query($sql_socio);
		//Estado Socio
		if($row["Estado"]=="A"):
			$estado_socio = 1;
		else:
			$estado_socio = 2;
		endif;
		
		
		if($row["descripcion_parentesco"]=="TITULAR"):
			$accion=$row["codigo_cliente"];
			$accionpadre="";
		else:			
			$accion=$row["codigo_cliente"];
			$accionpadre=$row["codigo_derecho"]."00";
		endif;
		
		$clave_socio = sha1(trim($row["ident_cliente"]));
		
		if($dbo->rows($result_socio)<=0):
			//Crear Socio			
			$inserta_socio = "Insert into Socio (IDClub, IDEstadoSocio, Accion, AccionPadre, Parentesco, Genero, Nombre, NumeroDocumento, Email, Clave, CorreoElectronico, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar)
							  Values ('".$IDClub."','".$estado_socio."','".$accion."','".$accionpadre."','".$codigo_parentesco."','".$row["sexo_cliente"]."','".$row["nombre_cliente"]."','".$row["ident_cliente"]."','".$row["ident_cliente"]."','".$clave_socio."','".$row["EMAIL"]."','Cron',NOW(),'100','100','S')";
			$dbo->query($inserta_socio);
		else:
			//Actualiza Socio			
			$actualiza_socio = "Update Socio set IDEstadoSocio = '".$estado_socio."', Accion = '".$accion."', AccionPadre='".$accionpadre."', Parentesco = '".$codigo_parentesco."', Genero = '".$row["sexo_cliente"]."', Nombre = '".$row["nombre_cliente"]."', NumeroDocumento = '".$row["ident_cliente"]."', CorreoElectronico = '".$row["EMAIL"]."', UsuarioTrEd = 'Cron', FechaTrEd = NOW()
									Where NumeroDocumento = '".$row["ident_cliente"]."' and IDClub = '".$IDClub."'";
			$dbo->query($actualiza_socio);						
		endif;
	}
	
	echo "<br>FIN Actualizacion";
	mssql_close($link);
	exit;

		
?>