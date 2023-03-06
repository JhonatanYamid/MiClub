 <?

	SIMReg::setFromStructure( array(
						"title" => "Acceso Invitados",
						"table" => "SocioInvitado",
						"key" => "IDSocioInvitado",
						"mod" => "SocioInvitado"
	) );


	$script = "salidainvitado";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

  $date = date("Y-m-d");
  //Incrementando 2 dias
  $mod_date = strtotime($date."+ 10 days");
  $FechaFinBusqueda= date("Y-m-d",$mod_date);


	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );


  if($_GET["action"]=="salidaespecial")
  {

    $array_adentro = array($_GET["qryString"]);
    foreach($array_adentro as $ident){

      if($_GET["modulo"]=="Socio")
	  {
        $datos_inv = $dbo->fetchAll( "Socio", " NumeroDocumento = '" . $ident . "' and IDClub = '".SIMUser::get("club")."' ", "array" );
        if($datos_inv["IDSocio"]>0)
		{
            $TipoInvitacion="Socio";
            $sql_inserta_historial_otro = "INSERT INTO LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$datos_inv["IDSocio"]."','".SIMUser::get("club")."', '".$TipoInvitacion."','S','Salida Especial', NOW(),NOW(),'".SIMUser::get("IDUsuario")."')";
            $dbo->query($sql_inserta_historial_otro);
            $mensaje_salida="Salida registrada con exito";
			$IDObservaciones = $datos_inv["IDSocio"];

        }
      }
      elseif($_GET["modulo"]=="Contratista" || $_GET["modulo"]=="SocioAutorizacion" || $_GET["modulo"]=="SocioInvitadoEspecial"){

        $datos_inv = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $ident . "' LIMIT 1", "array" );
        if($datos_inv["IDInvitado"]>0)
		{


          $array_tipo_inv=array("Contratista","SocioAutorizacion","SocioInvitadoEspecial");
          foreach($array_tipo_inv as $TipoInv){


            if($TipoInv=="Contratista" || $TipoInv=="SocioAutorizacion")
			{
              $TipoInvitacion="Contratista";
              $sql_registramov="SELECT IDSocioAutorizacion as IDSocioAutorizacion FROM SocioAutorizacion WHERE IDInvitado='".$datos_inv["IDInvitado"]."' and IDClub = '".SIMUser::get("club")."' Order by IDSocioAutorizacion DESC Limit 500";

            }
            elseif($TipoInv=="SocioInvitadoEspecial")
			{
              $TipoInvitacion="InvitadoAcceso";
              $sql_registramov="SELECT IDSocioInvitadoEspecial as IDSocioAutorizacion FROM SocioInvitadoEspecial WHERE IDInvitado='".$datos_inv["IDInvitado"]."' and IDClub = '".SIMUser::get("club")."' Order by IDSocioInvitadoEspecial DESC Limit 500";


            }

            $r_resgitramov=$dbo->query($sql_registramov);
            while($row_registramov=$dbo->fetchArray($r_resgitramov)){

              //Verifico si esta invitacion no tiene salida para realizarla
              if($TipoInv=="Contratista" || $TipoInv=="SocioAutorizacion")
			  {

                //$sql_acce="SELECT IDLogAcceso FROM LogAcceso WHERE IDInvitacion = '".$row_registramov["IDSocioAutorizacion"]."' and Salida = 'S' Order by IDLogAcceso DESC  ";
                $sql_acce="SELECT IDLogAcceso,Entrada,Salida FROM LogAcceso WHERE IDInvitacion = '".$row_registramov["IDSocioAutorizacion"]."' and IDClub = '".SIMUser::get("club")."' Order by IDLogAcceso DESC LIMIT 1 ";
              }
              elseif($TipoInv=="SocioInvitadoEspecial"){

                $sql_acce="SELECT IDLogAcceso,Entrada,Salida FROM LogAcceso WHERE IDInvitacion = '".$row_registramov["IDSocioInvitadoEspecial"]."' and IDClub = '".SIMUser::get("club")."' Order by IDLogAcceso DESC LIMIT 1";
              }

              //echo "<br>" . $sql_acce;
              $r_acce=$dbo->query($sql_acce);
              $datos_acce=$dbo->fetchArray($r_acce);
              $TotalAcce=$dbo->rows($r_acce);
              //echo " TOT:: " . $TotalAcce . " ENTRA " . $datos_acce["Entrada"];
              //if((int)$TotalAcce<=0){
              if($datos_acce["Entrada"]=="S"){
                $sql_inserta_historial_otro = "INSERT INTO LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioAutorizacion"]."','".SIMUser::get("club")."', '".$TipoInvitacion."','S','Salida Especial', NOW(),NOW(),'".SIMUser::get("IDUsuario")."')";
                $dbo->query($sql_inserta_historial_otro);
                $sql_inserta_historial_otro = "INSERT INTO LogAccesoVista (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioAutorizacion"]."','".SIMUser::get("club")."', '".$TipoInvitacion."','S','Salida Especial', NOW(),NOW(),'".SIMUser::get("IDUsuario")."')";
                $dbo->query($sql_inserta_historial_otro);
              }
              $mensaje_salida="Salida registrada con exito";
            }
          }

          }
      }

    }
    $_GET["action"]="search";
  }


	switch ( SIMNet::req( "action" ) ) {

		case "add" :
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "search" :
			//realizo busquedas
			//Guardo el Log de la busqueda
			//$sql_log_peticion =$dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('".SIMUser::get("club")."','".SIMUser::get("IDUsuario")."','".SIMNet::req("qryString")."','Consulta',NOW())");


			//BUSQUEDA INVITADOS ACCESOS
				$qryString = str_replace(".","",SIMNet::req("qryString"));
				$qryString = str_replace(",","",$qryString);
				if (ctype_digit($qryString)) {
					// si es solo numeros en un numero de documento
					$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".(int)$qryString."' and FechaFin < '".$FechaFinBusqueda."' and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
					$modo_busqueda = "DOCUMENTO";
				} else {
					//seguramente es una placa
					//Consulto en invitaciones accesos
					$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and  FechaFin < '".$FechaFinBusqueda."' and SIE.IDClub = '".SIMUser::get("club")."' Order By IDSocioInvitadoEspecial";
					$modo_busqueda = "PLACA";
				}


					$result_invitacion = $dbo->query($sql_invitacion);
					$total_resultados = $dbo->rows($result_invitacion);
					$datos_invitacion = $dbo->fetchArray($result_invitacion);
					$datos_invitacion["TipoInvitacion"] = "Invitado " . $datos_invitacion["TipoInvitacion"];
					$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
					$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );

					if($datos_invitacion["Ingreso"]=="N"){
						$accion_acceso = "ingreso";
						$label_accion_acceso = "Ingres&oacute;";
					}
					elseif($datos_invitacion["Salida"]=="N")	{
						$accion_acceso	= "salio";
						$label_accion_acceso	= "Sali&oacute;";
					}
					$modulo="SocioInvitadoEspecial";
					$id_registro = $datos_invitacion["IDSocioInvitadoEspecial"];

					$busca = "SELECT * FROM SocioInvitadoEspecial WHERE IDInvitado = ".$datos_invitacion["IDInvitado"]." ORDER BY FechaTrCr DESC LIMIT 1";
					$result = $dbo->query($busca);
					$datos = $dbo->fetchArray($result);
					$observacion = $datos['IDSocioInvitadoEspecial'];

					//Consulto grupo Familiar
					if($datos_invitacion["CabezaInvitacion"]=="S"):
						$sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '".$datos_invitacion["IDSocioInvitadoEspecial"]."'";
						$result_grupo = $dbo->query($sql_grupo);
					endif;
			//FIN BUSQUEDA INVITADOS ACCESOS

			//BUSQUEDA CONTRATISTA
				if($total_resultados<=0):
					if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".(int)$qryString."' and FechaFin < '".$FechaFinBusqueda."' and SA.IDClub = '".SIMUser::get("club")."'";
							$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa
						//Consulto en invitaciones accesos
            /*
            $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'
                                UNION Select SA.* From SocioAutorizacion SA Where Predio = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub = '".SIMUser::get("club")."'";
            */
            $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaFin < '".$FechaFinBusqueda."' and SA.IDClub = '".SIMUser::get("club")."'";
            $modo_busqueda = "PLACA";
					}



						$result_invitacion = $dbo->query($sql_invitacion);
						$total_resultados = $dbo->rows($result_invitacion);
						$datos_invitacion = $dbo->fetchArray($result_invitacion);

						if($datos_invitacion["Ingreso"]=="N"){
							$accion_acceso = "ingreso";
							$label_accion_acceso = "Ingres&oacute;";
						}
						elseif($datos_invitacion["Salida"]=="N")	{
							$accion_acceso	= "salio";
							$label_accion_acceso	= "Sali&oacute;";
						}

						$datos_invitacion["TipoInvitacion"] = "Contratista " . $datos_invitacion["TipoAutorizacion"];
						$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
						$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
						$modulo="SocioAutorizacion";
						$id_registro = $datos_invitacion["IDSocioAutorizacion"];

						$busca = "SELECT * FROM SocioAutorizacion WHERE IDInvitado = ".$datos_invitacion["IDInvitado"]." ORDER BY FechaTrCr DESC LIMIT 1";
						$result = $dbo->query($busca);
						$datos = $dbo->fetchArray($result);
						$observacion = $datos['IDSocioAutorizacion'];
				endif;
			//FIN BUSQUEDA CONTRATISTA

			//BUSQUEDA INVITADOS GENERAL
				if($total_resultados<=0):
					if (ctype_digit($qryString)) {
						// si es solo numeros en un numero de documento
						$sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '".(int)$qryString."' and FechaIngreso = '".date("Y-m-d")."' and IDClub = '".SIMUser::get("club")."'";
						$modo_busqueda = "DOCUMENTO";
						$result_invitacion = $dbo->query($sql_invitacion);
						$total_resultados = $dbo->rows($result_invitacion);
						$datos_invitacion = $dbo->fetchArray($result_invitacion);
						$datos_invitacion["TipoInvitacion"] = "Invitado ";
						$datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
						$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
						$datos_invitado["Nombre"]  = $datos_invitacion["Nombre"];
						$datos_invitado["NumeroDocumento"] = $datos_invitacion["NumeroDocumento"];
						$datos_invitado["IDTipoDocumento"] = "2"; // le pongo cc a todos ya que en el modulo no se solicita este dato
						$modulo="SocioInvitado";
						$id_registro = $datos_invitacion["IDSocioInvitado"];

						$busca = "SELECT * FROM SocioInvitado WHERE IDInvitado = ".$datos_invitacion["IDInvitado"]." ORDER BY FechaTrCr DESC LIMIT 1";
						$result = $dbo->query($busca);
						$datos = $dbo->fetchArray($result);
						$observacion = $datos['IDSocioInvitado'];

						if($datos_invitacion["Estado"]=="P"){
							$accion_acceso = "ingreso";
							$label_accion_acceso = "Ingres&oacute;";
						}
					}
				endif;
			//FIN BUSQUEDA CONTRATISTA


			//BUSQUEDA SOCIO
			if($total_resultados<=0):
					if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select * From Socio Where (NumeroDocumento = '".$qryString."' or Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' ) and IDClub = '".SIMUser::get("club")."'";
							$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa	o una accion
						//Consulto las placas de vehiculos de socios
						/*
						$sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."') and IDClub = '".SIMUser::get("club")."'
										  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'
										  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''";
						*/

            /*
						$sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' or NumeroDocumento = '".$qryString."' or Predio like '".$qryString."') and IDClub = '".SIMUser::get("club")."'
										   UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''
                      UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''  order by IDSocio ASC";
          */

                      $sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' or NumeroDocumento = '".$qryString."' or Predio like '".$qryString."') and IDClub = '".SIMUser::get("club")."'
          										   UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''
                                order by IDSocio ASC";



					}

						$result_invitacion = $dbo->query($sql_invitacion);
						$total_resultados = $dbo->rows($result_invitacion);
						$datos_invitacion = $dbo->fetchArray($result_invitacion);
						$datos_invitacion["TipoInvitacion"] = "Socio Club";
						$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
						$datos_invitado = $datos_socio;
						$modulo="Socio";
						$id_registro = $datos_invitacion["IDSocio"];
						$observacion =$datos_invitacion["IDSocio"];

						//Consulto grupo Familiar
						if (empty($datos_socio["AccionPadre"])): // Es Cabeza
							$nucleo_socio = 1;
							$condicion_nucleo = " and AccionPadre = '".$datos_socio["Accion"]."'";
							$datos_invitacion["CabezaInvitacion"]="S";
							$response_nucleo = array();
							$sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento FROM Socio WHERE IDClub = '".SIMUser::get("club")."' and IDSocio <> '".$datos_socio["IDSocio"]."' " . $condicion_nucleo;
							$result_grupo = $dbo->query($sql_grupo);
						endif;

				endif;
			//FIN BUSQUEDA SOCIO

			// Si no lo encuntra busco si tiene alguna autorización para otro dia

			if($total_resultados<=0):
				$datos_inv_prox = $dbo->fetchAll( "Invitado", " NumeroDocumento = '" . $qryString . "' ", "array" );
				if((int)$datos_inv_prox["IDInvitado"]>0):
					//Autorizaciones para otro dia
						$sql_auto_post = "Select * From SocioAutorizacion Where IDInvitado = '".$datos_inv_prox["IDInvitado"]."' and FechaInicio > '".date("Y-m-d")."'";
						$result_auto_post = $dbo->query($sql_auto_post);
						if($dbo->rows($result_auto_post)>0):
							$row_auto_post = $dbo->fetchArray($result_auto_post);
							$array_proxima_autorizacion [] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una autorizacion para el " .  $row_auto_post["FechaInicio"];
						endif;
					//Invitaciones para otro dia
						$sql_inv_post = "Select * From SocioInvitadoEspecial Where IDInvitado = '".$datos_inv_prox["IDInvitado"]."' and FechaInicio > '".date("Y-m-d")."'";
						$result_inv_post = $dbo->query($sql_inv_post);
						if($dbo->rows($result_inv_post)>0):
							$row_inv_post = $dbo->fetchArray($result_inv_post);
							$array_proxima_autorizacion [] = $datos_inv_prox["Nombre"] . " " . $datos_inv_prox["Apellido"] . " tiene una invitacion para el " .  $row_inv_post["FechaInicio"];
						endif;
				endif;
			endif;





			$view = "views/".$script."/list.php";
		break;


		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
