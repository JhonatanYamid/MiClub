 <?

	SIMReg::setFromStructure( array(
						"title" => "Acceso Invitados",
						"table" => "SocioInvitado",
						"key" => "IDSocioInvitado",
						"mod" => "SocioInvitado"
	) );


	$script = "accesoinvitadoaut";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );


	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );




	switch ( SIMNet::req( "action" ) ) {

		case "add" :
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "search" :

    if(!empty(SIMNet::req("qryString"))){
          $qryString = str_replace(".","",SIMNet::req("qryString"));
          $qryString = str_replace(",","",$qryString);


      			//realizo busquedas
      			//Guardo el Log de la busqueda
      			//$sql_log_peticion =$dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('".SIMUser::get("club")."','".SIMUser::get("IDUsuario")."','".SIMNet::req("qryString")."','Consulta',NOW())");

            //BUSQUEDA CONTRATISTA
      				if($total_resultados<=0):
      					if (ctype_digit($qryString)) {
      							// si es solo numeros en un numero de documento
      							$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '".(int)$qryString."' or I.NumeroDocumento = '".$qryString."' ) and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() Order by IDSocioAutorizacion DESC";
      							$modo_busqueda = "DOCUMENTO";
      					} else {
      						//seguramente es una placa
      						//Consulto en invitaciones accesos
      						$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
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
      						//$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
      						$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
      						$modulo="SocioAutorizacion";
      						$id_registro = $datos_invitacion["IDSocioAutorizacion"];
      				endif;
      			//FIN BUSQUEDA CONTRATISTA


      			//BUSQUEDA INVITADOS ACCESOS
            if($total_resultados<=0){

      				if (ctype_digit($qryString)) {
      					// si es solo numeros en un numero de documento
      					$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '".(int)$qryString."' or I.NumeroDocumento = '".$qryString."') and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() Order By IDSocioInvitadoEspecial";
      					$modo_busqueda = "DOCUMENTO";
      				} else {
      					//seguramente es una placa
      					//Consulto en invitaciones accesos
      					$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() Order By IDSocioInvitadoEspecial";
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

      					//Consulto grupo Familiar
      					if($datos_invitacion["CabezaInvitacion"]=="S"):
      						$sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '".$datos_invitacion["IDSocioInvitadoEspecial"]."'";
      						$result_grupo = $dbo->query($sql_grupo);
      					endif;
            }
      			//FIN BUSQUEDA INVITADOS ACCESOS



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
      							$sql_invitacion = "Select * From Socio Where (NumeroDocumento = '".$qryString."' or Accion = '".$qryString."' or NumeroDerecho = '".$qryString."') and IDClub = '".SIMUser::get("club")."'";
      							$modo_busqueda = "DOCUMENTO";
      					} else {
      						//seguramente es una placa	o una accion
      						//Consulto las placas de vehiculos de socios
      					$sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."' or NumeroDocumento = '".$qryString."') and IDClub = '".SIMUser::get("club")."'
      										  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".SIMUser::get("club")."'
      										  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".SIMUser::get("club")."'  and AccionPadre = ''";

      					}

      						$result_invitacion = $dbo->query($sql_invitacion);
      						$total_resultados = $dbo->rows($result_invitacion);
      						$datos_invitacion = $dbo->fetchArray($result_invitacion);
      						$datos_invitacion["TipoInvitacion"] = "Socio Club";
      						$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
      						$datos_invitado = $datos_socio;
      						$modulo="Socio";
      						$id_registro = $datos_invitacion["IDSocio"];

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
          }



			$view = "views/".$script."/list.php";
		break;

		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
