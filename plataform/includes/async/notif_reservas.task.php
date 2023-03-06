 #!/usr/bin/php -q
 <?
 	//include("/var/www/vhosts/miclubapp.com/httpdocs/admin/config.inc.php");
	include("/home/http/miclubapp/httpdocs/admin/config.inc.php");


  //las de aeroclub son 24 horas antes las demas 1 hora antes
  $sql_reservas = "SELECT * FROM ReservaGeneral WHERE IDClub = '36' and Fecha = ADDDATE(CURDATE(), INTERVAL 1 DAY)AND Notificado = 'N' AND Tipo <> 'Automatica'";
  envia_push_recordacion($sql_reservas);

  //Para los demas clubes 1 hora antes si tienen la hora bogota


  //Mexico
  date_default_timezone_set('America/Mexico_City');
  $HoraActual=date("H:i:s");
  $sql_reservas = "SELECT * FROM ReservaGeneral WHERE IDClub in (101,112) and Fecha = CURDATE() AND Hora > '".$HoraActual."' AND Hora <= AddTime('".$HoraActual."', '00:59:00') AND Notificado = 'N' AND Tipo <> 'Automatica' ";
  envia_push_recordacion($sql_reservas);

  //Uruguay
  date_default_timezone_set('America/Montevideo');
  $HoraActual=date("H:i:s");
  $sql_reservas = "SELECT * FROM ReservaGeneral WHERE IDClub in (125) and Fecha = CURDATE() AND Hora > '".$HoraActual."' AND Hora <= AddTime('".$HoraActual."', '00:59:00') AND Notificado = 'N' AND Tipo <> 'Automatica' ";
  envia_push_recordacion($sql_reservas);

  //Colombia
  date_default_timezone_set('America/Bogota');
  $HoraActual=date("H:i:s");
  $sql_reservas = "SELECT * FROM ReservaGeneral WHERE IDClub not in (101,112,125) and Fecha = CURDATE() AND Hora > '".$HoraActual."' AND Hora <= AddTime('".$HoraActual."', '00:59:00') AND Notificado = 'N' AND Tipo <> 'Automatica' ";



  function envia_push_recordacion($sql_reservas){

        $dbo =& SIMDB::get();
      	//$sql_reservas = "SELECT * FROM ReservaGeneral WHERE  IDReservaGeneral = '563400' ";
       	$qry_reservas = $dbo->query( $sql_reservas );
       	$reservas_encontrados = $dbo->rows( $qry_reservas );

       	while( $r_reservas = $dbo->fetchArray( $qry_reservas ) )
       	{
      		if((int)$r_reservas["IDSocioBeneficiario"]>0):
      			$IDSocio = $r_reservas["IDSocioBeneficiario"];
      		else:
      			$IDSocio = $r_reservas["IDSocio"];
      		endif;

       		//traer socio
       		 $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $IDSocio . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "'   ";
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

              if($r_reservas["IDClub"]!=36){
                $mensaje_antelacion=" Favor presentarse con 10 minutos de antelacion ";

              }
      			$message = "Recuerde que tiene una reserva de " . $nombre_servicio_personalizado . "  a las " . $r_reservas["Hora"] . $mensaje_antelacion;

      			$custom["tipo"] = "app";
      	   		$custom["idmodulo"] = (string)"2";
      	   		$custom["titulo"] = "Notificacion Club";

      			//$result_send = SIMUtil::sendAlerts($users, $message, $custom);

            $array_ios=array();
						$array_android=array();
						if($r_socios["Dispositivo"]=="iOS")
							$array_ios[]=$r_socios["Token"];
						elseif($r_socios["Dispositivo"]=="Android")
							$array_android[]=$r_socios["Token"];

						$resp=SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$r_reservas["IDClub"]);


      			//actualizar la reserva
      			$sql_update = "UPDATE ReservaGeneral SET Notificado = 'S' WHERE IDReservaGeneral = '" . $r_reservas["IDReservaGeneral"] . "' ";
      			$dbo->query( $sql_update );


      			//invitados encontrados
      			$sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$r_reservas["IDReservaGeneral"]."' and IDSocio >0";
      			$result_invitado = $dbo->query($sql_invitado);

      			while($row_invitado = $dbo->fetchArray($result_invitado)):

              $array_ios=array();
              $array_android=array();

      				$sql_socio_invitado = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $row_invitado["IDSocio"] . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "' and Token<>'' and Token <> '2byte'";
      				$qry_socio_invitado = $dbo->query( $sql_socio_invitado );
      				while( $r_socios_invitado = $dbo->fetchArray( $qry_socio_invitado ) )
      				{
                $IDClubInv=$r_reservas["IDClub"];
      					$users = array( array( "id" => $r_socios_invitado["IDSocio"],
      						"idclub"=>$r_reservas["IDClub"],
      						"registration_key"=>$r_socios_invitado["Token"] ,
      						"deviceType"=>$r_socios_invitado["Dispositivo"] )
      					);

                if($r_socios_invitado["Dispositivo"]=="iOS")
                  $array_ios[]=$r_socios_invitado["Token"];
                elseif($r_socios_invitado["Dispositivo"]=="Android")
                  $array_android[]=$r_socios_invitado["Token"];

      					//SIMUtil::sendAlerts($users, $message, $custom);
      				}
              if(count($array_android)>0 || count($array_ios)>0)
                $resp=SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$IDClubInv);


      			endwhile;


      		}//end while


       	}//end while
    }


    //****************************************************************************************************
    //Enviar correo a los servicios configurados con esta opción
    //****************************************************************************************************

    $sql_servicio="SELECT IDServicio,DiasNotificacion FROM Servicio WHERE NotificarSocioRecordacionReserva = 'S' and TextoRecordacionSocio <> '' and DiasNotificacion >0";
    $qry_servicio = $dbo->query( $sql_servicio );
    while( $r_servicio = $dbo->fetchArray( $qry_servicio ) ){
        $dias_anterioridad=$r_servicio["DiasNotificacion"];
        $sql_reservas = " SELECT *
               FROM ReservaGeneral RG
               WHERE Fecha = DATE_ADD(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL ".$dias_anterioridad." DAY)
               AND NotificadoMail = 'N'
               AND Tipo <> 'Automatica'
               AND IDServicio = '".$r_servicio["IDServicio"]."'";
               $qry_reservas = $dbo->query( $sql_reservas );
                $reservas_encontrados = $dbo->rows( $qry_reservas );

                while( $r_reservas = $dbo->fetchArray( $qry_reservas ) ){
                if((int)$r_reservas["IDSocioBeneficiario"]>0):
                  $IDSocio = $r_reservas["IDSocioBeneficiario"];
                else:
                  $IDSocio = $r_reservas["IDSocio"];
                endif;
                  //traer socio
                   $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $IDSocio . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "'   ";
                $qry_socios = $dbo->query( $sql_socios );
              //socios encontrados
                $socios_encontrados = $dbo->rows( $qry_socios );
                while( $r_socios = $dbo->fetchArray( $qry_socios ) ){
                  $solo_socio="S";
                  $recordacion="S";
                  SIMUtil::notificar_nueva_reserva($r_reservas["IDReservaGeneral"],$IDTipoReserva,$solo_socio,$recordacion);
                  //actualizar la reserva
                  $sql_update = "UPDATE ReservaGeneral SET NotificadoMail = 'S' WHERE IDReservaGeneral = '" . $r_reservas["IDReservaGeneral"] . "' ";
                  $dbo->query( $sql_update );
                }//end while
                }//end while
    }
    /*
    $sql_reservas = " SELECT *
             FROM ReservaGeneral RG, Servicio S
             WHERE RG.IDServicio = S.IDServicio
             AND S.NotificarSocioRecordacionReserva = 'S' AND S.TextoRecordacionSocio <> ''
             AND Fecha = DATE_ADD(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 3 DAY)
             AND NotificadoMail = 'N'
             AND Tipo <> 'Automatica'";
    */





?>
