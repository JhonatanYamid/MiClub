 #!/usr/bin/php -q
 <?
 	//include("/var/www/vhosts/miclubapp.com/httpdocs/admin/config.inc.php");
	include("/home/http/miclubapp/httpdocs/admin/config.inc.php");
	
	

 	//$token = "dfObOXc4Dk4:APA91bGvlDsWohHLpE_Oqtu7JtJcReppOBQiQqPEeMuHBCE9mqJeiiH2ELsItua3DUgQZxayE20uuuCK7PiYAq8LP9Ji9eyAVpAgE3ztQrG6G97LiNcOtbqSzLcL2mr1iCHIiS0GW_Jn";
	//traer socios a los que les interesa la noticia
	

 	//traer reservas de hoy que sean mayo
 	//$sql_reservas = "SELECT * FROM ReservaGeneral WHERE Fecha = CURDATE() AND Hora > CURTIME() AND Hora <= AddTime(CURTIME(), '00:10:00') AND Notificado = 'N'  ";
	$sql_reservas = "SELECT * FROM ReservaGeneral WHERE Fecha = CURDATE() AND Hora > CURTIME() AND Hora <= AddTime(CURTIME(), '02:10:00') AND Notificado = 'N' and IDReservaGeneral = '28829' ";
 	$qry_reservas = $dbo->query( $sql_reservas );

 	$reservas_encontrados = $dbo->rows( $qry_reservas );
 	

 	while( $r_reservas = $dbo->fetchArray( $qry_reservas ) )
 	{
 		//traer socio 
 		 $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $r_reservas["IDSocio"] . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "'   ";
		$qry_socios = $dbo->query( $sql_socios );


		//socios encontrados
		$socios_encontrados = $dbo->rows( $qry_socios );

		while( $r_socios = $dbo->fetchArray( $qry_socios ) )
		{
			
			$users = array( array( "id" => $r_socios["IDSocio"],
				"idclub"=>$r_reservas["IDClub"], 
				"registration_key"=>$r_socios["Token"] ,
				"deviceType"=>$r_socios["Dispositivo"] )

			);
			

			$serviciomaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_reservas["IDServicio"] . "' ");
			$nombreservicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $serviciomaestro . "' ");
			
			$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".$r_reservas["IDClub"]."' and IDServicioMaestro = '" . $serviciomaestro . "'" );
			if(empty($nombre_servicio_personalizado))
				$nombre_servicio_personalizado = $nombreservicio;
				
			if((int)$r_reservas["IDServicioTipoReserva"]>0):
				$nombre_servicio_personalizado .= " (".$dbo->getFields( "ServicioTipoReserva" , "Nombre" , "IDServicioTipoReserva = '".$r_reservas["IDServicioTipoReserva"]."'").")";
			endif;

			$message = "Recuerde que tiene una reserva de " . $nombre_servicio_personalizado . "  a las " . $r_reservas["Hora"] . " ";

			$custom["tipo"] = "app";
	   		$custom["idmodulo"] = (string)"2";
	   		$custom["titulo"] = "Notificacion Club";

			//SIMUtil::sendAlerts($users, $message, $custom);

			//actualizar la reserva
			$sql_update = "UPDATE ReservaGeneral SET Notificado = 'S' WHERE IDReservaGeneral = '" . $r_reservas["IDReservaGeneral"] . "' ";
			//$dbo->query( $sql_update );
			
			
			//invitados encontrados			
			$sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$r_reservas["IDReservaGeneral"]."' and IDSocio >0";	
			$result_invitado = $dbo->query($sql_invitado);
			while($row_invitado = $dbo->fetchArray($result_invitado)):
				echo $sql_socio_invitado = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $row_invitado["IDSocio"] . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "' and Token<>'' and Token <> '2byte'";
				$qry_socio_invitado = $dbo->query( $sql_socio_invitado );
				while( $r_socios_invitado = $dbo->fetchArray( $qry_socio_invitado ) )
				{
					$users = array( array( "id" => $r_socios_invitado["IDSocio"],
						"idclub"=>$r_reservas["IDClub"], 
						"registration_key"=>$r_socios_invitado["Token"] ,
						"deviceType"=>$r_socios_invitado["Dispositivo"] )
					);
					echo "<br><br>Enviado a invitados";
					print_r($users);					
					//SIMUtil::sendAlerts($users, $message, $custom);
				}
			endwhile;
			
			
			

		}//end while
		
		
		
		
		
		


 	}//end while


	


			

?>